<?php

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;

$container = require __DIR__ . '/container.php';
$migrationsConfig = require __DIR__ . '/../migrations.php';

/** @var EntityManagerInterface $em */
$em = $container->get(EntityManagerInterface::class);

return DependencyFactory::fromEntityManager(
    new ConfigurationArray($migrationsConfig),
    new ExistingEntityManager($em)
);