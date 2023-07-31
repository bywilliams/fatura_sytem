<?php
require_once("templates/header.php");
require_once("globals.php");
require_once("connection/conn.php");
require_once("utils/check_password.php");
require_once("models/Message.php");
require_once("traits/generates.php");
require_once("dao/UserDAO.php");


$userDao = new UserDAO($conn, $BASE_URL);

$message = new Message($BASE_URL);


$type = filter_input(INPUT_POST, "type");

if ($type == "update") {

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $user = new  User();
    $userData = $userDao->verifyToken();

    // Preencher os dados do usuário
    $userData->name = $data['name'];
    $userData->lastname = $data['lastname'];
    $userData->email = $data['email'];
    $userData->bio = $data['bio'];

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


}else if($type == "edit_user_admin") {

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $userData = array(
        'user_id' => $data['user_id'],
        'sits_user_id' => $data['sits_user_id'],
        'levels_access_id' => $data['levels_access_id']
    );
    
    // New element
    //$arr['zero'] = 0;

    // Checa se o input de inserir um novo password possui valor 
    if($data['new_password']) {

        if($data['password']) {
    
            if($data['password'] == $data['confirmPassword']) {
    
                if (password_strength($data['password'])) { 
    
                    $new_password = password_hash($data['password'], PASSWORD_DEFAULT);
                    $userData['password'] = $new_password;
    
                }else {
                    
                    $message->setMessage("A senha deve possuir ao menos 8 caracteres, sendo pelo menos 1 letra maiúscula, 1 minúscula, 1 número e 1 simbolo.", "error", "back");
                }
                
            }else {
                $message->setMessage("As senhas não são iguais.", "error", "back");
            }
    
        }else {
            $message->setMessage("Prencha o campo senha e confirmação de senha para poder altera-la", "error", "back");
        }

    }else {
        // Se o input de nova senha não possuir valor  mantem a mesma senha já cadastrada
        $userData['password'] = $data['password'];
    }

    // por fim faz o update dos dados
    try{
        $userDao->adminUserUpdate($userData);
    }catch(PDOException $e) {
        echo "Error ao editar o usuário, consulte o administrador do sistema";
        //echo "Error updating " . $e->getMessage();
    }
    
    
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

}else if ($type == "delete") {

    $id = filter_input(INPUT_POST, "id");

    try{
        
        // PEGA NOME DA IMAGEM E CAMINHO
        $img = $_POST["current_file"];
        $path = './assets/home/avatar/' . $img;

        // Checa se existe o arquivo de imagem na pasta, se existir deleta 
        if (file_exists($path)):
            // deleta imagem anterior
            unlink($path);
        else :
            echo "<script>
            alert('Ops, algo deu errado, arquivo não encontrado!');
            </script>";
        endif;

        // Deleta registro no BD
        $userDao->deleteUser($id);

    }catch(PDOException $e) {
        echo "erro ao deletar conta, consulte o administrador do sistema";
        //echo "erro ao deletar conta" . $e->getMessage();
    }

}
