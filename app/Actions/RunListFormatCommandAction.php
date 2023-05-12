<?php

namespace App\Actions;

use Illuminate\Support\Facades\Process;

class RunListFormatCommandAction
{
    public function enter(string $videoId)
    {
        $command = sprintf(
            config('define.api.yt-dlp.default'),
            $videoId,
            config('define.api.yt-dlp.options.listFormat'),
            config('define.api.yt-dlp.options.outputType.json')
        );

        $process = Process::run($command);

        return $process->output();
    }
}
