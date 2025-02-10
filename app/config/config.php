<?php

    $env = parse_ini_file(__DIR__ . '/../../.env');

    return [
        'db' => [
            'host' => $env['DB_HOST'],
            'name' => $env['DB_NAME'],
            'user' => $env['DB_USER'],
            'password' => $env['DB_PASSWORD'],
        ]
    ];