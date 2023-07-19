<?php 

    class Menu {
        public $id_menu;
        public $class_icon;
        public $url;
        public $sub_menu;
        public $menu_name;
        public $show_item;
        public $main_menu_id;
        public $class_icon_submenu;
        public $url_submenu;
        public $submenu_name;


    }


    interface MenuDAOInterface{
        public function buildMenu($data);
        public function findMenu(); 
        public function findSubMenu(int $id); 
    }
