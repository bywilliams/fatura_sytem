<?php
require_once("globals.php");
require_once("utils/config.php");
require_once("connection/conn.php");
require_once("models/Message.php");
require_once("models/BankAccounts.php");
require_once("dao/BankAccountsDAO.php");
require_once("dao/UserDAO.php");

$bankAccountDao = new BankAccountsDao($conn, $BASE_URL);

$message = new Message($BASE_URL);

// resgata dados do usuário
$userDao = new UserDAO($conn, $BASE_URL);
$userData = $userDao->verifyToken();

// Pega a data atual do sistema, necessita ser ecryptado também no BD
$current_date = $agora->format("Y-m-d H:i:s");

// recebe o tipo do form (Create, Update, Delete)
$type = filter_input(INPUT_POST, "type");

if ($type == "create") {

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $_SESSION['razao'] = $data['razao'];
    $_SESSION['cnpj'] = $data['cnpj'];
    $_SESSION['ag'] = $data['ag'];
    $_SESSION['cc'] = $data['cc'];
    $_SESSION['pix'] = $data['pix'];
    $_SESSION['color'] = $data['color'];

    if ($data) {

        // Preencher os dados da conta no Objeto
        $bankAccount = new BankAccounts();
        $bankAccount->razao_social = encryptData($data['razao'], $encryptionKey);
        $bankAccount->cnpj =  encryptData($data['cnpj'], $encryptionKey);
        $bankAccount->agencia =  encryptData($data['ag'], $encryptionKey);
        $bankAccount->conta =  encryptData($data['cc'], $encryptionKey);
        $bankAccount->created_at =  encryptData($current_date, $encryptionKey);
        $bankAccount->pix =  encryptData($data['pix'], $encryptionKey);
        $bankAccount->card_color = $data['color'];
        $bankAccount->banco = $data['banco'];
        // echo $encryptionKey . "<br>";
        // echo $bankAccount->decryptData($bankAccount->razao_social, $encryptionKey); exit;

        try {
            $bankAccountDao->createbankAccount($bankAccount);
            $_SESSION['razao'] = "";
            $_SESSION['cnpj'] = "";
            $_SESSION['ag'] = "";
            $_SESSION['cc'] = "";
            $_SESSION['pix'] = "";
            $_SESSION['color'] = "";
        } catch (PDOException $e) {
            //echo "Erro ao cadastrar conta, consulte o administrador do sistema";
            echo "Erro ao cadastrar conta: ".$e->getMessage();
        }

    } else {
        $message->setMessage("Preencha todos os campos", "success", "back");
    }

} else if ($type == "update") {
    
    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Preenche os dados do Objeto
    $bankAccount = new BankAccounts();
    $bankAccount->id = $data['id'];
    $bankAccount->razao_social = encryptData($data['razao'], $encryptionKey);
    $bankAccount->cnpj =  encryptData($data['cnpj'], $encryptionKey);
    $bankAccount->agencia =  encryptData($data['ag'], $encryptionKey);
    $bankAccount->conta =  encryptData($data['cc'], $encryptionKey);
    $bankAccount->pix =  encryptData($data['pix'], $encryptionKey);
    $bankAccount->updated_at =  encryptData($current_date, $encryptionKey);
    $bankAccount->card_color = $data['color'];
    $bankAccount->banco = $data['banco'];

    
    try{
        // faz o update
        $bankAccountDao->updateBankAccount($bankAccount);
    }catch(PDOException $e){ 
        // Apresenta um erro generico
        //echo "Erro ao atualizar conta, consulte o adminsitrado do sistema";
        echo "Erro ao atualizar conta" . $e->getMessage();
    }

} else if ($type == "delete") {

    $id = filter_input(INPUT_POST, "id");

   
    try{
        
        // PEGA NOME DA IMAGEM E CAMINHO
        $img = $_POST["current_file"];
        $path = './assets/home/contas/' . $img;

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
        $bankAccountDao->destroyBankAccount($id);

    }catch(PDOException $e) {
        echo "erro ao deletar conta, consulte o administrador do sistema";
        //echo "erro ao deletar conta" . $e->getMessage();
    }

}
