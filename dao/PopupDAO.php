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

        public function popupInvoice() {

            $stmt = $this->conn->prepare("SELECT id, popup_name, title, description, image, created_at, popup.modified 
            FROM popup 
            WHERE id = '1' 
            ");
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $popup = $this->buildPopup($data);

                return $popup;
            }
            
        }



    }