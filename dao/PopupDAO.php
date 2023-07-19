<?php

require_once("models/Popup.php");
require_once("models/User.php");
require_once("models/Message.php");

    Class PopupDAO implements PopupDAOInterface {

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }


        public function buildPopup($data) {

            $popup = new Popup();

            $popup->id = $data['id'];
            $popup->popup_name = $data['popup_name'];
            $popup->title = $data['title'];
            $popup->description = $data['description'];
            $popup->image = $data['image'];
            $popup->created_at = $data['created_at'];
            $popup->modified = $data['modified'];

            return $popup;
        }

        public function popup($users_id) {

            $stmt = $this->conn->prepare("SELECT popup.id, popup_name, title, description, image, created_at, popup.modified 
            FROM popup INNER JOIN popup_users ON popup.id  = popup_users.popup_id 
            WHERE popup_users.show_popup = 'S' AND popup_users.status_visualized = 'N' 
            AND popup_users.expired_date > NOW() AND popup_users.users_id = $users_id");
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $popup = $this->buildPopup($data);

                return $popup;
            }
            
        }

        public function createPopup($users_id, $email, $popup_id) {

            $stmt = $this->conn->prepare("INSERT INTO popup_users (
                popup_id, show_popup, status_visualized, users_id, email, expired_date, created
            ) VALUES (
                :popup_id, 'S', 'N', :users_id, :email, NOW() + INTERVAL 10 DAY, now()
            )");
            $stmt->bindParam(":popup_id", $popup_id);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":users_id", $users_id);

            $stmt->execute();

        }

        public function updatePopupUser($popup_id, $users_id) {

            $stmt = $this->conn->prepare("UPDATE popup_users SET 
            show_popup = 'N', 
            status_visualized = 'S',
            modified = now() 
            WHERE popup_id = :popup_id AND users_id = :users_id 
            ");
            
            $stmt->bindParam(":popup_id", $popup_id);
            $stmt->bindParam(":users_id", $users_id);
            

            if($stmt->execute()) {
                $this->message->setMessage("", "", "back");
            }else {
                echo "error";
            }

        }

    }