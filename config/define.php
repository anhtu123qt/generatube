<?php

return [
    'api' => [
        'youtube' => [
            'search' => [
                'video'   => 'https://www.googleapis.com/youtube/v3/videos',
                'related' => 'https://www.googleapis.com/youtube/v3/search'
            ]
        ],
        'yt-dlp' => [
            'default' => 'yt-dlp "%s" %s %s', // yt-dlp [URL] [OPTION] [OPTION]
            'options'  => [
                'listFormat'     => '-F',
                'format'         => '-f %s',
                'filesizeApprox' => '-O "%(filesize,filesize_approx)s"',
                'outputType'     => [
                    'json' => '-j'
                ],
                'typeCombine' => '%s+%s'
            ],
            'formatId' => [
                'audio' => [
                    'ultraLow' => '599',
                    'low'       => '139',
                    'medium'    => '140',
                ],
                'video' => [
                    144 => [
                        '394',
                        '160',
                        'audioPriority' => 'ultraLow'
                    ],
                    240 => [
                        '395',
                        '133',
                        'audioPriority' => 'ultraLow'
                    ],
                    360 => [
                        '396',
                        '134',
                        'audioPriority' => 'low'
                    ],
                    480 => [
                        '397',
                        '135',
                        'audioPriority' => 'low'
                    ],
                    720 => [
                        '398',
                        '136',
                        'audioPriority' => 'medium'
                    ],
                    1080 => [
                        '399',
                        '137',
                        '299',
                        'audioPriority' => 'medium'
                    ],
                ],
            ],
            'thresholdPoint' => '{"i'
        ]
    ],
    'regex' => [
        'dataSize'    => '/\d+(\.\d+)?[kKmMgG][iIbB]*/',
        'number_only' => '/[^0-9]/'
    ]
];
