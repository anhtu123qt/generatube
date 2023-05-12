<?php

namespace App\Http\Controllers;

use App\Actions\RunListFormatCommandAction;
use App\Http\Resources\VideoResource;
use App\Jobs\StoreVideoInformationJob;
use App\Models\Thumbnail;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class SearchVideoController extends BaseApiController
{
    /**
     * Handle Search Youtube Video
     */
    public function __invoke(Request $request)
    {
        $result = [];
        $videoId = $this->getValue($request->id);
        $video[] = $this->handleRouteYouTubeAPI(Video::YOUTUBE_API_TYPE_SEARCH_VIDEO, $videoId);
        if (empty($video)) {
            return $this->response(400);
        }
        $relatedVideos = $this->handleRouteYouTubeAPI(Video::YOUTUBE_API_TYPE_SEARCH_RELATED, $videoId);
        $result = array_merge($video, $relatedVideos);

        StoreVideoInformationJob::dispatch($result)->delay(60);

        $result[0]['fileSize'] = $this->getFileSizeByResolution($videoId);

        return $this->response(200, $result);
    }

    /**
     * Handle Route Youtube API
     *
     * @param int $typeAPI
     * @param string|int $videoId
     * @return array|mixed|void
     */
    private function handleRouteYouTubeAPI(int $typeAPI, string|int $videoId)
    {
        switch ($typeAPI) {
            case Video::YOUTUBE_API_TYPE_SEARCH_VIDEO:
                $result = optional($this->callAPI(
                    config('define.api.youtube.search.video'),
                    [
                        'id' => $videoId
                    ]
                ))->items[0];

                return $this->getValue(
                    [
                        'id' => $this->getValue($result->id),
                        'title' => $this->getValue($result->snippet->title),
                        'channel_id' => $this->getValue($result->snippet->channelId),
                        'thumbnail' => $this->getValue($result->snippet->thumbnails->standard)
                    ]
                );
            case Video::YOUTUBE_API_TYPE_SEARCH_RELATED:
                $result = $this->callAPI(
                    config('define.api.youtube.search.related'),
                    [
                        'relatedToVideoId' => $videoId,
                        'type' => 'video',
                        'maxResults' => 20
                    ]
                );

                $videos = [];
                foreach ($result->items as $item) {
                    $videos[] = [
                        'id' => $this->getValue($item->id->videoId),
                        'title' => $this->getValue($item->snippet->title),
                        'channel_id' => $this->getValue($item->snippet->channelId),
                        'thumbnail' => $this->getValue($item->snippet->thumbnails->standard)
                    ];
                }

                return $videos;
            default:
                break;
        }
    }

    /**
     * Handle call API By Type
     ** @param string $url
     * @param array $params
     * @return object|array
     */
    private function callAPI(string $url, array $params): object|array
    {
        $defaultParams = [
            'part' => 'snippet',
            'key' => config('app.youtube_api_key')
        ];
        try {
            $response = Http::get($url, array_merge($defaultParams, $params));

            return $response->object();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return [];
        }
    }

    private function getFileSizeByResolution(string $videoId): array
    {
//        $videoId = '5QH7ZL8z2k0';
        $result = [];
        $rawInfo = app(RunListFormatCommandAction::class)->enter($videoId);
        $formatType = $this->getValue(config('define.api.yt-dlp.formatId'));
        $fileSize = $this->getFileSizeByRawType($rawInfo, $formatType);

        foreach ($formatType[Video::TYPE_VIDEO] as $resolution => $videoTypes) {
            foreach ($videoTypes as $videoType) {
                if (preg_match(config('define.regex.number_only'), $videoType)) break;
                if (isset($fileSize[$videoType])) {
                    $audioType = $formatType[Video::TYPE_AUDIO][$videoTypes[Video::AUDIO_PRIORITY]];
                    $result[$resolution]['typeCombine'] = sprintf(config('define.api.yt-dlp.options.typeCombine'), $videoType, $audioType);
                    $result[$resolution]['size'] = round($fileSize[$videoType] + $fileSize[$audioType], 2);

                    break;
                }
            }
        }

        return $result;
    }

    private function getFileSizeByRawType(string $rawInfo, array $formatType): array
    {
        $result = [];
        $pos = strpos($rawInfo, "\n");
        while ($pos > 0) {
            $rawInfo = substr($rawInfo, strpos($rawInfo, "\n") + 1, strlen($rawInfo) - strpos($rawInfo, "\n"));
            $rawType = substr($rawInfo, 0, 3);

            if ($rawType === config('define.api.yt-dlp.thresholdPoint')) break;

            if (in_array(trim($rawType), Arr::flatten($formatType))) {
                $fileSize = '';
                preg_match(config('define.regex.dataSize'), $rawInfo, $fileSize);
                $result[$rawType] = $this->convertToMB($fileSize[0]);
            }
        }

        return $result;
    }

    private function convertToMB($fileSize): float|int
    {
        if (str_contains($fileSize, 'KiB')) return floatval(str_replace('KiB', '', $fileSize))/pow(10,3);

        return floatval(str_replace('MiB', '', $fileSize));
    }
}
