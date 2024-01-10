<?php 
require_once("globals.php");
require_once("connection/conn.php");
require_once("dao/UserDAO.php");
require_once("models/Banks.php");
require_once("dao/BanksDAO.php");
require_once("models/Message.php");

// variavel do sistema de notificações
$message = new Message($BASE_URL);

// Objeto DAO para bancos
$banksDao = new BanksDAO($conn, $BASE_URL);

// resgata dados do usuário
$userDao = new UserDAO($conn, $BASE_URL);
$userData = $userDao->verifyToken();


$type = filter_input(INPUT_POST, "type");

if ($type == "create") {

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $_SESSION['cod'] = $data['cod'];
    $_SESSION['name'] = $data['name'];

    if ($data['cod'] && $data['name']) {

        $bank = new Banks();
        $bank->cod = $data['cod'];
        $bank->name = $data['name'];

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
                } else {
                    // caso for PNG
                    $imageFile = imagecreatefrompng($image["tmp_name"]);
                }
            } else {
                $message->setMessage("Tipo inválido de imagem, insira imagens do tipo png ou jpg.", "error", "back");
            }


            //Gera nome para a imagem
            $imageName = $bank->imageGenerateName();

            // Cria a imageem no diretório
            imagejpeg($imageFile, "./assets/home/contas/" . $imageName, 100);

            $bank->logo = $imageName;
        }

        try {
            $banksDao->createBank($bank);
            $_SESSION['cod'] = "";
            $_SESSION['name'] = "";

        } catch (PDOException $e) {
            echo "Erro ao cadastrar banco, consulte o administrador do sistema";
            //echo "Error: ". $e->getMessage();
        }

        
    }else {
        $message->setMessage("Por favor preecha todos os campos!", "error", "back");
    }

}else if($type == "delete") {

    $id = filter_input(INPUT_POST, "id");


    try {
        $banksDao->deleteBank($id);
    } catch (PDOException $e) {
        echo "Erro ao deletar banco, consulte o administrador";
        //echo "Error: " . $e->getMessage();
    }

}


?>