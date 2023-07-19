<?php 

$localhost = 'localhost';
$user = 'root';
$db = "fatura";
$psw = "";

$conn = new PDO("mysql:host=$localhost;dbname=$db", $user, $psw);

$conn->exec("SET NAMES 'utf8'; SET character_set_connection=utf8; SET character_set_client=utf8; SET character_set_results=utf8;");

date_default_timezone_set('America/Sao_Paulo');

// Habilitar erros PDO
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
