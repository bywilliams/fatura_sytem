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
        public function popupInvoice(); // traz dados dos popups
        

    }
