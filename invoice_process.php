<?php

require_once("globals.php");
require_once("connection/conn.php");
require_once("dao/InvoicesDAO.php");
require_once("dao/UserDAO.php");
require_once("models/Invoices.php");
require_once("models/Message.php");

$message = new Message($BASE_URL);

$invoiceDao = new InvoicesDAO($conn, $BASE_URL);

// resgata dados do usuário
$userDao = new UserDAO($conn, $BASE_URL);
$userData = $userDao->verifyToken();

$type = filter_input(INPUT_POST, "type");

// registro das faturas
if ($type == "create") {

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $_SESSION['invoice_one'] = $data['invoice_one'];
    $_SESSION['date_emission'] = $data['date_emission'];
    $_SESSION['value'] = $data['value'];
    $_SESSION['notation'] = $data['notation'];
    $_SESSION['type_paid'] = $data['type_paid'];
    $_SESSION['invoice_two'] = $data['invoice_two'];
    $_SESSION['dt_expired'] = $data['dt_expired'];
    $_SESSION['reference'] = $data['reference'];
    $_SESSION['account'] = $data['account'];


    if($data) {

        $invoice = new Invoices();
        $invoice->invoice_one = $data['invoice_one'];
        $invoice->emission = $data['emission'];
        $value = preg_replace("/[^0-9.]+/i","",$data['value']);
        //echo $value; 
        $value = str_replace(",",".",$value);
        //echo $value; 
        $invoice->value = $value;
        $invoice->notation = $data['notation'];
        $invoice->type = $data['type_paid'];
        $invoice->invoice_two = $data['invoice_two'];
        $invoice->dt_expired = $data['dt_expired'];
        $invoice->reference = $data['reference'];
        $invoice->account = $data['account'];
        $invoice->user_id = $userData->id;

        //print_r($data); exit;

        try {
            // Registra no BD
            $invoiceDao->createUserInvoice($invoice);
            $_SESSION['invoice_one'] = "";
            $_SESSION['date_emission'] = "";
            $_SESSION['value'] = "";
            $_SESSION['notation'] = "";
            $_SESSION['type_paid'] = "";
            $_SESSION['invoice_two'] = "";
            $_SESSION['dt_expired'] = "";
            $_SESSION['reference'] = "";
            $_SESSION['account'] = "";

        } catch (PDOException $e) {
            echo "Erro ao cadastrar faturas, consulte o administrador do sistema. <br>";
            echo "Error: ". $e->getMessage();

        }

    }else {
        $message->setMessage("Preencha os campos necessários!", "error", "back");
    }
      
    // $value = filter_input(INPUT_POST, "value");
    // $value = preg_replace("/[^0-9,]+/i","",$value);
    // $value = str_replace(",",".",$value);


    
} else if ($type == "update") {

    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if ($data) {
        $invoice = new Invoices();
    
        // Preenche os dados de finança no objeto
        $invoice->invoice_one = $data['invoice_one'];
        
        $value = $data['value'];
        $value = preg_replace("/[^0-9,]+/i","",$value);
        $value = str_replace(",",".",$value);
        //echo $value; exit;
        $invoice->value = $value;
        $invoice->type = $data['type_edit'];
        $invoice->notation = $data['notation'];
        $invoice->dt_expired = $data['dt_expired'];
        $invoice->invoice_two = $data["invoice_two"];
        $invoice->reference = $data['reference'];
        $invoice->account = $data['account'];
        $invoice->id = $data['id'];
        
        try {
            $invoiceDao->updateUserInvoice($invoice);
        } catch (\PDOException $e) {
            // echo "Erro ao atualizar fatura, consulte o administrador do sistena.";
            echo "Error: " . $e->getMessage();
        }
    }else {
        $message->setMessage("Houve um erro inesperado.", "error", "index.php");
    }


    
} else if ($type == "editInvoiceStatus"){

    $invoice_status = filter_input(INPUT_POST, "invoice_status");
    $value_paid = filter_input(INPUT_POST, "value_paid");
    $value = preg_replace("/[^0-9,]+/i","",$value_paid);
    $value = str_replace(",",".",$value);
    $invoice_id = filter_input(INPUT_POST, "id");

    //echo "$invoice_status, $value_paid, $invoice_id"; exit;

    if ($invoice_status) {
        
        $invoice = new Invoices();
        $invoice->value = $value;
        $invoice->id = $invoice_id;
       
        try {
            $invoiceDao->setInvoicePaidAdmin($invoice);
        } catch (PDOException $e) {
            //echo "Erro ao atualizar fatura, consulte o administrado do sistema";
            echo "Error: " . $e->getMessage();
        }

    }else {
        $message->setMessage("Preencha o campo de status!", "error", "back");
    }

} else if($type == "delete"){

    // Pega id de resgitro para deleção
    $id = filter_input(INPUT_POST, "id");

    if ($id) {
        // Deletar Registro Selecionado
        try {
            $invoiceDao->destroyUserInvoice($id);
        } catch (PDOException $e) {
            //echo "Erro ao deletar registro, consulte o administrador do sistema";
            echo "Falha ao deletar registro : {$e->getMessage()}";
        }
    }else {
        $message->setMessage("Um erro foi encontrado", "error", "index.php");
    }
    
}

