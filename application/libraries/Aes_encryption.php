<?php
class AES_Encryption
{
    private $encryptionKey = "MAKV2SPBNI99212";

    // Enkripsi
    // public function encrypt($clearText) {
    //     $key = hash('sha256', $this->encryptionKey, true);
    //     $iv = substr(hash('sha256', "IvEncryptionKey", true), 0, 16);

    //     $encrypted = openssl_encrypt($clearText, 'AES-256-CBC', $key, 0, $iv);
    //     return base64_encode($encrypted);
    // }

    function encrypt($plainText)
    {
        $encryptionKey = "MAKV2SPBNI99212"; // Kunci enkripsi
        $salt = hex2bin('4976616e204d65647665646576'); // Konversi byte array yang sama seperti C#

        // **PBKDF2 untuk mendapatkan Key dan IV**
        $key_iv = hash_pbkdf2("sha1", $encryptionKey, $salt, 1000, 48, true);
        $key = substr($key_iv, 0, 32);  // 32 byte key untuk AES-256
        $iv = substr($key_iv, 32, 16);  // 16 byte IV

        // **Konversi plain text ke UTF-16LE seperti di C#**
        $plainText = mb_convert_encoding($plainText, 'UTF-16LE', 'UTF-8');

        // **Enkripsi menggunakan AES-256-CBC**
        $encrypted = openssl_encrypt($plainText, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($encrypted); // Encode hasil enkripsi ke Base64 agar mirip dengan C#
    }

    // Dekripsi
    function decrypt($cipherText)
    {
        $encryptionKey = "MAKV2SPBNI99212"; // Kunci enkripsi yang sama
        $salt = hex2bin('4976616e204d65647665646576'); // Sama seperti pada encrypt

        // **PBKDF2 untuk mendapatkan Key dan IV**
        $key_iv = hash_pbkdf2("sha1", $encryptionKey, $salt, 1000, 48, true);
        $key = substr($key_iv, 0, 32);  // 32 byte key untuk AES-256
        $iv = substr($key_iv, 32, 16);  // 16 byte IV

        // **Decode Base64**
        $cipherText = base64_decode($cipherText);

        // **Dekripsi menggunakan AES-256-CBC**
        $decrypted = openssl_decrypt($cipherText, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);

        // **Konversi hasil dari UTF-16LE ke UTF-8**
        return mb_convert_encoding($decrypted, 'UTF-8', 'UTF-16LE');
    }
}
