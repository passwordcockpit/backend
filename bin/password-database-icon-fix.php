<?php

/**
 * Script to update the field isSimulation,
 * set false to all isSimulation
 *
 */
chdir(__DIR__ . '/../');

require 'vendor/autoload.php';

$container = require 'config/container.php';

$entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);

$entityManager->getConnection()->beginTransaction();

$icons = [
    "key",
    "user",
    "music",
    "search",
    "envelope",
    "heart",
    "star",
    "film",
    "th-large",
    "th",
    "th-list",
    "check",
    "times",
    "power-off",
    "signal",
    "cog",
    "home",
    "file",
    "clock",
    "road",
    "download",
    "arrow-circle-down",
    "arrow-circle-up",
    "inbox",
    "play-circle",
    "list-alt",
    "lock",
    "flag",
    "headphones",
    "volume-up",
    "qrcode",
    "barcode",
    "tag",
    "book",
    "bookmark",
    "print"
];

try {
    $qb = $entityManager->createQueryBuilder();

    $qb->select('p')->from(\Password\Api\V1\Entity\Password::class, 'p');

    $passwords = $qb->getQuery()->getArrayResult();

    foreach ($passwords as $pass) {
        if (!in_array($pass['icon'], $icons)) {
            $qb->update(\Password\Api\V1\Entity\Password::class, 'p');
            $qb
                ->where('p.passwordId = ?1')
                ->set('p.icon', '?2')
                ->setParameters(array(1 => $pass['passwordId'], 2 => 'key'));
            $qb->getQuery()->execute();
        }
    }

    $entityManager->flush();
    $entityManager->getConnection()->commit();

    echo 'Updated passwords icons table.' . "\n";
} catch (Throwable $ex) {
    $entityManager->getConnection()->rollBack();
    echo 'Error: ' . $ex->getMessage() . "\n";
}
