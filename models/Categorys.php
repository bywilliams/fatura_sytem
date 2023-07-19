<?php 

    Class Categorys {

        public $id;
        public $category_name;
        public $class_icon;

    }

    interface CategorysDAOInterface {

        public function getAllEntryCategorys();
        public function getAllExitCategorys();
        public function getCategory($category_id);
        public function buildCategorys($data);

    }