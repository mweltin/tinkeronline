<?php
$encryption = openssl_encrypt($simple_string, $ciphering, 
$encryption_key, $options, $encryption_iv);
 
$decryption=openssl_decrypt ($simple_string, $ciphering, 
$encryption_key, $options, $encryption_iv);