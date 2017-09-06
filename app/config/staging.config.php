<?php

$settings = [
    'database' => [
        'mongo' => [
            'host'     => '',
            'port'     => '',
            'username' => '',
            'password' => '',
            'dbname'   => '',
        ],
    ],
    'application' => [
        'repoDir'        => __DIR__ . '/../../app/repositories/',
        'servicesDir'    => __DIR__ . '/../../app/services/',
        'viewsDir'       => __DIR__ . '/../../app/views/',
        'modelsDir'      => __DIR__ . '/../../app/models/',
        'controllersDir' => __DIR__ . '/../../app/controllers/',
        'libraryDir'     => __DIR__ . '/../../app/library/',
        'baseUri'        => 'http://rpp-user-ms-api.dev',
    ],
    'import' => [
        'path' => '/data/import/user/',   
    ],
];