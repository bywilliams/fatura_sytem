<?php
require_once("globals.php");
 require_once("connection/conn.php");
 require_once("models/Message.php");
require_once("dao/UserDAO.php");
require_once("dao/PopupDAO.php");

$popupDao = new PopupDAO($conn, $BASE_URL);
$message = new Message($BASE_URL);

// resgata dados do usuário
$userDao = new UserDAO($conn, $BASE_URL);
$userData = $userDao->verifyToken();

$no_show_popup = filter_input(INPUT_POST, "no_show_popup");

if (!empty($no_show_popup)) {
        $popupDao->updatePopupUser($no_show_popup, $userData->id); 
  
}

?>