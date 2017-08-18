<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);


/**
 * simple method to encrypt or decrypt a plain text string
 * initialization vector(IV) has to be the same when encrypting and decrypting
 * PHP 5.4.9 ( check your PHP version for function definition changes )
 *
 * this is a beginners template for simple encryption decryption
 * before using this in production environments, please read about encryption
 * use at your own risk
 *
 * @param string $action: can be 'encrypt' or 'decrypt'
 * @param string $string: string to encrypt or decrypt
 *
 * @return string
 */
include('/home/rconfig/config/config.inc.php');

function encrypt_decrypt($action, $string) {
    if(KEY == '' || empty(KEY)){
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = KEY;
        $secret_iv = 'This is my secret iv';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        $output = false;
    }
    return $output;
}

$plain_txt = "This is my plain text";
echo "Plain Text = $plain_txt\n";

$encrypted_txt = encrypt_decrypt('encrypt', $plain_txt);
var_dump($encrypted_txt);
echo "Encrypted Text = $encrypted_txt\n";

$decrypted_txt = encrypt_decrypt('decrypt', $encrypted_txt);
echo "Decrypted Text = $decrypted_txt\n";

if( $plain_txt === $decrypted_txt ) echo "SUCCESS";
else echo "FAILED";

echo "\n";



   $devicePassword = encrypt_decrypt('encrypt', $_POST['devicePassword']);
            if($devicePassword === false) { 
                $devicePassword = $_POST['devicePassword'];
            }