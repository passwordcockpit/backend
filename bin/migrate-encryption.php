<?php

chdir(dirname(__DIR__));

require 'vendor/autoload.php';
$container = require 'config/container.php';

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Crypt\FileCipher;
use Laminas\Crypt\BlockCipher;
use Password\Api\V1\Entity\Password;
use File\Api\V1\Entity\File;

$entityManager = $container->get(EntityManagerInterface::class);
$blockCipher = BlockCipher::factory(
                $container->get("config")['block_cipher']['encryption_library'],
                $container->get("config")['block_cipher']['algorithms']
            );
$fileCipher = new FileCipher();  
$encryptionKey = $container->get('config')['block_cipher']['key'];
$uploadPath = $container->get('config')['upload_config']['upload_path'];

// Encryption setup
$key = hash('sha256', $encryptionKey, true);
$ivLength = openssl_cipher_iv_length('aes-256-cbc');
$iv = random_bytes($ivLength);

$migratedPasswords = [];
$migratedFiles = [];

// Decrypt and re-encrypt passwords
echo "******PASSWORDS******\n";
$passwords = $entityManager->getRepository(Password::class)->findAll();
foreach ($passwords as $password) {
    $encrypted = $password->getPassword();

    if($encrypted) {
        // Decrypt
        $blockCipher->setKey($encryptionKey);
        $decrypted = $blockCipher->decrypt($encrypted);

        if($decrypted) {
            // Encrypt
            $ciphertext = openssl_encrypt($decrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
            $newEncrypted = base64_encode($iv . $ciphertext);

            if($newEncrypted) {
                $password->setPassword($newEncrypted);
                $migratedPasswords[] = $password->getPasswordId();
            }
        }
    }
}

$entityManager->flush();

foreach($migratedPasswords as $migratedPassword) {
    echo "ID: {$migratedPassword} - Migrated\n";
}

// Decrypt and re-encrypt files
echo "******FILES******\n";
$files = $entityManager->getRepository(File::class)->findAll();
foreach ($files as $file) {
    // Retrieve file from path
    $path = $uploadPath . DIRECTORY_SEPARATOR .$file->getFilename();
    if (!file_exists($path)) {
        if (file_exists($path.'.crypted')) {
            // To be retrocompatible search for different path when file was named with the suffix `.crypted`
            $path = $path.'.crypted';
        } else {
            continue;
        }
    }

    // Decrypt
    $fileCipher->setKey($encryptionKey);
    $tempPath = 'tmp/'.md5($file->getFilename() . time() . random_int(0, mt_getrandmax()));
    $decrypted = $fileCipher->decrypt($path, $tempPath);
    if($decrypted) {
        unlink($path);
        // Encrypt
        $encryptedData = file_get_contents($tempPath);
        $ciphertext = openssl_encrypt($encryptedData, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $newEncrypted = base64_encode($iv . $ciphertext);
        if ($newEncrypted && file_put_contents($path, $newEncrypted) !== false) {
            unlink($tempPath);
            echo "ID: {$file->getFileId()} - Migrated\n";
        }
    }
}