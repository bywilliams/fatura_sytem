<?php 

$encryptionKey = 'Hoc non pereo habebo fortior me';
    
// Função para encryptar dados
function encryptData($data, $key) {
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, );
    return $encryptedData;
}

//Função para decryptar dados
function decryptData($data, $key) {
    return openssl_decrypt($data, 'aes-256-cbc', $key, );
}


?>