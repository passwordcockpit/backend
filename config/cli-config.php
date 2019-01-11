<?php

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Helper\HelperSet;

$container = require __DIR__ . '/container.php';
$em = $container->get(EntityManagerInterface::class);


return new HelperSet([
    'db' => new ConnectionHelper($em->getConnection()),
    'em' => new EntityManagerHelper($em),
]);