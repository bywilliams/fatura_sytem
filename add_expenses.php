<?php
require_once("templates/header_iframe.php");
require_once("globals.php");
require_once("connection/conn.php");
require_once("dao/CategorysDAO.php");
require_once("dao/FinancialMovimentDAO.php");

// Traz todas as categorias disponiceis para entradas
$categorysDao = new CategorysDAO($conn);
$entry_categorys = $categorysDao->getAllEntryCategorys();

$financialMovimentDao = new FinancialMovimentDAO($conn, $BASE_URL);

$entryFinancialScheduled = $financialMovimentDao->getAllCashInflowScheduled($userData->id);

?>



<div class="container">
    <h1 class="text-center my-5">Despesas <i class="fa-solid fa-calendar-minus text-danger"></i></i></h1>

    <!-- Cash Inflow | Cash outflow form  -->
    <section>
       
        <div class="actions p-5 mb-4 bg-light rounded-3 shadow-sm">
            <form action="<?= $BASE_URL ?>moviment_process.php" method="post">
                <input type="hidden" name="type" value="create">
                <input type="hidden" name="type_action" value="1">
                <div class="row">
                    <div class="col-md-4">
                        <h4 class="font-weight-normal">Descriçao</h4>
                        <input type="text" name="description" id="description" class="form-control" placeholder="Ex: salário">
                    </div>
                    <div class="col-md-4">
                        <h4 class="font-weight-normal">Valor</h4>
                        <input type="text" name="value" id="value" class="form-control money" placeholder="Ex: 80,00:">
                    </div>
                    <!-- <div class="col-md-3" id="category">
                        <h4 class="font-weight-normal">Registrada em</h4>
                        <input class="form-control" type="date" name="" id="">
                    </div> -->
                    <div class="col-md-3">
                        <h4>Data da despesa</h4>
                        <input class="form-control " type="date" name="date_scheduled" id="">
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

    <h4 class="font-weight-normal mt-5">Últimas 10 despesas</h4>

    <?php if (count($entryFinancialScheduled) > 0) : ?>
        <div class="table_report" id="table_report_entry">
            <table class="table table-bordered table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Registrada em</th>
                        <th scope="col">Data da despesa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entryFinancialScheduled as $entryFinancialMovimentItem) : ?>
                        <?php $value = str_replace('.', '', $entryFinancialMovimentItem->value);
                        $total_entry_value += (float) $value; ?>

                        <tr>
                            <td scope="row">
                                <?= $entryFinancialMovimentItem->id ?>
                            </td>
                            <td>
                                <?= $entryFinancialMovimentItem->description ?>
                            </td>
                            <td>
                                <?= $entryFinancialMovimentItem->value ?>
                            </td>
                            <td>
                                25/07/2023
                            </td>
                            <td>
                                <?= $entryFinancialMovimentItem->update_at ?>
                            </td>
                            <!-- <td>
                                <?= $entryFinancialMovimentItem->category ?>
                            </td> -->
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
                <h5 class="py-2 text-center text-info">Ainda não há receitas agendadas.</h5>
            </div>
        </div>
    <?php endif ?>

</div>

    <?php require_once("templates/footer.php"); ?>