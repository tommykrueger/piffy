<?php

namespace Piffy\Traits;

trait CryptographyTrait {

    private string $key = 'GEEK';

    public function encrypt(string $string): string
    {
// Store a string into the variable which
// need to be Encrypted

// Display the original string
        //echo "Original String: " . $string;

// Store the cipher method
        $ciphering = "AES-128-CTR";

// Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

// Non-NULL Initialization Vector for encryption
        $encryption_iv = '1234567891011121';

// Store the encryption key
        $encryption_key = DATA_SECRET;

// Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($string, $ciphering, $encryption_key, $options, $encryption_iv);

        return $encryption;
    }

    public function decrypt(string $string): string
    {
        $options = 0;

        $ciphering = "AES-128-CTR";

        // Display the encrypted string
        //echo "Encrypted String: " . $string . "\n";

// Non-NULL Initialization Vector for decryption
        $decryption_iv = '1234567891011121';

// Store the decryption key
        $decryption_key = DATA_SECRET;

// Use openssl_decrypt() function to decrypt the data
        $decryption = openssl_decrypt ($string, $ciphering, $decryption_key, $options, $decryption_iv);

// Display the decrypted string
        //echo "Decrypted String: " . $decryption;

        return $decryption;

    }

}