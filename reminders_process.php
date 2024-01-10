<?php 

    require_once("globals.php");
    require_once("connection/conn.php");
    require_once("dao/RemindersDAO.php");
    require_once("dao/UserDAO.php");
    require_once("models/Reminders.php");
    require_once("models/Message.php");


    $message = new Message($BASE_URL);
    $reminderDao = new RemindersDAO($conn, $url);

    // resgata dados do usuário
    $userDao = new UserDAO($conn, $BASE_URL);
    $userData = $userDao->verifyToken();

    // Pega o tipo do form
    $type = filter_input(INPUT_POST, 'type');

    if($type == 'register'){

        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $reminder_date = filter_input(INPUT_POST, "reminder_date");
       
        
        $_SESSION['title'] = $title;
        $_SESSION['description'] = $description;
        $_SESSION['reminder_date'] = $reminder_date;

        if ($title && $description && $reminder_date) {

            $reminder = new Reminders();

            $reminder->title = $title;
            $reminder->description = $description;
            $reminder->reminder_date = $reminder_date;
            $reminder->users_id = $userData->id;

            try {

                $reminderDao->createReminder($reminder);
                $_SESSION['title'] = "";
                $_SESSION['description'] = "";
                $_SESSION['reminder_date'] = "";

            } catch (PDOException $e) {
                echo "Falha ao cadastrar o lembrete, consulte o administrador do sistema";
                // echo "Falha ao cadastrar o lembrete : {$e->getMessage()}";
            }

        } else {
            $message->setMessage("Preencha os campos titulo, descrição e data", "error", "back");
        }

    } else if($type == "edit"){

        $id = filter_input(INPUT_POST, "id");
        $title = filter_input(INPUT_POST, "title");
        $description = str_replace('"', '', filter_input(INPUT_POST, "description"));
        $reminder_date = filter_input(INPUT_POST, "reminder_date");
        $visualized = filter_input(INPUT_POST, "visualized");

        $reminder = new Reminders();
        $reminder->id = $id;
        $reminder->title = $title;
        $reminder->description = $description;
        $reminder->reminder_date = $reminder_date;
        $visualized != "" ? $reminder->visualized = $visualized :  "";
        $reminder->users_id = $userData->id;

        try {

            $reminderDao->updateReminder($reminder);
        
        } catch (PDOException $e) {
            echo "Falha ao atualizar lembrete, consulte o administrador do sistema.";
            // echo "Falha ao editar o lembrete : {$e->getMessage()}";
        }


    } else if ($type == 'delete') {
        
        $reminder_id = filter_input(INPUT_POST,  'id');
        $reminderDao->destroyReminder($reminder_id);
    }

?>