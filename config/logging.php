<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

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
            'ignore_exceptions' => false,
        ],

        'agents' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . now()->toDateString() . '/agents.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
        ],

        'emails' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . now()->toDateString() . '/emails.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
        ],

        'tron_events' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . now()->toDateString() . '/tron_events.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
        ],

        'the_tron_events' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . now()->toDateString() . '/the_tron_events.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
        ],

        'transactions' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . now()->toDateString() . '/transactions.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
        ],

        'tronweb' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . now()->toDateString() . '/tronweb.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
        ],

        'schedule' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . now()->toDateString() . '/schedules.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . now()->toDateString() . '/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 30,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],

];
