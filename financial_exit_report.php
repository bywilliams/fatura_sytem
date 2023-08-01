<?php
require_once("templates/header_iframe.php");
require_once("utils/config.php");
require_once("dao/ExpenseDAO.php");

$expensesDao = new ExpenseDAO($conn, $BASE_URL);

// // Traz os registros de saída da query personalizada 
// $sql = "";
// $getOutReports = $expensesDao->getReports($sql,  $userData->id);

/* paginação do relatório  */
$totalRegistros = $expensesDao->countTypeExpensesCurrentMonth($userData->id);

$resultsPerPage = 10;
$numberPages = ceil($totalRegistros / $resultsPerPage);

// Pega numero da página atual
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
// calcula o indice do primeiro registro da página atual
$offset = ($page - 1) * $resultsPerPage;

$sql = "";
$expense_id = 
$name_expense = 
$value_expense =
$month_expense = "";


if ($_POST) {
    //echo "pesquisa enviada";
    $sql = "";

    if (isset($_POST['expense_id']) && $_POST['expense_id'] != '') { 
        $expense_id = $_POST['expense_id'];
        $sql .= "AND id = $expense_id";
    }

    if (isset($_POST['name_expense']) && $_POST['name_expense'] != '') {
        $name_expense = $_POST['name_expense'];
        $name_expense_encrypted = encryptData($name_expense, $encryptionKey);
        $sql .= " AND description LIKE '%$name_expense_encrypted%'";
    }

    if (isset($_POST['value_expense']) && $_POST['value_expense'] != '') {
        $value_expense = $_POST['value_expense'];
        $sql .= " AND value <= $value_expense";
    }

    if (isset($_POST['month_expense']) && $_POST['month_expense'] != '') { 
        $month_expense = $_POST['month_expense'];
        
        $month_querie = substr($_POST['month_expense'], -2);
        $month_expense_final = encryptData($month_querie, $encryptionKey);
        $sql .= " AND month_reference LIKE '%%$month_expense_final%%' ";
    }

   // echo $sql . "<br>";
}

//echo $expense_id, $name_expense, $value_expense, $month_expense;

// Traz total de saídas do usuário default ou com paginação
$expensesUser = $expensesDao->getAllExpensesToPagination($userData->id, $sql, $resultsPerPage, $offset);
$total_out_value = 0;

?>

<div class="container-fluid">
    <h1 class="text-center my-5">Despesas <img src="<?= $BASE_URL ?>assets/home/dashboard-main/empty-wallet.png" width="64" height="64" alt=""></h1>
    <div class="entrys-search" id="entrys-search">
        <form method="POST">
            <input type="hidden" name="user_id" id="user_id" value="<?= $userData->id ?>">
            <div class="row offset-sm-1">
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por id:</h4>
                        <input type="number" name="expense_id" id="expense_id" class="form-control" placeholder="Ex: 10" value="<?= $expense_id ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por nome:</h4>
                        <input type="text" name="name_expense" id="name_expense" class="form-control" placeholder="Ex: salário" value="<?= $name_expense ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por valor:</h4>
                        <select class="form-control" name="value_expense" id="value_expense">
                            <option value="">Selecione</option>
                            <option value="100" <?= $value_expense == 100 ? 'selected' : "" ?>>até R$ 100,00</option>
                            <option value="1000" <?= $value_expense == 1000 ? 'selected' : "" ?>>até R$ 1.000,00</option>
                            <option value="3000" <?= $value_expense == 3000 ? 'selected' : "" ?>>até R$ 3.000,00</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por mês:</h4>
                        <input type="month" name="month_expense" id="month_expense" class="form-control" value="<?= $month_expense ?>">
                    </div>
                </div>
                <div class="col-md-1">
                    <input class="btn btn-lg btn-success" type="submit" value="Buscar">
                    <!-- <button class="btn btn-lg btn-secondary" id="print_btn" onclick="print()"> Imprimir</button> -->
                </div>
            </div>
        </form>
    </div>

    <div class="table_report my-3" id="search_exit"></div>

    <!-- table div thats receive all expenses without customize inputs parameters  -->
    <div class="table_report" id="table_report_exit">
        <h3 class="text-center text-secondary">Resultados:</h3>
        <table class="table table-hover table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Registrada em</th>
                    <th scope="col">Data da despesa</th>
                    <th scope="col" class="report-action">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expensesUser as $expense) : 
                    // Bloco a seguir decripta as datas e converte o formato
                    $dt_registered = decryptData($expense->dt_registered, $encryptionKey);
                    $dt_expense = decryptData($expense->dt_expense, $encryptionKey);
                    $dt_registered_final = date("d-m-Y H:i:s", strtotime($dt_registered));
                    $dt_expense_final = date("d-m-Y", strtotime($dt_expense));

                    $value = str_replace('.', '', $expense->value);
                    $total_out_value += (float) $value; 
                    ?>
                    <tr>
                        <th scope="row"><?= $expense->id ?></th>
                        <td><?= decryptData($expense->description, $encryptionKey) ?></td>
                        <td><?= $expense->value ?></td>
                        <td><?= $dt_registered_final ?></td>
                        <td><?= $dt_expense_final ?></td>
                        <td id="latest_moviments" class="report-action"><a href="#" data-toggle="modal" data-target="#exampleModalCenter<?= $expense->id ?>" title="Editar">
                                <i class="fa-solid fa-file-pen"></i></a>
                            <a href="#" data-toggle="modal" data-target="#modal_del_finance_moviment<?= $expense->id ?>" title="Deletar"><i class="fa-solid fa-trash-can"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8"> <strong> Total: </strong> R$ <?= number_format($total_out_value, 2, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
        <!-- Pagination buttons -->
        <div class="row justify-content-center">
            <nav aria-label="...">
                <ul class="pagination pagination-lg">
                    <?php for ($i = 1; $i <= $numberPages; $i++): ?>
                        <?php $active = ($i == $page) ? "active-pagination" : ""; ?>

                        <li class="page-item <?=$active?>">
                            <a class="page-link" href="<?= $BASE_URL ?>financial_exit_report.php?page=<?= $i ?>" tabindex="-1"><?= $i ?></a>
                        </li>

                    <?php endfor ?>
                </ul>
            </nav>
        </div>
         <!-- End pagination buttons -->
    </div>
    <!-- table div thats receive all expenses without customize inputs parameters  -->

    <!-- Finance all expense moviment modal -->
    <?php foreach ($outFinancialMoviments as $outFinancialMovimentItem) : ?>
        <div class="modal fade" id="exampleModalCenter<?= $outFinancialMovimentItem->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Movimentação</h5>
                        <button type="button" class="close_reports" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= $BASE_URL ?>moviment_process.php?id=<?= $outFinancialMovimentItem->id ?>" method="post">
                            <input type="hidden" name="type" value="edit">
                            <div class="form-group">
                                <label for="description">Descriçao:</label>
                                <input type="text" name="description_edit" id="" class="form-control" placeholder="Insira uma nova descrição" value="<?= $outFinancialMovimentItem->description ?>">
                            </div>
                            <div class="form-group">
                                <label for="value">Valor:</label>
                                <input type="text" name="value_edit" id="" class="form-control money" placeholder="Insira um novo valor" value="<?= $outFinancialMovimentItem->value ?>">
                            </div>
                            <?php if ($outFinancialMovimentItem->type == 2) : ?>
                                <div class="form-group">
                                    <label for="expense_type">Despesa:</label>
                                    <?php if ($outFinancialMovimentItem->expense == "Fixa") : ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio1" value="F" checked>
                                            <label class="edit_moviment_label" for="inlineRadio1">Fixa</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio2" value="V">
                                            <label class="edit_moviment_label" for="inlineRadio2">Variavel</label>
                                        </div>
                                    <?php else : ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio1" value="F">
                                            <label class="edit_moviment_label" for="inlineRadio1">Fixa</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio2" value="V" checked>
                                            <label class="edit_moviment_label" for="inlineRadio2">Variavel</label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="category">Categoria:</label>
                                    <select name="category_edit" id="" class="form-control">
                                        <?php foreach ($exit_categorys as $category) : ?>
                                            <?php if ($category->category_name == $outFinancialMovimentItem->category) : ?>
                                                <option value="<?= $category->id ?>" selected> <?= $category->category_name ?></option>
                                            <?php else : ?>
                                                <option value="<?= $category->id ?>"> <?= $category->category_name ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else : ?>
                                <div class="form-group">
                                    <label for="category">Categoria:</label>
                                    <select name="category_edit" id="" class="form-control">
                                        <?php foreach ($entry_categorys as $category) : ?>
                                            <?php if ($category->category_name == $outFinancialMovimentItem->category) : ?>
                                                <option value="<?= $category->id ?>" selected> <?= $category->category_name ?></option>
                                            <?php else : ?>
                                                <option value="<?= $category->id ?>"> <?= $category->category_name ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="obs">Observação:</label>
                                <textarea class="form-control" name="obs" id="obs" rows="5" placeholder="Adicione uma observação..."><?= $outFinancialMovimentItem->obs ?></textarea>
                            </div>
                            <input type="submit" value="Enviar" class="btn btn-lg btn-success">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- End Finance moviment modal -->

    <!-- Finance customize expense moviment modal -->
    <?php foreach ($getOutReports as $customizeFinancialMovimentItem) : ?>
        <div class="modal fade" id="customizeExpenseQuery<?= $customizeFinancialMovimentItem->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Movimentação</h5>
                        <button type="button" class="close_reports" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= $BASE_URL ?>moviment_process.php?id=<?= $customizeFinancialMovimentItem->id ?>" method="post">
                            <input type="hidden" name="type" value="edit">
                            <div class="form-group">
                                <label for="description">Descriçao:</label>
                                <input type="text" name="description_edit" id="" class="form-control" placeholder="Insira uma nova descrição" value="<?= $customizeFinancialMovimentItem->description ?>">
                            </div>
                            <div class="form-group">
                                <label for="value">Valor:</label>
                                <input type="text" name="value_edit" id="" class="form-control money" placeholder="Insira um novo valor" value="<?= $customizeFinancialMovimentItem->value ?>">
                            </div>
                            <?php if ($customizeFinancialMovimentItem->type == 2) : ?>
                                <div class="form-group">
                                    <label for="expense_type">Despesa:</label>
                                    <?php if ($customizeFinancialMovimentItem->expense == "F") : ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio1" value="F" checked>
                                            <label class="edit_moviment_label" for="inlineRadio1">Fixa</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio2" value="V">
                                            <label class="edit_moviment_label" for="inlineRadio2">Variada</label>
                                        </div>
                                    <?php else : ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio1" value="F">
                                            <label class="edit_moviment_label" for="inlineRadio1">Fixa</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio2" value="V" checked>
                                            <label class="edit_moviment_label" for="inlineRadio2">Váriavel</label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="category">Categoria:</label>
                                    <select name="category_edit" id="" class="form-control">
                                        <?php foreach ($exit_categorys as $category) : ?>
                                            <?php if ($category->category_name == $financialMoviment->category) : ?>
                                                <option value="<?= $category->id ?>" selected> <?= $category->category_name ?></option>
                                            <?php else : ?>
                                                <option value="<?= $category->id ?>"> <?= $category->category_name ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else : ?>
                                <div class="form-group">
                                    <label for="category">Categoria:</label>
                                    <select name="category_edit" id="" class="form-control">
                                        <?php foreach ($entry_categorys as $category) : ?>
                                            <?php if ($category->category_name == $financialMoviment->category) : ?>
                                                <option value="<?= $category->id ?>" selected> <?= $category->category_name ?></option>
                                            <?php else : ?>
                                                <option value="<?= $category->id ?>"> <?= $category->category_name ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="obs">Observação:</label>
                                <textarea class="form-control" name="obs" id="obs" rows="5" placeholder="Adicione uma observação..."><?= $financialMoviment->obs ?></textarea>
                            </div>
                            <input type="submit" value="Enviar" class="btn btn-lg btn-success">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- End Finance moviment modal -->

    <!-- Modal para confirmação de exclusão de registro financeiro -->
    <?php foreach ($getOutReports as $financialMoviment) : ?>
        <div class="modal" tabindex="-1" id="modal_del_finance_moviment<?= $financialMoviment->id ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <p>Tem certeza que deseja excluir o registro?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                        <form action="<?= $BASE_URL ?>moviment_process.php" method="POST">
                            <input type="hidden" name="type" value="deletar">
                            <input type="hidden" name="id" value="<?= $financialMoviment->id ?>">        
                            <button type="submit" class="btn btn-primary">Sim</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- Fim Modal para cofnirmação de exclusão de registro financeiro -->
</div>

<?php require_once("templates/footer.php"); ?>

<script>
$(document).ready(function(){
    $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
});

</script>

<script src="js/ajax_finance_expense_request.js"></script>