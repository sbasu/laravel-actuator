<?php

declare(strict_types=1);

return [
    'path' => env('ACTUATOR_PATH', 'actuator'),

    'middleware' => ['api'],

    'indicators' => [
        'database'   => true,
        'disk_space' => true,
        'cache'      => true,
        'queue'      => true,
    ],

    'metrics' => [
        'enabled'     => true,
        'sample_rate' => 1.0,
    ],

    'show_details' => true,

    'show_env' => false,

    'log_access' => false,

    'max_request_history' => 100,
];
