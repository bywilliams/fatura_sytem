<?php 

    class BankAccounts {
        public $id;
        public $razao_social;
        public $cnpj;
        public $agencia;
        public $conta;
        public $logo_img;
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