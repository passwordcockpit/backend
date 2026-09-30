<?php

namespace App\Service;

/**
 * OpenSSL based replacement for the abandoned laminas-crypt package
 * configured with openssl, aes, gcm
 * compatible with BlockCipher
 */
class Crypt
{
    private const BLOCK_SIZE = 16;
    private const KEY_SIZE = 32;
    private const HASH = 'sha256';

    // BlockCipher (aes-256-gcm)
    private const CIPHER_ALGO = 'aes-256-gcm';
    private const KEY_ITERATIONS = 5000;
    private const TAG_SIZE = 16;


    public static function encrypt(string $data, string $key): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER_ALGO));
        $ciphertext = openssl_encrypt(
            self::pad((string) $data),
            self::CIPHER_ALGO,
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
        $ivLength = openssl_cipher_iv_length(self::CIPHER_ALGO);
        if ($decoded === false || strlen($decoded) <= self::TAG_SIZE + $ivLength) {
            return false;
        }

        $tag = substr($decoded, 0, self::TAG_SIZE);
        $iv = substr($decoded, self::TAG_SIZE, $ivLength);
        $ciphertext = substr($decoded, self::TAG_SIZE + $ivLength);
        $decrypted = openssl_decrypt(
            $ciphertext,
            self::CIPHER_ALGO,
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
