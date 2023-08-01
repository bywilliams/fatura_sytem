<?php 
require_once("models/Expense.php");
require_once("models/Message.php");


    class ExpenseDAO implements ExpenseDAOInterface {

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }
        

        public function buildExpense ($data) {

            require_once("utils/config.php");

            $expense = new Expense();

            $expense->id = $data['id'];
            $expense->description = $data['description'];
            $expense->value = number_format($data['value'], 2, ',', '.');
            $expense->dt_registered = $data['dt_registered'];
            $expense->dt_expense = $data['dt_expense'];
            $expense->month_reference = $data['month_reference'];
            $expense->dt_updated = $data['dt_updated'];

            return $expense;

        }

        public function getAllUserExpense($user_id) {

            $expenses = [];

            $stmt = $this->conn->prepare("SELECT 
                id, description, value, dt_registered, dt_expense, dt_updated, month_reference
                FROM tb_expenses
                WHERE user_id = :user_id
            ");

            $stmt->bindParam(":user_id", $user_id);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                
                $data = $stmt->fetchAll();
                foreach($data as $expense) {
                    $expenses[] = $this->buildExpense($expense);
                }
            }

            return $expenses;

        }

        public function createUserExpense(Expense $expense) {

            $stmt = $this->conn->prepare("INSERT INTO tb_expenses (
                description, value, user_id, dt_registered, dt_expense, month_reference
            ) VALUES ( 
                :description, :value, :user_id, :dt_registered, :dt_expense,:month_reference
            )");

            $stmt->bindParam(":description", $expense->description, PDO::PARAM_STR);
            $stmt->bindParam(":value", $expense->value);
            $stmt->bindParam(":user_id", $expense->user_id, PDO::PARAM_INT);
            $stmt->bindParam(":dt_registered", $expense->dt_registered, PDO::PARAM_STR);
            $stmt->bindParam(":dt_expense", $expense->dt_expense, PDO::PARAM_STR);
            $stmt->bindParam(":month_reference", $expense->month_reference, PDO::PARAM_STR);

            if($stmt->execute()) {
                $this->message->setMessage("Despesa cadastrada com sucesso!", "success", "back");
            }

        }

        public function updateUserExpense(Expense $expense) {

        }

        public function getReports($sql, $id) {

            $reportEntryData = [];
           
            $stmt = $this->conn->query("SELECT 
            id, description, value, dt_registered, dt_expense, dt_updated 
            FROM tb_expenses 
            WHERE user_id = $id $sql 
            ORDER BY value DESC");
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetchAll();

                foreach($data as $reportItem) {
                    $reportEntryData[] = $this->buildExpense($reportItem);
                }

            }

            return $reportEntryData;

        }

        public function getAllExpensesToPagination($id, $sql, $resultsPerPage = "", $offset = "") {
            
            $outFinancialMoviments = [];

            // // defini o período do dia 01 ao dia atual do sistema
            // $initial_date = date("Y-m-01 00:00:00");
            // $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT 
            id, description, value, dt_registered, dt_expense, dt_updated 
            FROM tb_expenses 
            WHERE user_id = $id $sql
            ORDER BY id 
            DESC LIMIT $resultsPerPage OFFSET $offset;");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $outFinancialMoviments[] = $this->buildExpense($financialMoviment);
                
                }
            }
            return $outFinancialMoviments;
        }

        public function countTypeExpensesCurrentMonth($id) {

            $countResults = 0;

            // // defini o período do dia 01 ao dia atual do sistema
            // $initial_date = date("Y-m-01 00:00:00");
            // $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM tb_expenses 
            WHERE user_id = $id 
            ORDER BY id
            ");
            $stmt->execute();

            $countResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $countResults;

        }


        public function destroyUserExpee($id) {

        }

    }


?>