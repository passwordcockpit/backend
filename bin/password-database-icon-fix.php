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
    "user-secret",
    "user-graduate",
    "users",
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
    "cloud",
    "road",
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
    "print",
    "camera",
    "wifi",
    "address-book",
    "anchor",
    "asterisk",
    "ban",
    "bed",
    "bell",
    "bolt",
    "bomb",
    "bone",
    "bong",
    "briefcase-medical",
    "briefcase",
    "bug",
    "building",
    "bus",
    "calculator",
    "calendar",
    "campground",
    "candy-cane",
    "capsules",
    "car",
    "certificate",
    "coins",
    "credit-card",
    "desktop",
    "download",
    "exclamation",
    "fingerprint",
    "fish",
    "flask",
    "gamepad",
    "gift",
    "hammer",
    "hdd",
    "image",
    "laptop",
    "map-marker",
    "money-bill",
    "mobile-alt",
    "paperclip",
    "pastafarianism",
    "poo",
    "radiation",
    "restroom",
    "rss",
    "satellite",
    "save",
    "sd-card",
    "server",
    "share-alt",
    "shopping-cart",
    "sim-card",
    "smile",
    "space-shuttle",
    "thumbs-up",
    "thumbs-down",
    "toilet-paper",
    "tools",
    "tree",
    "tshirt"
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
