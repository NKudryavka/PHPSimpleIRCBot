<?php
$config = [
    'debug' => false,
    'timezone' => 'Asia/Tokyo',
    'servers' => [
        'test' => [
            'host' => 'irc.himitsukichi.com',
            'port' => 6667,
            'nick' => 'test',
            'loginname' => 'test',
            'realname' => 'test',
            'encoding' => 'ISO-2022-JP',
            'channels' => ['#your_own_test_channel'],
            'modules' => [
                'repeat' => ['prefix' => 'repeat'],
            ],
        ],
    ],
];