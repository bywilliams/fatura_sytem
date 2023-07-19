<?php

require_once("globals.php");
require_once("connection/conn.php");
require_once("dao/FinancialMovimentDAO.php");
require_once("dao/UserDAO.php");
require_once("models/FinancialMoviment.php");
require_once("models/Message.php");


$financialMovimentDao = new FinancialMovimentDAO($conn, $BASE_URL);
$message = new Message($BASE_URL);

// resgata dados do usuário
$userDao = new UserDAO($conn, $BASE_URL);
$userData = $userDao->verifyToken();

$type = filter_input(INPUT_POST, "type");

// Criação de movimento financeiro
if ($type == "create") {

    $description = filter_input(INPUT_POST, "description");
    $type_action = filter_input(INPUT_POST, "type_action");
    $expense_type = filter_input(INPUT_POST, "expense_type");
    $obs = filter_input(INPUT_POST, "obs");

    $value = filter_input(INPUT_POST, "value");
    $value=preg_replace("/[^0-9,]+/i","",$value);
    $value=str_replace(",",".",$value);

    $category = filter_input(INPUT_POST, "category");

    // Current date para registro sem agendamento e date_scheduled para registros agendados
    date_default_timezone_set('America/Sao_Paulo');
    $current_date = date("Y-m-d H:i:s");
    $date_scheduled = filter_input(INPUT_POST, "date_scheduled");

    $financialMoviment = new FinancialMoviment();

    if ($description && $value && $type_action) {

        if ($type_action == 2) {

            if (empty($expense_type) || empty($category)) {
                // Informa que o campo Despesa precisa ser preenchido
                $message->setMessage("Para o tipo Saída, preencha também o campo despesa e categoria!", "error", "back");
                exit;
            }

        }

        $financialMoviment->description = $description;
        $financialMoviment->value = $value;
        $financialMoviment->type = $type_action;
        $financialMoviment->expense = $expense_type;
        $financialMoviment->category = $category;

        if ($date_scheduled != "") {
            $date_scheduled = "$date_scheduled 08:00:00";
            $financialMoviment->create_at = $date_scheduled;
            $financialMoviment->scheduled = "S";
        }else {
            $financialMoviment->create_at = $current_date;
            //$financialMoviment->scheduled = "";
        } 

        $financialMoviment->users_id = $userData->id;

        $financialMovimentDao->create($financialMoviment);

    } else {
        $message->setMessage("Preencha os campos descrição, valor e tipo!", "error", "back");
    }

} else if ($type == "edit") {

    // Edição de movimento financeiro existente
    $description_edit = filter_input(INPUT_POST, "description_edit");
    $obs = filter_input(INPUT_POST, "obs");
    $value_edit = filter_input(INPUT_POST, "value_edit");
    $value_edit=preg_replace("/[^0-9,]+/i","",$value_edit);
    $value_edit=str_replace(",",".",$value_edit);
    $expense_type_edit = filter_input(INPUT_POST, "expense_type_edit");
    $category_edit = filter_input(INPUT_POST, "category_edit");

    $financialMoviment = new FinancialMoviment();

    // Preenche os dados de finança no objeto
    $financialMoviment->description = $description_edit;
    $financialMoviment->obs = $obs;
    $financialMoviment->value = $value_edit;
    $financialMoviment->expense = $expense_type_edit;
    $financialMoviment->category = $category_edit;
    $financialMoviment->id = $_GET["id"];

    $financialMovimentDao->update($financialMoviment);
    
}else if($type == "deletar"){

    // Pega id de resgitro para deleção
    $id = filter_input(INPUT_POST, "id");

    // Deletar Registro Selecionado
    try {
        $financialMovimentDao->destroy($id);
    } catch (\Throwable $e) {
        echo "Falha ao deletar registro : {$e->getMessage()}";
    }
    
}

