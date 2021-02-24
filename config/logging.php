<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
        ],

        'prod_stack' => [
            'driver' => 'stack',
            'channels' => ['prod_slack'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'prod_slack' => [
            'channel' => 'cluttons_feed',
            'driver' => 'slack',
            'url' => 'https://hooks.slack.com/services/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',
            'username' => 'Cluttons Sales',
            'emoji' => ':boom:',
            'level' => 'error',
        ],

        'dev_slack' => [
            'channel' => 'cluttons_feed',
            'driver' => 'slack',
            'url' => 'https://hooks.slack.com/services/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',
            'username' => 'Cluttons Dev',
            'emoji' => ':boom:',
            'level' => 'error',
        ],

        'staging_slack' => [
            'channel' => 'cluttons_feed',
            'driver' => 'slack',
            'url' => 'https://hooks.slack.com/services/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',
            'username' => 'Cluttons Staging',
            'emoji' => ':boom:',
            'level' => 'error',
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
    ],

];