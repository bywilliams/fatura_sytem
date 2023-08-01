<?php 
require_once("globals.php");
require_once("utils/config.php");
require_once("connection/conn.php");
require_once ("models/Expense.php");
require_once("dao/UserDAO.php");
require_once("dao/ExpenseDAO.php");

$expenseDao = new ExpenseDAO($conn, $BASE_URL);

$message = new Message($BASE_URL);

// resgata dados do usuário
$userDao = new UserDAO($conn, $BASE_URL);
$userData = $userDao->verifyToken();

// Pega a data atual do sistema, necessita ser ecryptado também no BD
$current_date = $agora->format("Y-m-d H:i:s");

$type = filter_input(INPUT_POST, "type");

if ($type == "create") {

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $_SESSION['description'] = $data['description'];
    $_SESSION['value'] =  $data['value'];
    $_SESSION['date_expense'] = $data['date_expense'];

    if($data['description'] && $data['value'] && $data['date_expense']) {
        
        $expense = new Expense();
        $expense->description = encryptData($data['description'], $encryptionKey);
        $value = preg_replace("/[^0-9,]+/i","",$data['value']);
        $value = str_replace(",",".",$value);
        $expense->value = $value;
        $expense->user_id = $userData->id;
        $expense->dt_registered = encryptData($current_date, $encryptionKey);
        $expense->dt_expense = encryptData($data['date_expense'], $encryptionKey);
        $expense->month_reference = encryptData((date("m", strtotime($data['date_expense']))), $encryptionKey);


        //echo date("m", strtotime($data['date_expense'])); exit;

        //echo $expense->dt_registered . "<br>" . $expense->dt_expense; exit;
        // echo decryptData($expense->description, $encryptionKey), "<br>" . 
        // $expense->value, "<br>" . 
        // decryptData($expense->dt_registered, $encryptionKey), "<br>" . 
        // decryptData($expense->dt_expense, $encryptionKey);
        // exit;
        try{
            // insere despesa no BD
            $expenseDao->createUserExpense($expense);
            $_SESSION['description'] = "";
            $_SESSION['value'] =  "";
            $_SESSION['date_expense'] = "";
        }catch(PDOException $e){ 
            //echo "Erro ao cadastrar despesa, consulte o administrador do sistena";
            echo "Erro ao cadastrar despesa". $e->getMessage();
        }


    }else {
        $message->setMessage("Por favor preencha todos os campos", "error", "back");
    }

}elseif ($type == "update") {
    echo "update";
}elseif ($type == "delete") { 
    echo "delete";
}




?>