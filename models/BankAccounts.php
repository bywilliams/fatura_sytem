<?php 
require_once("traits/generates.php");

    class BankAccounts {
        use Generates;

        public $id;
        public $razao_social;
        public $cnpj;
        public $agencia;
        public $conta;
        public $logo_img;
        public $pix;
        public $card_color;
        public $created_at;
        public $updated_at;

    }

    interface BankAccountsDAOInterface{

        public function buildBankAccount ($data);
        public function getAllBankAccounts();
        public function createbankAccount(BankAccounts $bankAccount);
        public function updateBankAccount(BankAccounts $bankAccount);
        public function destroyBankAccount($id);

    }





?>