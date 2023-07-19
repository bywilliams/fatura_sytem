<?php 

    class Popup {
        public $id;
        public $popup_name;
        public $title;
        public $description;
        public $image;
        //public $show_popup;
        public $created_at;
        public $modified;
        public $expired_date;
    }

    interface PopupDAOInterface {

        public function buildPopup($data); // constroi objeto popup
        public function popup($users_id); // traz dados dos popups
        public function createPopup($users_id, $email, $popup_id); // insere popups para usuários novos
        public function updatePopupUser($pop_id, $users_id); // atualiza status de cada popup para usuário 

    }
