<?php 

$encryptionKey = 'chave_de_criptografia_secreta';
    
// Função para encryptar dados
function encryptData($data, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($iv . $encryptedData);
}

//Função para decryptar dados
function decryptData($encryptedData, $key) {
    $decodedData = base64_decode($encryptedData);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($decodedData, 0, $ivLength);
    $data = substr($decodedData, $ivLength);
    return openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
}


?>