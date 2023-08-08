<?php 
    require_once("models/Banks.php");
    require_once("models/Message.php");

    class BanksDAO implements BanksDAOInterface {

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildBank($data) {

            $bank = new Banks();
            $bank->id = $data['id'];
            $bank->cod = $data['cod'];
            $bank->name = $data['name'];
            $bank->logo = $data['logo'];
            $bank->created_at = $data['created_at'];
            $bank->updated_at = $data['updated_at'];

            return $bank;

        }

        public function getAllbanks() {

            $banks = [];

            $stmt = $this->conn->prepare("SELECT id, cod, name, logo, created_at, updated_at FROM banks");
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll();
                foreach ($data as $bank) {
                    $banks[] = $this->buildBank($bank);
                }
            }

            return $banks;

        }

        public function createBank(Banks $bank) {

            $stmt = $this->conn->prepare("INSERT INTO banks (
                cod, name, logo, created_at
            ) VALUES(
                :cod, :name, :logo, NOW()
            )");

            $stmt->bindParam("cod", $bank->cod, PDO::PARAM_STR);
            $stmt->bindParam("name", $bank->name, PDO::PARAM_STR);
            $stmt->bindParam("logo", $bank->logo, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $this->message->setMessage("Banco criado com sucesso!", "success", "back");
            }

        }

        public function updateBank($id) {

        }

        public function deleteBank($id) {

            if ($id) {
                
                $stmt = $this->conn->prepare("DELETE FROM banks WHERE id = :id");
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $this->message->setMessage("Banco deletado com succeso!", "success", "back");
                }

            }

        }

    }


?>