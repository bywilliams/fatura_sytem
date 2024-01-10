<?php 
require_once("models/Menu.php");

Class MenuDAO implements MenuDAOInterface{

    private $conn;
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function buildMenu($data){

        $menu = new Menu();

        $menu->id_menu = isset($data['idmenu']) ? $data['idmenu'] : null;
        $menu->class_icon = isset($data['class_icon']) ? $data['class_icon'] : null;
        $menu->url = isset($data['url']) ? $data['url'] : null;
        $menu->sub_menu = isset($data['sub_menu']) ? $data['sub_menu'] : null;
        $menu->menu_name = isset($data['menu_name']) ? $data['menu_name'] : null;
        $menu->show_item = isset($data['show_item']) ? $data['show_item'] : null;
        $menu->main_menu_id = isset($data['main_menu_id']) ? $data['main_menu_id'] : null ;
        $menu->class_icon_submenu = isset($data['class_icon_submenu']) ? $data['class_icon_submenu'] : null;
        $menu->url_submenu = $data['url_submenu'];
        $menu->submenu_name = $data['submenu_name'];

        return $menu;
    }

    public function findMenu() {
        $menus = [];

        $stmt = $this->conn->query("SELECT 
        idmenu, class_icon, url, menu_name, main_menu_id, show_item, sub_menu, class_icon_submenu, url_submenu, submenu_name  
        FROM menu 
        WHERE menu_name IS NOT NULL AND show_item = 'S' 
        ORDER BY idmenu"
        );        
        $stmt->execute();
       

        if ($stmt->rowCount() > 0) {

            $data = $stmt->fetchAll();
            
            foreach ($data as $item_menu){
                $menus[] = $this->buildMenu($item_menu);
            }
            return $menus;
    
        }

    }

    public function findSubMenu(int $id) {

        $subMenus = [];
        
        $stmt = $this->conn->query("SELECT 
        class_icon_submenu, url_submenu, submenu_name, main_menu_id 
        FROM menu 
        WHERE main_menu_id = $id ORDER BY idmenu ASC"
        );
        
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
           
            $data = $stmt->fetchAll();

            foreach ($data as $item_menu){
                $subMenus[] = $this->buildMenu($item_menu);
            }
           
        }
        return $subMenus;

    }

}   
