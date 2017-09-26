<?php

$settings = [
    'database' => [
        'mongo' => [
            'host'     => '192.168.110.132',
            'port'     => '27017',
            'username' => 'mic_cms_ms',
            'password' => '1qaz2wsx',
            'dbname'   => 'mic_cms_ms',
        ],
    ],
    'application' => [
        'repoDir'        => __DIR__ . '/../../app/repositories/',
        'servicesDir'    => __DIR__ . '/../../app/services/',
        'viewsDir'       => __DIR__ . '/../../app/views/',
        'modelsDir'      => __DIR__ . '/../../app/models/',
        'controllersDir' => __DIR__ . '/../../app/controllers/',
        'libraryDir'     => __DIR__ . '/../../app/library/',
        'baseUri'        => 'http://mic-cms-api.dev',
    ],
    'import' => [
        'path' => '/data/import/user/',   
    ],
    'curl_api' => [
        'cms'   => 'http://staging-mic-cms-ms-api.eggdigital.com:8107/',
    ],
];