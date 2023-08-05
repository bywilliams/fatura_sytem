<?php
require_once("templates/header_iframe.php");
require_once("utils/config.php");
require_once("dao/ExpenseDAO.php");
require_once("dao/UserDAO.php");

$uersDao = new UserDAO($conn, $BASE_URL);
$allUsers = $uersDao->findAllUsers();

$expensesDao = new ExpenseDAO($conn, $BASE_URL);

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
$user_expense =
$month_expense = "";


if ($_POST) {
    //echo "pesquisa enviada";
    $sql = "";
    $totalRegistros = 0;

    if (isset($_POST['expense_id']) && $_POST['expense_id'] != '') { 
        $expense_id = $_POST['expense_id'];
        $sql .= "AND tb_expenses.id = $expense_id";
    }

    if (isset($_POST['name_expense']) && $_POST['name_expense'] != '') {
        $name_expense = $_POST['name_expense'];
        $sql .= " AND description LIKE '%%$name_expense%%'";
    }

    if (isset($_POST['user_expense']) && $_POST['user_expense'] != '') {
        $user_expense = $_POST['user_expense'];
        $sql .= " AND user_id = $user_expense";
    }

    if (isset($_POST['month_expense']) && $_POST['month_expense'] != '') { 
        $month_expense_input = $_POST['month_expense'];
        $month_querie = substr($_POST['month_expense'], -2);
        $sql .= " AND MONTH(dt_registered) = '$month_querie' ";
    }

    //echo $sql . "<br>";
}

// Traz total de saídas do usuário default ou com paginação
$expensesUser = $expensesDao->getAllExpensesAdminToPagination( $sql, $resultsPerPage, $offset);
//print_r($expensesUser);
$total_out_value = 0;

?>

<div class="container-fluid">
    <h1 class="text-center my-5">Despesas dos funcionários <img src="<?= $BASE_URL ?>assets/home/dashboard-main/empty-wallet.png" width="64" height="64" alt=""></h1>
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
                        <h4 class="font-weight-normal">Por funcionário:</h4>
                        <select class="form-control" name="user_expense" id="user_expense">
                            <option value="">Selecione</option>
                            <?php foreach($allUsers as $user): ?>
                                <option value="<?= $user->id ?>"><?= $user->getFullName($user) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por mês:</h4>
                        <input type="month" name="month_expense" id="month_expense" class="form-control" value="<?= $month_expense_input ?>">
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
                    <th scope="col">Funcionário</th>
                    <th scope="col" class="report-action">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expensesUser as $expense) : 
                    $value = str_replace('.', '', $expense->value);
                    $total_out_value += (float) $value; 
                    ?>
                    <tr>
                        <th scope="row"><?= $expense->id ?></th>
                        <td><?= $expense->description ?></td>
                        <td><?= $expense->value ?></td>
                        <td><?= $expense->dt_registered ?></td>
                        <td><?= $expense->dt_expense ?></td>
                        <td>
                            <?= $expense->user_name ?>
                        </td>
                        <td id="latest_moviments" class="report-action"><a href="#" data-toggle="modal" data-target="#expenseEditModal<?= $expense->id ?>" title="Editar">
                                <i class="fa-solid fa-file-pen"></i></a>
                            <a href="#" data-toggle="modal" data-target="#modal_del_expense<?= $expense->id ?>" title="Deletar"><i class="fa-solid fa-trash-can"></i></a>
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
        <?php if($totalRegistros > 10): ?>
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
        <?php endif ?>
    </div>
    <!-- table div thats receive all expenses without customize inputs parameters  -->

    <!-- Expense expense moviment Edit modal -->
    <?php foreach ($expensesUser as $expense) : ?>
        <div class="modal fade" id="expenseEditModal<?= $expense->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar <?= decryptData($expense->description, $encryptionKey) ?></h5>
                        <button type="button" class="close_reports" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= $BASE_URL ?>expense_process.php?id=<?= $expense->id ?>" method="post">
                            <input type="hidden" name="type" value="update">
                            <input type="hidden" name="id" value="<?= $expense->id ?>">
                            <div class="form-group">
                                <label for="description">Descriçao:</label>
                                <input type="text" name="description" id="" class="form-control" placeholder="Insira uma nova descrição" value="<?= decryptData($expense->description, $encryptionKey) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="value">Valor:</label>
                                <input type="text" name="value" id="" class="form-control money" placeholder="Insira um novo valor" value="<?= $expense->value ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="obs">Data da despesa:</label>
                                <input type="date" class="form-control" name="date_expense" id="date_expense" value="<?= date("Y-m-d", strtotime(decryptData($expense->dt_expense, $encryptionKey))) ?>" required>
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
    <!-- End Expense moviment Edit modal -->

    <!-- Modal para confirmação de exclusão de registro financeiro -->
    <?php foreach ($expensesUser as $expense) : ?>
        <div class="modal fade" tabindex="-1" id="modal_del_expense<?= $expense->id ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <p>Tem certeza que deseja excluir o registro?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                        <form action="<?= $BASE_URL ?>expense_process.php" method="POST">
                            <input type="hidden" name="type" value="delete">
                            <input type="hidden" name="id" value="<?= $expense->id ?>">        
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