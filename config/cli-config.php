<?php

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Helper\HelperSet;

$container = require __DIR__ . '/container.php';
$em = $container->get(EntityManagerInterface::class);

$dbParams = include __DIR__ . '/autoload/migrations-db.php';

$connection = DriverManager::getConnection($dbParams);

return new HelperSet([
    'db' => new ConnectionHelper($connection),
    'em' => new EntityManagerHelper($em)
]);
