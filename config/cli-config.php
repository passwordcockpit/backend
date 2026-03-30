<?php

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;

$container = require __DIR__ . '/container.php';

/** @var EntityManagerInterface $em */
$em = $container->get(EntityManagerInterface::class);

return new class($em) implements EntityManagerProvider {
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getDefaultEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
};