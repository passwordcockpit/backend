<?php

$config = include __DIR__ . '/config/config.php';
return [
    'name' => 'Passwordcockpit migrations',
    'migrations_namespace' => 'App\Migrations',
    'migrations_directory' =>
        $config['doctrine']['migrations']['migrations_directory'],
    'all_or_nothing' => true
];
