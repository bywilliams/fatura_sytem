<?php 
    require_once("models/BankAccounts.php");
    require_once("models/Message.php");

    class BankAccountsDAO implements BankAccountsDAOInterface {


        private $conn;
        private $message;
        private $url;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildBankAccount ($data){

            $bankAccount = new BankAccounts();

            $bankAccount->id = $data['id'];
            $bankAccount->razao_social = $data['razao_social'];
            $bankAccount->cnpj = $data['cnpj'];
            $bankAccount->agencia = $data['agencia'];
            $bankAccount->conta = $data['conta'];
            $bankAccount->logo_img = $data['logo_img'];
            $bankAccount->card_color = $data['card_color'];
            $bankAccount->created_at = $data['created_at'];
            $bankAccount->updated_at = $data['updated_at'];

            return $bankAccount;

        }
        
        public function getAllBankAccounts() {

        }

        public function createbankAccount(BankAccounts $bankAccount) {

            $stmt = $this->conn->prepare("INSERT INTO bank_accounts (
                id,razao_social,cnpj,agencia,conta,lgo_img,card_color,created_at
            ) VALUES (
                :id, :razao_social, :cnpj, :agencia, :conta, :logo_img, :card_logo, NOW() 
            )");

            $stmt->bindParam(":id", $bankAccount->id, PDO::PARAM_INT);
            $stmt->bindParam(":razao_social", $bankAccount->razao_social, PDO::PARAM_STR);
            $stmt->bindParam(":cnpj", $bankAccount->cnpj, PDO::PARAM_STR);
            $stmt->bindParam(":agencia", $bankAccount->agencia, PDO::PARAM_INT);
            $stmt->bindParam(":conta", $bankAccount->conta, PDO::PARAM_INT);
            $stmt->bindParam(":logo_img", $bankAccount->logo_img, PDO::PARAM_STR);
            $stmt->bindParam(":card_color", $bankAccount->card_color, PDO::PARAM_STR);
            $stmt->bindParam(":created_at", $bankAccount->created_at);

            if($stmt->execute()) {
                $this->message->setMessage("Conta cadastrada com sucesso!", "success", "back");
            }

        }

        public function updateBankAccount(BankAccounts $bankAccount) {

        }

        public function destroyBankAccount($id) {

        }

    }


?>