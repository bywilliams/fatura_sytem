<?php

require_once("globals.php");
require_once("connection/conn.php");
require_once("utils/check_password.php");
require_once("dao/UserDAO.php");
require_once("models/Message.php");

$userDao = new UserDAO($conn, $BASE_URL);

$message = new Message($BASE_URL);


$type = filter_input(INPUT_POST, "type");

if ($type == "update") {

    $name = filter_input(INPUT_POST, "name");
    $lastname = filter_input(INPUT_POST, "lastname");
    $email = filter_input(INPUT_POST, "email");
    $bio = filter_input(INPUT_POST, "bio");

    $user = new  User();
    $userData = $userDao->verifyToken();

    // Preencher os dados do usuário
    $userData->name = $name;
    $userData->lastname = $lastname;
    $userData->email = $email;
    $userData->bio = $bio;

    // Upload da imagem
    if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

        $image = $_FILES["image"];

        //tipos permitidos 
        $imagesType = ["image/jpg", "image/jpeg", "image/png"];
        $jpgArray = ["image/jpg", "image/jpeg"];

        // Checa tipo da imagem
        if (in_array($image["type"], $imagesType)) {
            
            if (in_array($image["type"], $jpgArray)) {
                $imageFile = imagecreatefromjpeg($image["tmp_name"]);
            }else {
                // caso for PNG
                $imageFile = imagecreatefrompng($image["tmp_name"]);
            }

        }else {
            $message->setMessage("Tipo inválido de imagem, insira imagens do tipo png ou jpg.", "error", "back");
        }


        //Gera nome para a imagem
        $imageName = $user->imageGenerateName();

        // Cria a imageem no diretório
        imagejpeg($imageFile, "./assets/home/avatar/" . $imageName, 100);

        $userData->image = $imageName;
        
    }

    // por fim faz o update dos dados
    $userDao->update($userData);


}else if($type == "changePassword"){
    
    $password = filter_input(INPUT_POST, "password");
    $confirmPassword = filter_input(INPUT_POST, "confirmPassword");

    $userData = $userDao->verifyToken();
    $id = $userData->id;

    if ($password) {
        
        if ($password == $confirmPassword) {

            if (password_strength($password)) {

                $passwordChanged = password_hash($password, PASSWORD_DEFAULT);

                $userData->password = $passwordChanged;
                $userData->id = $id;

                $userDao->changePassword($userData);
                
            }else {
                $message->setMessage("A senha deve possuir ao menos 8 caracteres, sendo pelo menos 1 letra maiúscula, 1 minúscula, 1 número e 1 simbolo.", "error", "back");
            }
            
        }else {
            $message->setMessage("As senhas não são iguais.", "error", "back");
        }

    }else {

        $message->setMessage("Prencha o campo senha e confirmação de senha para poder altera-la", "error", "back");
        
    }

}
