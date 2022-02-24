<?php

$config = include __DIR__ . '/config/config.php';
return [
    'migrations_paths' => [
        'App\Migrations' => $config['doctrine']['migrations']['migrations_directory']
    ],
    'all_or_nothing' => true
];
