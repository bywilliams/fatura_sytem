<?php 
    require_once("models/Invoices.php");
    require_once("models/Message.php");

    class InvoicesDAO implements InvoicesDAOInterface {

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildInvoice ($data) {

            $invoice = new Invoices();

            $invoice->id = $data['id'];
            $invoice->invoice_one = $data['invoice_one'];
            $invoice->emission = date("d-m-Y H:i:s", strtotime($data['emission']));
            $invoice->value =  $data['value'];
            $invoice->notation = $data['notation'];
            $invoice->type = $data['type'];
            $invoice->invoice_two = $data['invoice_two'];
            $invoice->dt_expired = date("d-m-Y", strtotime($data['dt_expired']));
            $invoice->reference = $data['reference'];
            $invoice->account = $data['account'];
            $invoice->user_id = $data['user_id'];
            $invoice->invoice_one_status = $data['invoice_one_status'];
            $invoice->invoice_two_status = $data['invoice_two_status'];
            $invoice->user_name = $data['user_name'];
            $invoice->conta_img = $data['logo'];
            $invoice->razao_social = $data['razao_social'];

            return $invoice;

        }

        public function getLatestInvoices($user_id) {

            $invoices = [];

            $stmt = $this->conn->prepare("SELECT 
            invoices.id, invoice_one, emission, value, notation, type, invoice_two, dt_expired, reference, invoice_one_status, invoice_two_status,  
            bank_accounts.razao_social, banks.logo
            FROM invoices INNER JOIN bank_accounts ON invoices.account = bank_accounts.id
           	INNER JOIN banks ON bank_accounts.banco = banks.cod
            WHERE user_id = :user_id 
            ORDER BY id DESC LIMIT 5");
            $stmt->bindParam(":user_id", $user_id);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $data = $stmt->fetchAll();

                foreach ($data as $invoice) {
                    $invoices[] = $this->buildInvoice($invoice);
                }

            }

            return $invoices;

        }

        // public function getAllUserInvoices($user_id) {

        //     $invoices = [];

        //     $stmt = $this->conn->prepare("SELECT 
        //     id, invoice_one, emission, value, notation, type, invoice_two, dt_expired, reference, account, user_id, invoice_one_status 
        //     WHERE user_id = :user_id");
        //     $stmt->bindParam(":usert_id", $user_id);

        //     $stmt->execute();

        //     if ($stmt->rowCount() > 0) {
                
        //         $data = $stmt->fetchAll();

        //         foreach ($data as $invoice) {
        //             $invoices[] = $this->buildInvoice($invoice);
        //         }

        //     }

        //     return $invoices;

        // }

        public function getAllInvoicesUserToPagination($id, $sql, $resultsPerPage = "", $offset = "") {
            
            $outFinancialMoviments = [];

            $stmt = $this->conn->query("SELECT 
            invoices.id, invoice_one, emission, value, notation, type, invoice_two, dt_expired, 
            reference, account, user_id, invoice_one_status, banks.logo
            FROM invoices INNER JOIN bank_accounts ON invoices.account = bank_accounts.id 
            INNER JOIN banks ON bank_accounts.banco = banks.cod 
            WHERE user_id = '$id' $sql
            ORDER BY id 
            DESC LIMIT $resultsPerPage OFFSET $offset");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $outFinancialMoviments[] = $this->buildInvoice($financialMoviment);
                
                }
            }
            return $outFinancialMoviments;
        }

        public function getAllInvoicesUserExpiringToPagination($id, $sql, $resultsPerPage = "", $offset = "") {
            
            $invoiceExpiring = [];

            $currentDate = date("Y-m-d");

            $stmt = $this->conn->query("SELECT 
            invoices.id, invoice_one, emission, value, notation, type, invoice_two, dt_expired, reference, account, user_id, invoice_one_status, bank_accounts.logo_img AS 'conta_img'
            FROM invoices INNER JOIN bank_accounts ON invoices.account = bank_accounts.id
            WHERE user_id = '$id' AND dt_Expired = '$currentDate' $sql
            ORDER BY id 
            DESC LIMIT $resultsPerPage OFFSET $offset");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $invoiceExpiring[] = $this->buildInvoice($financialMoviment);
                
                }
            }
            return $invoiceExpiring;
        }

        public function checkInvoicesUserExpiringToday($id) {
            
            $invoiceExpiring = [];

            $currentDate = date("Y-m-d");

            $stmt = $this->conn->query("SELECT 
            id, invoice_one, emission, value, invoice_two, dt_expired, reference 
            FROM invoices
            WHERE user_id = '$id' AND dt_Expired = '$currentDate' 
            ");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $invoiceExpiring[] = $this->buildInvoice($financialMoviment);
                
                }
            }
            return $invoiceExpiring;
        }
        

        public function countInvoicesUser($id) {

            $countResults = 0;

            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM invoices 
                WHERE user_id = '$id' 
                ORDER BY id
            ");
            $stmt->execute();

            $countResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $countResults;

        }

        public function getAllCashInflow($id) {

            //$mes = date("m");

            $stmt = $this->conn->prepare("SELECT SUM(value) as sum FROM invoices
            WHERE user_id = :user_id
            ");
            $stmt->bindParam(":user_id", $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $sum = number_format($row['sum'], 2, ',', '.');
                return $sum;
            }

        }

        public function countAllInvoicesForAdmin() {

            $invoices = [];

            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM invoices 
            ");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $data = $stmt->fetchAll();

                foreach ($data as $invoice) {
                    $invoices[] = $this->buildInvoice($invoice);
                }

            }

            return $invoices;

        }

        public function getAllInvoicesForAdminToPagination($sql = "", $resultsPerPage = "", $offset = "") {

            $invoices = [];

            $stmt = $this->conn->prepare(" SELECT 
            invoices.id, invoice_one, emission, value, notation, type, invoice_two, dt_expired, reference, account, invoice_one_status, user_id, CONCAT( users.name, ' ',  users.lastname) AS user_name, banks.logo
            FROM invoices INNER JOIN users ON users.id = invoices.user_id INNER JOIN bank_accounts ON invoices.account = bank_accounts.id
            INNER JOIN banks ON bank_accounts.banco = banks.cod
            WHERE invoices.id <> 0 $sql
            ORDER BY invoices.id
            DESC LIMIT $resultsPerPage OFFSET $offset
            ");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $data = $stmt->fetchAll();

                foreach ($data as $invoice) {
                    $invoices[] = $this->buildInvoice($invoice);
                }

            }

            return $invoices;

        }

        public function getBiggestInvoiceValueUser($id) {

            $bigInvoice = [];

            $mes = date("m");

            $stmt = $this->conn->prepare("SELECT MAX(VALUE) AS 'value', reference FROM invoices 
                WHERE MONTH(emission) = '$mes' 
                AND user_id = :user_id 
                GROUP BY value LIMIT 1
            ");

            $stmt->bindParam(":user_id", $id);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $bigInvoice[] = $this->buildInvoice($data);
            }

            return $bigInvoice;

        }

        public function getLowerInvoiceValueUser($id) {
            
            $lowerInvoice = [];

            $mes = date("m");

            $stmt = $this->conn->prepare("SELECT MIN(VALUE) AS value, reference FROM invoices 
                WHERE MONTH(emission) = '$mes' 
                AND user_id = :user_id 
                GROUP BY value LIMIT 1
            ");

            $stmt->bindParam(":user_id", $id);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $lowerInvoice[] = $this->buildInvoice($data);
            }

            return $lowerInvoice;
        }

        public function setInvoicePaidAdmin(Invoices $invoice) {

            $stmt = $this->conn->prepare("UPDATE invoices SET invoice_one_status = :invoice_one_status WHERE id = :id");
            $stmt->bindParam(":invoice_one_status", $invoice->invoice_one_status);
            $stmt->bindParam(":id", $invoice->id);

            if ($stmt->execute()) {
                $this->message->setMessage("Fatura marcada como paga!", "success", "back");
            }

        }

        public function getTotalBalance($id) {

            $mes = date("m");

            $stmt = $this->conn->query("SELECT SUM(value) AS 'value' FROM invoices 
            WHERE MONTH(emission) = '$mes' AND user_id = $id AND invoice_one_status = 'S'");
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $total_balance = number_format($row['value'], 2, ',', '.');
                return $total_balance;
            }

        }

        public function createUserInvoice(Invoices $invoice) {

            $stmt = $this->conn->prepare("INSERT INTO invoices (
                id, invoice_one, emission, value, notation, type, invoice_two, dt_expired, reference, account, invoice_one_status, user_id
            ) VALUES (
                :id, :invoice_one, :emission, :value, :notation, :type, :invoice_two, :dt_expired, :reference, :account, 'A', :user_id
            )");

            $stmt->bindParam(":id", $invoice->id);
            $stmt->bindParam(":invoice_one", $invoice->invoice_one);
            $stmt->bindParam(":emission", $invoice->emission);
            $stmt->bindParam(":value", $invoice->value);
            $stmt->bindParam(":notation", $invoice->notation);
            $stmt->bindParam(":type", $invoice->type);
            $stmt->bindParam(":invoice_two", $invoice->invoice_two);
            $stmt->bindParam(":dt_expired", $invoice->dt_expired);
            $stmt->bindParam(":reference", $invoice->reference);
            $stmt->bindParam(":account", $invoice->account);
            $stmt->bindParam(":user_id", $invoice->user_id);

            if ($stmt->execute()) {
                $this->message->setMessage("Fatura cadastrada com sucesso!", "success", "back");
            }

        }

        public function updateUserInvoice(Invoices $invoice) {

            $stmt = $this->conn->prepare("UPDATE invoices SET
                invoice_one = :invoice_one, 
                value = :value, 
                notation = :notation, 
                type = :type, 
                invoice_two = :invoice_two, 
                dt_expired = :dt_expired, 
                reference = :reference, 
                account = :account
                WHERE id = :id
            ");

            $stmt->bindParam(":invoice_one", $invoice->invoice_one);
            $stmt->bindParam(":value", $invoice->value);
            $stmt->bindParam(":notation", $invoice->notation);
            $stmt->bindParam(":type", $invoice->type);
            $stmt->bindParam(":invoice_two", $invoice->invoice_two);
            $stmt->bindParam(":dt_expired", $invoice->dt_expired);
            $stmt->bindParam(":reference", $invoice->reference);
            $stmt->bindParam(":account", $invoice->account);
            $stmt->bindParam(":id", $invoice->id);

            if ($stmt->execute()) {
                $this->message->setMessage("Fatura atualizada com sucesso!", "success", "back");
            }

        }

        public function getReports($sql, $id) {

        }

        public function destroyUserInvoice($id) {

            if ($id) {
                
                $stmt = $this->conn->prepare("DELETE FROM invoices WHERE id = :id");
                $stmt->bindParam(":id", $id);

                if ($stmt->execute()) {
                    $this->message->setMessage("Registro deletado com sucesso!", "success", "back");
                }

            }

        }

    }


?>