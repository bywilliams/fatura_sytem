<?php 
require_once("traits/generates.php");
    class Banks {
        use Generates;

        public $id;
        public $cod;
        public $name;
        public $logo;
        public $created_at;
        public $updated_at;

    }

    interface BanksDAOInterface {

        public function buildBank($data);
        public function getAllbanks();
        public function createBank(Banks $bank);
        public function updateBank($id);
        public function deleteBank($id);


    }



?>