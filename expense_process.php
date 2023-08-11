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

    //echo "form create"; exit;

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $_SESSION['description'] = $data['description'];
    $_SESSION['value'] =  $data['value'];
    $_SESSION['date_expense'] = $data['date_expense'];

    if($data['description'] && $data['value'] && $data['date_expense']) {
        
        $expense = new Expense();
        $expense->description = $data['description'];
        $value = preg_replace("/[^0-9,]+/i","",$data['value']);
        $value = str_replace(",",".",$value);
        $expense->value = $value;
        $expense->user_id = $userData->id;
        $expense->dt_expense = $data['date_expense'];
        $expense->month_reference = date("Y/m/d", strtotime($data['date_expense']));
        
        try{
           
            // insere despesa no BD
            $expenseDao->createUserExpense($expense);
            $_SESSION['description'] = "";
            $_SESSION['value'] =  "";
            $_SESSION['date_expense'] = "";
        }catch(PDOException $e){ 
            echo "Erro ao cadastrar despesa, consulte o administrador do sistena";
            //echo "Erro ao cadastrar despesa". $e->getMessage();
        }


    }else {
        $message->setMessage("Por favor preencha todos os campos", "error", "back");
    }

}elseif ($type == "update") {

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $expense = new Expense();
    $expense->description = $data['description'];
    $value = preg_replace("/[^0-9,]+/i","",$data['value']);
    $value = str_replace(",",".",$value);
    $expense->value = $value;
    $expense->dt_expense = $data['date_expense'];
    $expense->id = $data['id'];

    //echo "$expense->description, $expense->value, $expense->dt_expense";
    try{
        $expenseDao->updateUserExpense($expense);
    }catch (PDOException $e) {
        echo "Erro ao atualizar despesa consulte o administrador do sistema";
        //echo "Error: " . $e->getMessage();
    }
    
}elseif ($type == "delete") { 

    $id = filter_input(INPUT_POST, "id");
   
    try{       
        // Deleta registro no BD
        $expenseDao->destroyUserExpense($id);

    }catch(PDOException $e) {
        //echo "erro ao deletar despesesa, consulte o administrador do sistema";
        echo "erro ao deletar conta " . $e->getMessage();
    }

}




?>