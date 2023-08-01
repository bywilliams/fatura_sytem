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

            require_once("utils/config.php");

            $bankAccount = new BankAccounts();

            $bankAccount->id = $data['id'];
            $bankAccount->razao_social = $data['razao_social'];
            $bankAccount->cnpj = $data['cnpj'];
            $bankAccount->agencia = $data['agencia'];
            $bankAccount->conta = $data['conta'];
            $bankAccount->pix = $data['pix'];
            $bankAccount->logo_img = $data['logo_img'];
            $bankAccount->card_color = $data['card_color'];
            $bankAccount->created_at = $data['created_at'];

            return $bankAccount;

        }
        
        public function getAllBankAccounts() {

            $accounts = [];

            $stmt = $this->conn->prepare("SELECT 
            id, razao_social, cnpj, agencia, conta, pix, logo_img, card_color, created_at 
            FROM bank_accounts
            ORDER BY id DESC");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetchAll();

                foreach ($data as $account) {
                    $accounts[] = $this->buildBankAccount($account);
                }

            }

            return $accounts;

        }

        public function createbankAccount(BankAccounts $bankAccount) {

            $stmt = $this->conn->prepare("INSERT INTO bank_accounts (
                razao_social, cnpj, agencia, conta, pix, logo_img, card_color, created_at
            ) VALUES (
                :razao_social, :cnpj, :agencia, :conta, :pix, :logo_img, :card_color, :created_at
            )");

            $stmt->bindParam(":razao_social", $bankAccount->razao_social, PDO::PARAM_STR);
            $stmt->bindParam(":cnpj", $bankAccount->cnpj, PDO::PARAM_STR);
            $stmt->bindParam(":agencia", $bankAccount->agencia, PDO::PARAM_STR);
            $stmt->bindParam(":conta", $bankAccount->conta, PDO::PARAM_STR);
            $stmt->bindParam(":pix", $bankAccount->pix, PDO::PARAM_STR);
            $stmt->bindParam(":logo_img", $bankAccount->logo_img, PDO::PARAM_STR);
            $stmt->bindParam(":card_color", $bankAccount->card_color, PDO::PARAM_STR);
            $stmt->bindParam(":created_at", $bankAccount->created_at, PDO::PARAM_STR);
            

            if($stmt->execute()) {
                $this->message->setMessage("Conta cadastrada com sucesso!", "success", "back");
            }

        }

        public function updateBankAccount(BankAccounts $bankAccount) {

            $stmt = $this->conn->prepare ("UPDATE bank_accounts SET
                razao_social = :razao_social,
                cnpj = :cnpj,
                agencia = :agencia,
                conta = :conta,
                pix = :pix,
                logo_img = :logo_img,
                card_color = :card_color,
                updated_at = :updated_at
                WHERE id = :id
            ");

            $stmt->bindParam(":id", $bankAccount->id, PDO::PARAM_INT);
            $stmt->bindParam(":razao_social", $bankAccount->razao_social, PDO::PARAM_STR);
            $stmt->bindParam(":cnpj", $bankAccount->cnpj, PDO::PARAM_STR);
            $stmt->bindParam(":agencia", $bankAccount->agencia, PDO::PARAM_STR);
            $stmt->bindParam(":conta", $bankAccount->conta, PDO::PARAM_STR);
            $stmt->bindParam(":pix", $bankAccount->pix, PDO::PARAM_STR);
            $stmt->bindParam(":logo_img", $bankAccount->logo_img, PDO::PARAM_STR);
            $stmt->bindParam(":card_color", $bankAccount->card_color, PDO::PARAM_STR);
            $stmt->bindParam(":updated_at", $bankAccount->updated_at, PDO::PARAM_STR);
            $stmt->bindParam(":id", $bankAccount->id, PDO::PARAM_STR);

            if($stmt->execute()) {
                $this->message->setMessage("Conta atualizada com sucesso", "success", "back");
            }            

        }


        public function destroyBankAccount($id) {

            if ($id) {
                
                $stmt = $this->conn->prepare("DELETE FROM bank_accounts WHERE id = :id");
                $stmt->bindParam(":id", $id);

                if ($stmt->execute()) {
                    $this->message->setMessage("Conta deletada com sucesso!", "success", "back");
                }

            }

        }

    }


?>