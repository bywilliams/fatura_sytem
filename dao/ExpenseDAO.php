<?php 
require_once("models/Expense.php");
require_once("models/Message.php");
require_once("utils/config.php");

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

            $expense = new Expense();

            $expense->id = isset($data['id']) ? $data['id'] : null;
            $expense->description = $data['description'];
            $expense->value = number_format($data['value'], 2, ',', '.');
            $expense->dt_registered =  isset($data['dt_registered']) ? date("d-m-Y H:i:s", strtotime($data['dt_registered'])) : null;
            $expense->dt_expense =  isset($data['dt_expense']) ? date("d-m-Y", strtotime($data['dt_expense'])) : null;
            $expense->month_reference = isset($data['month_reference']) ? $data['month_reference'] : null;
            $expense->dt_updated = isset($data['dt_updated']) ? $data['dt_updated'] : null;
            $expense->user_name = isset($data['user_name']) ? $data['user_name'] : null;

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
                :description, :value, :user_id, NOW(), :dt_expense,:month_reference
            )");

            $stmt->bindParam(":description", $expense->description, PDO::PARAM_STR);
            $stmt->bindParam(":value", $expense->value);
            $stmt->bindParam(":user_id", $expense->user_id, PDO::PARAM_INT);
            $stmt->bindParam(":dt_expense", $expense->dt_expense, PDO::PARAM_STR);
            $stmt->bindParam(":month_reference", $expense->month_reference, PDO::PARAM_STR);

            if($stmt->execute()) {
                $this->message->setMessage("<script>
                Swal.fire({
                    title: 'Informação',
                    text: ' Despesa cadastrada com sucesso! ',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0B666A', 
                    cancelButtonText: 'Fechar',
                })
                ;</script>", "", "back");
            }

        }

        public function updateUserExpense(Expense $expense) {

            $stmt = $this->conn->prepare("UPDATE tb_expenses SET
            description = :description,
            value = :value,
            dt_expense = :dt_expense,
            dt_updated = NOW()
            WHERE id = :id
            ");

            $stmt->bindParam(":description", $expense->description, PDO::PARAM_STR);
            $stmt->bindParam(":value", $expense->value);
            $stmt->bindParam(":dt_expense", $expense->dt_expense, PDO::PARAM_STR);
            $stmt->bindParam(":id", $expense->id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $this->message->setMessage("<script>
                Swal.fire({
                    title: 'Informação',
                    text: ' Despesa atualizada com sucesso! ',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0B666A', 
                    cancelButtonText: 'Fechar',
                })
                ;</script>", "", "back");
            }

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

        public function getAllExpensesToPagination($user_id, $sql, $resultsPerPage = "", $offset = "") {
            
            $outFinancialMoviments = [];

            $stmt = $this->conn->query("SELECT 
            id, description, value, dt_registered, dt_expense, dt_updated 
            FROM tb_expenses AS exp 
            WHERE user_id = '$user_id' $sql
            ORDER BY id 
            DESC LIMIT $resultsPerPage OFFSET $offset");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $outFinancialMoviments[] = $this->buildExpense($financialMoviment);
                
                }
            }
            return $outFinancialMoviments;
        }

        public function getAllExpensesAdminToPagination($sql, $resultsPerPage = "", $offset = "") {
            
            $outFinancialMoviments = [];

            $stmt = $this->conn->query("SELECT 
            expe.id, expe.description, expe.value, expe.dt_registered, expe.dt_expense, expe.dt_updated,  
            CONCAT( usr.name, ' ',  usr.lastname) AS user_name
            FROM tb_expenses as expe 
            INNER JOIN users AS usr ON usr.id = expe.user_id 
            WHERE user_id > 0 $sql
            ORDER BY expe.id 
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

        public function countTotalExpensesCurrentMonth() {

            $countResults = 0;

            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM tb_expenses  
            ORDER BY id
            ");
            $stmt->execute();

            $countResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $countResults;

        }

        public function countTotalExpensesUserCurrentMonth($id) {

            $countResults = 0;

            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM tb_expenses
            WHERE user_id = '$id'  
            ORDER BY id
            ");
            $stmt->execute();

            $countResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $countResults;

        }

        public function getBiggestExpense($id) {

            $bigExpenses = [];

            $mes = date("m");

            $stmt = $this->conn->prepare("SELECT MAX(VALUE) AS value, description FROM tb_expenses 
                WHERE MONTH(month_reference) = '$mes' 
                AND user_id = :user_id 
                GROUP BY value DESC LIMIT 1
            ");

            $stmt->bindParam(":user_id", $id);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $bigExpenses[] = $this->buildExpense($data);
            }

            return $bigExpenses;

        }

        public function getLowerExpense($id) {
            
            $lowerExpense = [];

            $mes = date("m");

            $stmt = $this->conn->prepare("SELECT MIN(VALUE) AS value, description FROM tb_expenses 
                WHERE MONTH(month_reference) = '$mes' 
                AND user_id = :user_id 
                GROUP BY value ASC LIMIT 1
            ");

            $stmt->bindParam(":user_id", $id);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $lowerExpense[] = $this->buildExpense($data);
            }

            return $lowerExpense;
        }

        public function getAllCashOutflow($id) {

            $mes = date("m");

            $stmt = $this->conn->query("SELECT SUM(value) as sum FROM tb_expenses
            WHERE MONTH(month_reference) = '$mes'
            AND user_id = $id 
            ");
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $sum = number_format($row['sum'], 2, ',', '.');
                return $sum;
            }

        }


        public function destroyUserExpense($id) {

            if($id) {

                $stmt = $this->conn->prepare("DELETE FROM tb_expenses WHERE id = :id");
                $stmt->bindParam(":id", $id);

                if ($stmt->execute()) {
                    $this->message->setMessage("<script>
                    Swal.fire({
                        title: 'Informação',
                        text: ' Despesa excluída com sucesso!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0B666A', 
                        cancelButtonText: 'Fechar',
                    })
                    ;</script>", "", "back");
                }

            }

        }

    }


?>