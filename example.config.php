<?php
return [
    'driver' => 'remote',

    'remote' => [
        'host' => 'example.com',
        //'port' => 3310// optional
    ],
    'local' => [
        'path' => '/var/run/clamav/clamd.ctl'
    ]
];