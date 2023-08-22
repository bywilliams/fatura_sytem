<?php 
    require_once("models/Reminders.php");
    require_once("globals.php");
    require_once("models/Message.php");


    class RemindersDAO implements RemindersDAOInterface {

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildReminder($data){

            $reminder = new Reminders();

            $reminder->id = $data['id'];
            $reminder->title = $data['title'];
            $reminder->description = $data['description'];
            $reminder->reminder_date = date("d-m-Y", strtotime($data['reminder_date']));
            $reminder->visualized = $data['visualized'];
            $reminder->users_id = $data['users_id'];
            $reminder->created = $data['created'];
            $reminder->modified = $data['modified'];
            
            return $reminder;

        }

        public function getAllReminders($data){

            $reminders = [];

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->prepare("SELECT 
            id, title, description, reminder_date, users_id, created, modified, visualized
            FROM tb_reminders 
            WHERE created BETWEEN '$initial_date' AND '$current_date' AND users_id = :users_id 
            ORDER BY reminder_date ASC");
            $stmt->bindParam(":users_id", $data);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                
                $data = $stmt->fetchAll();
                
                foreach($data as $reminder) {
                    $reminders[] = $this->buildReminder($reminder);
                }
            }

            return $reminders;

        } 

        public function getLatestReminders($data) {

            $latestReminders = [];

            // defini o período do dia 01 ao dia atual do sistema
            $initial_date = date("Y-m-01 00:00:00");
            $current_date = date("Y-m-d H:i:s");

            $stmt = $this->conn->prepare("SELECT 
            id, title, description, reminder_date, visualized, users_id, created, modified 
            FROM tb_reminders 
            WHERE created BETWEEN '$initial_date' AND '$current_date' AND users_id = :users_id 
            ORDER BY reminder_date DESC LIMIT 4");
            $stmt->bindParam(":users_id", $data);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetchAll();

                foreach($data as $reminder) {
                    $latestReminders[] = $this->buildReminder($reminder);
                }
                
            }

            return $latestReminders;

        }

        public function getTodayReminders($data) {

            $todayReminders = [];
        
            // defini o período do dia 01 ao dia atual do sistema
            $current_date = date("Y-m-d");

            $stmt = $this->conn->prepare("SELECT 
            id, title, description, reminder_date, visualized, users_id, created, modified 
            FROM tb_reminders 
            WHERE reminder_date = '$current_date' AND users_id = :users_id 
            ");
            $stmt->bindParam(":users_id", $data);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetchAll();

                foreach($data as $reminder) {
                    $todayReminders[] = $this->buildReminder($reminder);
                }
                
            }

            return $todayReminders;

        }

        public function createReminder(Reminders $reminder){

            $stmt = $this->conn->prepare("INSERT INTO 
                tb_reminders (title, description, reminder_date, visualized, users_id, created) 
                VALUES (:title, :description, :reminder_date, 'N', :users_id, NOW())"
            );
            $stmt->bindParam(":title", $reminder->title);
            $stmt->bindParam(":description", $reminder->description);
            $stmt->bindParam(":reminder_date", $reminder->reminder_date);
            $stmt->bindParam(":users_id", $reminder->users_id);

            if($stmt->execute()) {
                $this->message->setMessage("<script>
                Swal.fire({
                    title: 'Informação',
                    text: ' Lembrete inserido com sucesso! ',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0B666A', 
                    cancelButtonText: 'Fechar',
                })
                ;</script>", "", "back");
            }
            

        }

        public function updateReminder(Reminders $reminder){
            $stmt = $this->conn->prepare("UPDATE tb_reminders SET
                title = :title, 
                description = :description,
                reminder_date = :reminder_date,
                visualized = :visualized,
                modified = NOW()
                WHERE users_id = :users_id AND id = :id
            "); 
            $stmt->bindParam(":title", $reminder->title);
            $stmt->bindParam(":description", $reminder->description);
            $stmt->bindParam(":reminder_date", $reminder->reminder_date);
            $stmt->bindParam(":visualized", $reminder->visualized);
            $stmt->bindParam(":users_id", $reminder->users_id);
            $stmt->bindParam(":id", $reminder->id);
            

            if ($stmt->execute()) {
                $this->message->setMessage("", "", "back");
            }            

        }

        public function destroyReminder($id){

            if ($id) {
                
                $stmt = $this->conn->prepare("DELETE FROM tb_reminders WHERE id = :id");
                $stmt->bindParam(":id", $id);

                if ($stmt->execute()) {
                    $this->message->setMessage("<script>
                    Swal.fire({
                        title: 'Informação',
                        text: ' Lembrete deletado com sucesso! ',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0B666A', 
                        cancelButtonText: 'Fechar',
                    })
                    ;</script>", "", "back");
                }

            }


        }

    }