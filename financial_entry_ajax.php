<?php 
require_once("templates/header_iframe.php"); 
require_once("dao/FinancialMovimentDAO.php");
$financialMovimentDao = new FinancialMovimentDAO($conn, $BASE_URL);

$sql = "";
$output = "";
$total = 0;

if (isset($_POST['name_search_entry']) && $_POST['name_search_entry'] != '') {
    $name_search_entry = $_POST['name_search_entry'];
    $sql .= " AND description LIKE '%$name_search_entry%'";
}

if (isset($_POST['values_entry']) && $_POST['values_entry'] != '') {
    $values_entry = $_POST['values_entry'];
    $sql .= " AND value <= $values_entry";
}

if (isset($_POST['from_date_entry']) && $_POST['from_date_entry'] != '' && isset($_POST['to_date_entry']) && $_POST['to_date_entry'] != '') {
    $formato = 'd/m/Y';
    $from_date_entry = DateTime::createFromFormat($formato, $_POST['from_date_entry']);
    $from_date_entry = $from_date_entry->format('Y-m-d');
    
    $to_date_entry = DateTime::createFromFormat($formato, $_POST['to_date_entry']);
    $to_date_entry = $to_date_entry->format('Y-m-d');

    $sql .= " AND create_at BETWEEN '$from_date_entry' AND '$to_date_entry 23:59:00'";
}

if (isset($_POST['category_entry']) && $_POST['category_entry'] != '') {
    $category_entry = $_POST['category_entry'];
    if($category_entry == 0){
        //$sql .= " AND category > $category_entry";
    }else {
        $sql .= " AND category = $category_entry";
    }
}

//echo $sql; exit;

// Traz o array com os dados da query personalizada 
$getEntryReports = $financialMovimentDao->getReports($sql, 1, $userData->id);

if (!empty($sql)) {
   
    if ($getEntryReports != null) {
        $output .= '
        <h3 class="text-center text-secondary">Resultados da pesquisa</h3>        
        <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Descrição</th>
                <th scope="col">Valor</th>
                <th scope="col">Data</th>
                <th scope="col">Categoria</th>
                <th scope="col">Observação</th>
                <th scope="col">Ação</th>
            </tr>
        </thead>
        <tbody>';
    
        foreach($getEntryReports as $item) {
           
            $value = str_replace('.', '', $item->value);
            $total += (float) $value;
            $obs = "";
            if($item->obs != ""):
                $obs = '<a href="#!" id="grupos'.$item->id.'" onclick="openTooltip('.$item->id.')"> <img src="'.$BASE_URL.'assets/home/dashboard-main/message_alert.gif" alt="message_alert" title="ver observação" width="33" height="30"> </a>
                <div class="tooltip_" id="tooltip_'.$item->id.'">
                    <div id="conteudo">
                        <div class="bloco" style="display: flex; justify-content: space-between">
                            <h5>Observação</h5>
                            <a href="#!" id="close'.$item->id.'"><i class="fa-solid fa-xmark"></i></a>
                        </div>
                        <div class="bloco">
                            <small>'.$item->obs.'</small>
                        </div>
                    </div>
                </div>';
            endif;

            $output .= '
            <tr>
                <th scope="row">'.$item->id.'</th>
                <td>'.$item->description.'</td>
                <td>'.$item->value.'</td>
                <td>'.$item->create_at.'</td>
                <td>'.$item->category.'</td>
                <td>'.$obs.'</td>
                <td id="latest_moviments"><a href="#" data-toggle="modal"
                    data-target="#exampleModalCenter'.$item->id.'" title="Editar">
                    <i class="fa-solid fa-file-pen"></i></a>
                <a href="#" data-toggle="modal" data-target="#modal_del_finance_moviment'.$item->id.'"
                        title="Deletar"><i class="fa-solid fa-trash-can"></i></a>
                </td>
            </tr>
            ';
        }
        echo $output . '
        <tfoot>
        <tr>
            <td colspan="8"> <strong> Total: R$</strong> '.number_format($total, 2, ',', '.').'</td>
        </tr>
        </tfoot>
    </table>';
    }else {
        echo "Nenhum registro encontrado, tente novamente";
    }
    
}

?>