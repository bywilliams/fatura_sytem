<?php 

    class Invoices {

        public $id;
        public $invoice_one;
        public $emission;
        public $value;
        public $notation;
        public $type;
        public $invoice_two;
        public $dt_expired;
        public $reference;
        public $account;
        public $user_id;
        public $ammount_paid;
        public $invoice_one_status;
        public $invoice_two_status;
        public $user_name;
        public $conta_img;
        public $razao_social;

        public function getFullName($user){
            return $user->name . " " . $user->lastname;
        }


    }

    interface InvoicesDAOInterface {

        public function buildInvoice ($data); // constrói objetos de invoice
        public function getLatestInvoices($user_id); // pega as últimas 5 faturas cadastradas
        public function getAllInvoicesForAdminToPagination($sql = "", $resultsPerPage = "", $offset = ""); // traz todas as faturas cadatradas e com paginação para o admin
        // public function getAllUserInvoices($user_id);
        public function getBiggestInvoiceValueUser($id); // traz o maior valor de receita 
        public function getLowerInvoiceValueUser($id); // traz o menor valor de receita
        public function setInvoicePaidAdmin(Invoices $invoice); // muda status de fatura 
        public function getTotalBalance($id); // traz o saldo de faturas pagas do usuário
        public function getBalanceGeneral($sql = "" ); // pega o balanço geral entre receitas, receitas pagas, despesas e receitas pagas x despesas (valor liquido)
        public function createUserInvoice(Invoices $invoice); // insere fatura no BD
        public function updateUserInvoice(Invoices $invoice); // atualiza uma fatura no BD
        public function getReports($sql, $id); 
        public function destroyUserInvoice($id); // Destrói uma fatura no BD

    }


?> 