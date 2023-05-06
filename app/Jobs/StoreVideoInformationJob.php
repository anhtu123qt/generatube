<?php

namespace App\Jobs;

use App\Models\Thumbnail;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreVideoInformationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected array $data)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->data as $item) {
            $video = $this->storeVideo($item['id'], $item['title'], $item['channel_id']);

            $this->storeThumbnail($video->id, $item['thumbnail']);
        }
    }

    private function storeVideo($youTubeId, $title, $channelId)
    {
        return Video::updateOrCreate([
            'yt_id'      => $youTubeId,
            'channel_id' => $channelId,
            'title'      => $title
        ]);
    }

    private function storeThumbnail($videoId, $thumbnail)
    {
        return Thumbnail::updateOrCreate([
            'video_id' => $videoId,
            'url'      => $thumbnail->url,
            'width'    => $thumbnail->width,
            'height'   => $thumbnail->height
        ]);
    }
}
