<?php
require_once("templates/header_iframe.php");
require_once("globals.php");
require_once("utils/config.php");
require_once("connection/conn.php");
require_once("dao/ExpenseDAO.php");
require_once("dao/FinancialMovimentDAO.php");


// Traz despesas do usuário
$expenseDao = new ExpenseDAO($conn, $BASE_URL);
$expensesUser = $expenseDao->getAllUserExpense($userData->id);

// Sessions
isset($_SESSION['description']) ? $_SESSION['description'] : "";
isset($_SESSION['value']) ? $_SESSION['value'] : "";
isset($_SESSION['date_expense']) ? $_SESSION['date_expense'] : "";

?>

<div class="container">
    <h1 class="text-center my-5">Cadastrar Despesas <i class="fa-solid fa-calendar-minus text-danger"></i></i></h1>

    <!-- Cash Inflow | Cash outflow form  -->
    <section>
       
        <div class="actions p-5 mb-4 bg-light rounded-3 shadow-sm">
            <form action="<?= $BASE_URL ?>expense_process.php" method="post">
                <input type="hidden" name="type" value="create">
                <input type="hidden" name="type_action" value="1">
                <div class="row">
                    <div class="col-md-4">
                        <h4 class="font-weight-normal">Descriçao</h4>
                        <input type="text" name="description" id="description" class="form-control" placeholder="Ex: salário" value="<?= $_SESSION['description'] ?>" required>
                    </div>
                    <div class="col-md-4">
                        <h4 class="font-weight-normal">Valor</h4>
                        <input type="text" name="value" id="value" class="form-control money" placeholder="Ex: 80,00:" value="<?= $_SESSION['value'] ?>" required>
                    </div>
                    <!-- <div class="col-md-3" id="category">
                        <h4 class="font-weight-normal">Registrada em</h4>
                        <input class="form-control" type="date" name="" id="">
                    </div> -->
                    <div class="col-md-3">
                        <h4>Data da despesa</h4>
                        <input class="form-control " type="date" name="date_expense" id="date_expense" value="<?= $_SESSION['date_expense'] ?>" required>
                    </div>
                    <div class="col-md-1 button">
                        <label for="submit">
                            <i class="fa-regular fa-square-plus fa-3x" title="Adicionar"></i>
                        </label>
                        <input type="submit" id="submit" value="">
                    </div>
                </div>
                <div class="row">

                </div>
            </form>
        </div>
    </section>
    <!-- Cash Inflow | Cash outflow form  -->

    <h4 class="font-weight-normal mt-5">Últimas 10 despesas do mês</h4>

    <?php if (count($expensesUser) > 0) : ?>
        <div class="table_report" id="table_report_entry">
            <table class="table table-bordered table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Registrada em</th>
                        <th scope="col">Data da despesa</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expensesUser as $expense) : 
                    
                            // Código abaixo soma os valores das despesas
                            $value = str_replace('.', '', $expense->value);
                            $total_entry_value += (float) $value;?>

                        <tr>
                            <td scope="row">
                                <?= $expense->id ?>
                            </td>
                            <td>
                                <?= $expense->description ?>
                            </td>
                            <td>
                                <?= $expense->value ?>
                            </td>
                            <td>
                                <?= $expense->dt_registered ?>
                            </td>
                            <td>
                                <?= $expense->dt_expense ?>
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
                        <td colspan="8"> <strong> Total: </strong> R$
                            <?= number_format($total_entry_value, 2, ",", "."); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php else : ?>
        <div class="col-md-12">
            <div iv class=" bg-light rounded-3 shadow-sm my-3 py-3">
                <h5 class="py-2 text-center text-info">Ainda não há despesas cadastradas.</h5>
            </div>
        </div>
    <?php endif ?>

</div>

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

    <?php require_once("templates/footer.php"); ?>