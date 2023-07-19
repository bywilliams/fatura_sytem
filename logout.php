<?php 

require_once("templates/header_iframe.php");

$userDao = new UserDao($conn, $BASE_URL);

if ($userDao) {
    $userDao->destroyToken();
}

echo "<script>window.top.location.href = 'index.php';</script>";