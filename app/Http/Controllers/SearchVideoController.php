<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Jobs\StoreVideoInformationJob;
use App\Models\Thumbnail;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
                        'id'         => $this->getValue($result->id),
                        'title'      => $this->getValue($result->snippet->title),
                        'channel_id' => $this->getValue($result->snippet->channelId),
                        'thumbnail'  => $this->getValue($result->snippet->thumbnails->standard)
                    ]
                );
            case Video::YOUTUBE_API_TYPE_SEARCH_RELATED:
                $result = $this->callAPI(
                    config('define.api.youtube.search.related'),
                    [
                        'relatedToVideoId' => $videoId,
                        'type'             => 'video',
                        'maxResults'       => 20
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
            'key'  => config('app.youtube_api_key')
        ];
        try {
            $response = Http::get($url, array_merge($defaultParams, $params));

            return $response->object();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return [];
        }
    }
}
