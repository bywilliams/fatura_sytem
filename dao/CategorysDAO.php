<?php 
require_once("models/Categorys.php");
    Class CategorysDAO implements CategorysDAOInterface{

        private $conn;

        public function __construct(PDO $conn){
            $this->conn = $conn;
        }

        public function buildCategorys($data) {

            $categorys = new Categorys();

            $categorys->id = $data['id'];
            $categorys->category_name = $data['category_name'];
            $categorys->class_icon = $data['class_icon'];

            return $categorys;
        }

        public function getAllEntryCategorys() {
            $categorys = [];

            $stmt = $this->conn->query("SELECT id, category_name, class_icon FROM finance_categorys WHERE category_type = 1");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $cateogrysArray = $stmt->fetchAll();

                foreach ($cateogrysArray as $category){

                    $categorys[] = $this->buildCategorys($category);
                
                }
            }
            return $categorys;
        }
        
        public function getAllExitCategorys() {
            $categorys = [];

            $stmt = $this->conn->query("SELECT id, category_name, class_icon FROM finance_categorys WHERE category_type = 2");

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $cateogrysArray = $stmt->fetchAll();

                foreach ($cateogrysArray as $category){

                    $categorys[] = $this->buildCategorys($category);
                
                }
            }
            return $categorys;
        }


        public function getCategory($category_id) {

            $category = "";

            $stmt = $this->conn->prepare("SELECT class_icon FROM finance_categorys WHERE id = :category_id");
            $stmt->bindParam(":category_id", $category_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                
                $category = $stmt->fetch();

            }
            return $category;
        }

        
    }