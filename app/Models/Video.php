<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';
    protected $fillable = [
        'yt_id',
        'channel_id',
        'title'
    ];

    CONST YOUTUBE_API_TYPE_SEARCH_VIDEO  = 1;
    CONST YOUTUBE_API_TYPE_SEARCH_RELATED = 2;

    CONST TYPE_VIDEO = 'video';
    CONST TYPE_AUDIO = 'audio';
    CONST AUDIO_PRIORITY = 'audioPriority';
}
