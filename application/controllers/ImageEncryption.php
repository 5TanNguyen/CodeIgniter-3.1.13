<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ImageEncryption
{

    public function __construct()
    {
    }

    public function encryptImage($sourceFile, $destFile, $encryptionKey)
    {
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($ivLength);
        $key = hash('sha256', $encryptionKey, true);

        $imageData = file_get_contents($sourceFile);
        $encryptedData = openssl_encrypt($imageData, 'AES-256-CBC', $key, 0, $iv);

        if ($encryptedData === false) {
            throw new Exception('Encryption failed: ' . openssl_error_string());
        }

        $result = $iv . $encryptedData;
        file_put_contents($destFile, $result);
    }

    public function decryptImage($encryptedFile, $encryptionKey)
    {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $key = hash('sha256', $encryptionKey, true);

        $encryptedData = file_get_contents($encryptedFile, false, stream_context_create($arrContextOptions));

        $iv = substr($encryptedData, 0, $ivLength);
        $ciphertext = substr($encryptedData, $ivLength);

        $decryptedData = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);

        if ($decryptedData === false) {
            throw new Exception('Decryption failed: ' . openssl_error_string());
        }

        return $decryptedData;
    }
}
