<?php

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;

$container = require __DIR__ . '/container.php';
$em = $container->get(EntityManagerInterface::class);

return new HelperSet([
    'em' => new EntityManagerHelper($em)
]);
