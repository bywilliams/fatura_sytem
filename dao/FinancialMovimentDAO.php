<?php 
    require_once("models/FinancialMoviment.php");
    // require_once("models/User.php");
    require_once("models/Message.php");
    require_once("models/Categorys.php");
    require_once("dao/CategorysDAO.php");


    Class FinancialMovimentDAO implements FinancialMovimentDAOInterface {

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }


        public function buildFinancialMoviment($data) {

            $financialMoviment = new FinancialMoviment();

            $financialMoviment->id = $data['id'];
            $financialMoviment->description = $data['description'];
            $financialMoviment->obs = $data['obs'];
            $financialMoviment->expense = $data['expense'];
            $financialMoviment->expense == "F" ? $financialMoviment->expense = "Fixa" : $financialMoviment->expense = "Variavel"; 
            $financialMoviment->value = number_format($data['value'], 2, ',', '.');
            $financialMoviment->type = $data['type'];
            $financialMoviment->scheduled = $data['scheduled'];
            $category = $data['category'];
            $financialMoviment->users_id = $data["users_id"];
            $financialMoviment->create_at = $data['create_at'];

            // Traz todas as categorias disponíveis
            $categoryDao = new CategorysDAO($this->conn);
            $entryCategorysArray = $categoryDao->getAllEntryCategorys();
            $exitCategorysArray = $categoryDao->getAllExitCategorys();

            // Percorre as categorias de entrada e saída para determinar a qual delas o registro pertence
            foreach ($exitCategorysArray as $exitCategory):
                $category == $exitCategory->id ? $financialMoviment->category = $exitCategory->category_name : "";
            endforeach;

            foreach ($entryCategorysArray as $entryCategory):
                $category == $entryCategory->id ? $financialMoviment->category = $entryCategory->category_name : "";
            endforeach;
            
            // Alteração de formato de data para padrão Brasileiro
            $timestamp = strtotime($financialMoviment->create_at); 
            $newDate = date("d/m/Y H:i:s", $timestamp );
            $financialMoviment->create_at = $newDate;
            
            $financialMoviment->update_at = $data["update_at"];

            return $financialMoviment;
        }
        
        public function findAll() {
            //TODO: para o menu relatórios criar o item todos (para todos as movimentações)
        }

        public function getHighValueIncome($id) {

            $highValueData = [];
            
            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT MAX(VALUE) AS maior_valor, description FROM tb_finances 
                WHERE create_at BETWEEN '$initial_date' AND '$current_date' 
                AND TYPE= 1 AND users_id = $id 
                GROUP BY value DESC LIMIT 1"
            );
            $stmt->execute();
           
            $data = $stmt->fetchAll();

            foreach ($data as $row){
                $highValueData = $row["description"] . ": R$ " . number_format($row['maior_valor'], 2, ',', '.');
            }

             // Se não dados no BD recebe uma string padrão 
             if (empty($highValueData)) {
                $highValueData = "Não há dados registrados";
            }

            return $highValueData;

        }

        public function getLowerValueIncome($id) {

            $lowerValueData = [];

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");
           
           

            $stmt = $this->conn->query("SELECT MIN(VALUE) AS menor_valor, description FROM tb_finances 
                WHERE create_at BETWEEN '$initial_date' AND '$current_date' 
                AND TYPE = 1 
                AND users_id = $id 
                GROUP BY value ASC LIMIT 1"
            );
            $stmt->execute();
           
            $data = $stmt->fetchAll();

            foreach ($data as $row){
                $lowerValueData = $row["description"] . ": R$ " . number_format($row['menor_valor'], 2, ',', '.');
            }
            
             // Se não dados no BD recebe uma string padrão 
            if (empty($lowerValueData)) {
                $lowerValueData = "Não há dados registrados";
            }

            return $lowerValueData;

        }

        public function getBiggestExpense($id) {

            $biggestValueData = [];
            
            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT MAX(VALUE) AS maior_valor, description FROM tb_finances 
            WHERE create_at BETWEEN '$initial_date' AND '$current_date' 
            AND TYPE = 2 AND users_id = $id 
            GROUP BY value DESC LIMIT 1");
            $stmt->execute();

            $data = $stmt->fetchAll();

            foreach ($data as $row){
                $biggestValueData = $row["description"] . ": R$ " . number_format($row['maior_valor'], 2, ',', '.');
            }
            
            // Se não dados no BD recebe uma string padrão 
            if (empty($biggestValueData)) {
                $biggestValueData = "Não há dados registrados";
            }

            return $biggestValueData;

        }

        public function getLowerExpense($id) {

            $lowerValueData = [];
            
            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT MIN(VALUE) AS menor_valor, description FROM tb_finances 
            WHERE create_at BETWEEN '$initial_date' AND '$current_date' 
            AND TYPE = 2 AND users_id = $id 
            GROUP BY value ASC LIMIT 1");
            $stmt->execute();

            $data = $stmt->fetchAll();

            foreach ($data as $row){
                $lowerValueData = $row["description"] . ": R$ " . number_format($row['menor_valor'], 2, ',', '.');
            }

             // Se não dados no BD recebe uma string padrão 
            if (empty($lowerValueData)) {
                $lowerValueData = "Não há dados registrados";
            }

            return $lowerValueData;

        }

        public function getLatestFinancialMoviment($id) {
            
            $financialMoviments = [];
            $mes = date('m');

            $stmt = $this->conn->query("SELECT * FROM tb_finances WHERE MONTH(create_at) = '$mes' AND users_id = $id ORDER BY id DESC LIMIT 5");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $financialMoviments[] = $this->buildFinancialMoviment($financialMoviment);
                
                }
            }
            return $financialMoviments;
        }

        public function getAllOutFinancialMoviment($id, $resultsPerPage = "", $offset = "") {
            
            $outFinancialMoviments = [];

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT 
            id, description, obs, value, expense, type, category, create_at, update_at, users_id 
            FROM tb_finances 
            WHERE create_at BETWEEN '$initial_date' AND '$current_date' AND users_id = $id AND type = 2 AND category IS NOT NULL 
            ORDER BY id 
            DESC LIMIT $resultsPerPage OFFSET $offset;");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $outFinancialMoviments[] = $this->buildFinancialMoviment($financialMoviment);
                
                }
            }
            return $outFinancialMoviments;
        }

        public function getAllEntryFinancialMoviment($id, $resultsPerPage = "", $offset = "") {
            
            $entryFinancialMoviments = [];

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT 
            id, description, obs, value, expense, type, category, create_at, update_at, users_id 
            FROM tb_finances 
            WHERE create_at BETWEEN '$initial_date' AND '$current_date' AND users_id = $id AND type = 1 AND category IS NOT NULL 
            ORDER BY id 
            DESC limit $resultsPerPage OFFSET $offset");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $entryFinancialMoviments[] = $this->buildFinancialMoviment($financialMoviment);
                
                }
            }
            return $entryFinancialMoviments;
        }

        public function countTypeFinancialCurrentMonth($id, $type) {

            $countResults = 0;

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM tb_finances 
            WHERE create_at BETWEEN '$initial_date' AND '$current_date' AND users_id = $id AND type = '$type' AND category IS NOT NULL 
            ORDER BY id
            ");
            $stmt->execute();

            $countResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $countResults;

        }

        public function getAllCashInflow($id) {

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT SUM(value) as sum FROM tb_finances
            WHERE create_at BETWEEN '$initial_date' AND '$current_date'
            AND users_id = $id 
            AND type = 1"); 
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $sum = number_format($row['sum'], 2, ',', '.');
                return $sum;
            }

        }

        public function getAllCashInflowScheduled($id) {
            
            $entryFinancialScheduledMoviments = [];

            $stmt = $this->conn->query("SELECT 
            id, description, obs, value, expense, type, category, scheduled, create_at, update_at, users_id 
            FROM tb_finances 
            WHERE MONTH(create_at) = '6' AND users_id = $id AND type = 1 AND category IS NOT NULL AND scheduled = 'S' 
            ORDER BY id DESC LIMIT 10");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $entryFinancialScheduledMoviments[] = $this->buildFinancialMoviment($financialMoviment);
                
                }
            }
            return $entryFinancialScheduledMoviments;
        }

        public function getAllCashOutflow($id) {

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT SUM(value) as sum FROM tb_finances
            WHERE create_at BETWEEN '$initial_date' AND '$current_date'
            AND users_id = $id 
            AND type = 2");
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $sum = number_format($row['sum'], 2, ',', '.');
                return $sum;
            }

        }

        public function getAllCashOutflowScheduled($id) {

            $entryFinancialScheduledMoviments = [];

            $stmt = $this->conn->query("SELECT 
            id, description, obs, value, expense, type, category, scheduled, create_at, update_at, users_id 
            FROM tb_finances 
            WHERE MONTH(create_at) = '6' AND users_id = $id AND type = 2 AND category IS NOT NULL AND scheduled = 'S' 
            ORDER BY id DESC LIMIT 10");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $financialMovimentsArray = $stmt->fetchAll();

                foreach ($financialMovimentsArray as $financialMoviment){

                    $entryFinancialScheduledMoviments[] = $this->buildFinancialMoviment($financialMoviment);
                
                }
            }
            return $entryFinancialScheduledMoviments;
        }

        public function getTotalBalance($id) {

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->query("SELECT (SELECT SUM(value) FROM tb_finances 
            WHERE create_at BETWEEN '$initial_date' AND '$current_date' AND users_id = $id AND TYPE = 1)
             - 
            (SELECT SUM(value) FROM tb_finances 
            WHERE create_at BETWEEN '$initial_date' AND '$current_date' AND users_id = $id AND TYPE = 2) 
            AS total_balance");
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $total_balance = number_format($row['total_balance'], 2, ',', '.');
                return $total_balance;
            }

        }

        public function getReports($sql, $type, $id) {

            $reportEntryData = [];
           
            $stmt = $this->conn->query("SELECT 
            id, description, obs, value, expense, type, category, create_at, update_at, users_id 
            FROM tb_finances 
            WHERE users_id = $id AND type = $type AND category IS NOT NULL $sql 
            ORDER BY value DESC");
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetchAll();

                foreach($data as $reportItem) {
                    $reportEntryData[] = $this->buildFinancialMoviment($reportItem);
                }

            }

            return $reportEntryData;

        }

        public function getAllEntryReports($sql, $type, $id) {

            $reportEntryData = [];

            $stmt = $this->conn->query("SELECT id, description, obs, value, expense, type, category, create_at, update_at, users_id FROM tb_finances WHERE users_id = $id AND type = $type AND category IS NOT NULL $sql ORDER BY value DESC");
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetchAll();

                foreach($data as $reportItem) {
                    $reportEntryData[] = $this->buildFinancialMoviment($reportItem);
                }

            }

            return $reportEntryData;

        }

        public function getCashInflowByMonths ($id) {

            $cashInflowMonthsArray = [];

            $stmt = $this->conn->query("SELECT SUM(value) 
                FROM tb_finances 
                WHERE type = 1
                AND users_id = $id
                group by YEAR(create_at),MONTH(create_at)
                order by YEAR(create_at),MONTH(create_at)"
            );
            $stmt->execute();
            $data = $stmt->fetchAll();
            
            foreach($data as $cashInflowMonth) {
                $cashInflowMonthsArray[] = $cashInflowMonth; 
            }

            return $cashInflowMonthsArray;

        }

        public function getCashOutflowByMonths ($id) {

            $cashOutflowMonthsArray = [];

            $stmt = $this->conn->query("SELECT SUM(value) from tb_finances 
                WHERE type = 2
                AND users_id = $id
                group by YEAR(create_at),MONTH(create_at)
                order by YEAR(create_at),MONTH(create_at)");
            $stmt->execute();
            $data = $stmt->fetchAll();
            
            foreach($data as $cashOutflowMonth) {
                $cashOutflowMonthsArray[] = $cashOutflowMonth; 
            }

            return $cashOutflowMonthsArray;

        }

        public function findById($id) {

        }

        public function create(FinancialMoviment $financialMoviment) {

            $stmt = $this->conn->prepare("INSERT INTO tb_finances (
                id, description, obs, value, type, expense, category, scheduled, create_at, users_id
            ) VALUES(
                :id, :description, :obs, :value, :type, :expense, :category, :scheduled, :create_at, :users_id
            )");

            $stmt->bindParam(':id', $financialMoviment->id);
            $stmt->bindParam(':description', $financialMoviment->description);
            $stmt->bindParam(':obs', $financialMoviment->obs);
            $stmt->bindParam(':value', $financialMoviment->value);
            $stmt->bindParam(':type', $financialMoviment->type);
            $stmt->bindParam(':expense', $financialMoviment->expense);
            $stmt->bindParam(':category', $financialMoviment->category);
            $stmt->bindParam(':scheduled', $financialMoviment->scheduled);
            $stmt->bindParam(':create_at', $financialMoviment->create_at);
            $stmt->bindParam(':users_id', $financialMoviment->users_id);
            
            $stmt->execute();
            
            $type_moviment = "";

            $financialMoviment->type == 1 ? $type_moviment = "Entrada" : $type_moviment = "Saída";

            $this->message->setMessage("$type_moviment registrada com sucesso!", "success", "back");

        }

        public function update(FinancialMoviment $financialMoviment) {

            $stmt = $this->conn->prepare("UPDATE tb_finances SET
                description = :description,
                obs = :obs,
                value = :value,
                expense = :expense,
                category = :category,
                update_at = now() 
                WHERE id = :id
            ");

            $stmt->bindParam(":description", $financialMoviment->description);
            $stmt->bindParam(":obs", $financialMoviment->obs);
            $stmt->bindParam(":value", $financialMoviment->value);
            $stmt->bindParam(":expense", $financialMoviment->expense);
            $stmt->bindParam(":category", $financialMoviment->category);
            $stmt->bindParam(":id", $financialMoviment->id);

            if($stmt->execute()) {
            $this->message->setMessage("Registro alterado com sucesso!", "success", "back");
            }

        }

        public function destroy($id) {

            // Checa se existe id
            if ($id) {
                
                $stmt = $this->conn->prepare("DELETE FROM tb_finances WHERE id = :id");
                $stmt->bindParam(":id", $id);

                if ($stmt->execute()) {
                    $this->message->setMessage("Registro excluído com sucesso!", "success", "back");
                }

            }

        }

        public function checkGraphicDataMonths($id) {

            /* 
            Script para caso o usuário tenha se cadastrado mesês após janeiro
            As entradas e saídas que antecedem o mês atual do sistema
            Serão preenchidos com 0 para o perfeito funcionamento do gráfico anual na dashboard 
            */

            $ano = date("Y");
            $m = date("m");
            $m = intval($m);

            for($mes = $m - 1; $mes > 0; $mes--):

                # -------------- Entradas ------------------------- #
                // Query a partir do mês atual em forma descrente ex: 03, 02, 01
                $stmt = $this->conn->query("SELECT SUM(value) as sum FROM tb_finances WHERE MONTH(create_at) = $mes AND users_id = $id AND TYPE = 1");
                $stmt->execute();
                $entradas = $stmt->fetchAll();

                // Itera o array que vem do BD checando a soma de entradas em cada mês
                foreach ($entradas as $row){
                    $value_entradas = $row["sum"];
                
                    // Se o array retornar vazio (null) neste Mês não há registros
                    // Então insere um registro padrão com valor 0
                
                    if (!$value_entradas == null || !empty($value_entradas)) {
                        //echo "mes $mes possui valor <br>";
                    }else {
                        // echo "mes $mes não possui valor <br>";
                        $stmt = $this->conn->prepare("INSERT INTO tb_finances (
                            description, value, type, create_at, users_id
                        ) VALUES(
                            :description, :value, :type, :create_at, :users_id
                        )");
                        $description = "Não houve registros nesse mês";
                        $value = 1;
                        $type = 1;
                        $create_at = "$ano-$mes-10 10:00:10";

                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':value', $value);
                        $stmt->bindParam(':type', $type);
                        $stmt->bindParam(':create_at', $create_at);
                        $stmt->bindParam(':users_id', $id);
                        $stmt->execute();

                    }
                }

                #-------------------- Saídas ------------------------------------#
                $stmt = $this->conn->query("SELECT SUM(value) as sum FROM tb_finances WHERE MONTH(create_at) = $mes AND users_id = $id AND TYPE = 2");
                $stmt->execute();
                $saidas = $stmt->fetchAll();
                
                foreach ($saidas as $row){
                    $value_saidas = $row["sum"];
            
                    // Se o array retornar vazio (null) neste Mês não há registros
                    // Então insere um registro padrão com valor 0
                    if (!$value_saidas == null || !empty($value_saidas)) {
                        //echo "mes $mes possui valor <br>";
                    }else {
                        //echo "mes $mes não possui valor <br>";
                        $stmt = $this->conn->prepare("INSERT INTO tb_finances (
                            description, value, type, create_at, users_id
                        ) VALUES(
                            :description, :value, :type, :create_at, :users_id
                        )");
                        $description = "Não houve registros nesse mês";
                        $value = 1;
                        $type = 2;
                        $create_at = "$ano-$mes-10 10:00:10";
            
                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':value', $value);
                        $stmt->bindParam(':type', $type);
                        $stmt->bindParam(':create_at', $create_at);
                        $stmt->bindParam(':users_id', $id);
                        $stmt->execute();
                    }
                }
        
            endfor;

        }

}