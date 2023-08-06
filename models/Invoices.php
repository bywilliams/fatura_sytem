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
        public $paid;
        public $user_name;
        public $conta_img;

        public function getFullName($user){
            return $user->name . " " . $user->lastname;
        }


    }

    interface InvoicesDAOInterface {

        public function buildInvoice ($data);
        public function getLatestInvoices($user_id);
        public function getAllInvoicesForAdminToPagination($sql = "", $resultsPerPage = "", $offset = "");
        // public function getAllUserInvoices($user_id);
        public function getBiggestInvoiceValueUser($id);
        public function getLowerInvoiceValueUser($id);
        public function setInvoicePaidAdmin(Invoices $invoice);
        public function getTotalBalance($id);
        public function createUserInvoice(Invoices $invoice);
        public function updateUserInvoice(Invoices $invoice);
        public function getReports($sql, $id);
        public function destroyUserInvoice($id);

    }


?> 