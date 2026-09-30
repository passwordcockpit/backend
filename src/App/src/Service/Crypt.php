<?php

namespace App\Service;

/**
 * OpenSSL based replacement for the abandoned laminas-crypt package
 * configured with openssl, aes, gcm
 * encrypt()/decrypt() compatible with BlockCipher
 * encryptFile()/decryptFile() compatible with FileCipher defaults
 */
class Crypt
{
    private const BLOCK_SIZE = 16;
    private const KEY_SIZE = 32;
    private const HASH = 'sha256';

    // BlockCipher (aes-256-gcm)
    private const CIPHER_ALGORITHM = 'aes-256-gcm';
    private const KEY_ITERATIONS = 5000;
    private const TAG_SIZE = 16;

    // FileCipher (aes-256-cbc + HMAC)
    private const FILE_CIPHER_ALGORITHM = 'aes-256-cbc';
    private const FILE_KEY_ITERATIONS = 10000;
    private const FILE_BUFFER_SIZE = 1048576; // 1MiB
    private const FILE_HMAC_SIZE = 64;
    private const FILE_HMAC_ALGORITHM = 'aes';
    

    public static function encrypt(string $data, string $key): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER_ALGORITHM));
        $ciphertext = openssl_encrypt(
            self::pad((string) $data),
            self::CIPHER_ALGORITHM,
            self::deriveKey($key, $iv, self::KEY_ITERATIONS, self::KEY_SIZE),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv,
            $tag,
            '',
            self::TAG_SIZE
        );

        return base64_encode($tag . $iv . $ciphertext);
    }

    public static function decrypt(string $data, string $key): string|false
    {
        $decoded = base64_decode((string) $data, true);
        $ivLength = openssl_cipher_iv_length(self::CIPHER_ALGORITHM);
        if ($decoded === false || strlen($decoded) <= self::TAG_SIZE + $ivLength) {
            return false;
        }

        $tag = substr($decoded, 0, self::TAG_SIZE);
        $iv = substr($decoded, self::TAG_SIZE, $ivLength);
        $ciphertext = substr($decoded, self::TAG_SIZE + $ivLength);
        $decrypted = openssl_decrypt(
            $ciphertext,
            self::CIPHER_ALGORITHM,
            self::deriveKey($key, $iv, self::KEY_ITERATIONS, self::KEY_SIZE),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv,
            $tag
        );

        return $decrypted === false ? false : self::unpad($decrypted);
    }

    private static function deriveKey(string $key, string $salt, int $iterations, int $length): string
    {
        return hash_pbkdf2(self::HASH, (string) $key, $salt, $iterations, $length, true);
    }

    public static function encryptFile(string $data, string $key): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(self::FILE_CIPHER_ALGORITHM));
        [$encryptionKey, $hmacKey] = self::deriveFileKeys($key, $iv);
        $ciphertext = openssl_encrypt(
            self::pad((string) $data),
            self::FILE_CIPHER_ALGORITHM,
            $encryptionKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        return self::fileHmac($hmacKey, $iv, $ciphertext, strlen((string) $data))
            . $iv
            . $ciphertext;
    }

    public static function decryptFile(string $data, string $key): string|false
    {
        $ivLength = openssl_cipher_iv_length(self::FILE_CIPHER_ALGORITHM);
        $data = (string) $data;
        if (strlen($data) < self::FILE_HMAC_SIZE + $ivLength + self::BLOCK_SIZE) {
            return false;
        }

        $hmac = substr($data, 0, self::FILE_HMAC_SIZE);
        $iv = substr($data, self::FILE_HMAC_SIZE, $ivLength);
        $ciphertext = substr($data, self::FILE_HMAC_SIZE + $ivLength);
        [$encryptionKey, $hmacKey] = self::deriveFileKeys($key, $iv);

        $decrypted = openssl_decrypt(
            $ciphertext,
            self::FILE_CIPHER_ALGORITHM,
            $encryptionKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );
        if ($decrypted === false) {
            return false;
        }
        $decrypted = self::unpad($decrypted);
        if ($decrypted === false) {
            return false;
        }

        $expectedHmac = self::fileHmac($hmacKey, $iv, $ciphertext, strlen($decrypted));
        if (!hash_equals($expectedHmac, $hmac)) {
            return false;
        }

        return $decrypted;
    }

    /**
     * FileCipher encrypts in chunks of FILE_BUFFER_SIZE plaintext bytes and
     * chains the HMAC over the ciphertext of each chunk
     */
    private static function fileHmac(string $hmacKey, string $iv, string $ciphertext, int $plaintextLength):string
    {
        // Every chunk except the last one is exactly FILE_BUFFER_SIZE bytes
        $fullChunks = max(0, intdiv($plaintextLength - 1, self::FILE_BUFFER_SIZE));
        $hmac = '';
        for ($i = 0; $i <= $fullChunks; $i++) {
            $chunk = $i < $fullChunks
                ? substr($ciphertext, $i * self::FILE_BUFFER_SIZE, self::FILE_BUFFER_SIZE)
                : substr($ciphertext, $i * self::FILE_BUFFER_SIZE);
            $hmac = hash_hmac(
                self::HASH,
                self::FILE_HMAC_ALGORITHM . $hmac . ($i === 0 ? $iv : '') . $chunk,
                $hmacKey
            );
        }

        return $hmac;
    }

    private static function deriveFileKeys(string $key, string $salt): array
    {
        $keys = self::deriveKey($key, $salt, self::FILE_KEY_ITERATIONS, self::KEY_SIZE * 2);

        return [substr($keys, 0, self::KEY_SIZE), substr($keys, self::KEY_SIZE)];
    }

    /**
     * PKCS#7 padding
     */
    private static function pad(string $data): string
    {
        $padding = self::BLOCK_SIZE - (strlen($data) % self::BLOCK_SIZE);

        return $data . str_repeat(chr($padding), $padding);
    }

    /**
     * PKCS#7 unpadding
     */
    private static function unpad(string $data): string|false
    {
        $length = strlen($data);
        $padding = $length > 0 ? ord($data[$length - 1]) : 0;
        if ($padding < 1 || $padding > self::BLOCK_SIZE || $padding > $length
            || substr($data, -$padding) !== str_repeat(chr($padding), $padding)
        ) {
            return false;
        }

        return substr($data, 0, $length - $padding);
    }
}
