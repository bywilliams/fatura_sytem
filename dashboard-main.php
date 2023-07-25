<?php
require_once("templates/header_iframe.php");
require_once("models/FinancialMoviment.php");
require_once("dao/FinancialMovimentDAO.php");
require_once("dao/RemindersDAO.php");
require_once("dao/CategorysDAO.php");
include_once("utils/hg_finance_api.php");

// Object Finance to acess propertys
$financialMoviment = new FinancialMoviment();
// Object DAO to FinancialMoviment
$financialMovimentDao = new FinancialMovimentDAO($conn, $BASE_URL);

// Object DAO to Category
$categorysDao = new CategorysDAO($conn);

// Traz todas as categorias disponiceis para receitas
$entry_categorys = $categorysDao->getAllEntryCategorys();

// Traz todas as categorias disponiceis para despesas
$exit_categorys = $categorysDao->getAllExitCategorys();

// Maior receita
$highValueIncome = $financialMovimentDao->getHighValueIncome($userData->id);

// Menor receita
$lowerValueIncome = $financialMovimentDao->getLowerValueIncome($userData->id);

// Maior despesa
$biggetsExpense = $financialMovimentDao->getBiggestExpense($userData->id);

// Menor despesa
$lowerExpense = $financialMovimentDao->getLowerExpense($userData->id);

// Traz as última movimentações do usuário
$latestFinancialMoviments = $financialMovimentDao->getLatestFinancialMoviment($userData->id);

// Traz total de entradas do usuário
$totalCashInflow = $financialMovimentDao->getAllCashInflow($userData->id);

// Traz total de saídas do usuário
$totalCashOutflow = $financialMovimentDao->getAllCashOutflow($userData->id);

// Pega o resultado da função que faz o calculo da % que as despesas representam sobre a receita
$resultExpensePercent = (float) $financialMoviment->balancePercent($totalCashInflow, $totalCashOutflow);

// Traz o balanço entre entradas e saídas do usuário
$total_balance = $financialMovimentDao->getTotalBalance($userData->id);

// Traz as entradas de cada mês até o mês atual para alimentar o gráfico
$cashInflowMonthsArray = $financialMovimentDao->getCashInflowByMonths($userData->id);

// Traz as saídas de cada mês até o mês atual para alimentar o gráfico
$cashOutflowMonthsArray = $financialMovimentDao->getCashOutflowByMonths($userData->id);

/*
Os operadores ternários para caso a Receita estiver vazia e Despesa com valor ou vice versa 
Saldo recebera o valor de algum deles, já que a operação da linha 41 não terá um resultado
*/
$totalCashInflow <= "0,00" ? $total_balance = -(float)$totalCashOutflow : $totalCashInflow;
$totalCashOutflow <= "0,00" ? $total_balance = $totalCashInflow : $totalCashOutflow;

// Balance color text depending revenue vs expenses result
$balance_color_text = "";
$total_balance > 0 ? $balance_color_text = "text-success" : $balance_color_text = "text-danger";

/*
O bloco de códigos abaixo confere se todos os meses anteriores ao atual possuem dados de entradas e saídas
para alimentação do gráfico, caso não tiver é feito um único registro com valor simbólico para entrar no gráfico 
 */
$current_month = date("m");
$countDataRevenueByMonths = count($cashInflowMonthsArray);
$countDataEpensesByMonths = count($cashOutflowMonthsArray);

if ($current_month != $countDataRevenueByMonths || $current_month != $countDataEpensesByMonths) {
    $financialMovimentDao->checkGraphicDataMonths($userData->id);
}

// traz os últimos 4 lembretes cadastrados pelo usuário 
$reminderDao = new RemindersDAO($conn, $BASE_URL);
$latestReminders = $reminderDao->getLatestReminders($userData->id);

?>

<body id="iframe-body">

    <div class="container-fluid">

        <!-- Balance Warning if expenses user is above 50% -->
        <!-- <div class="offset-md-3 col-md-6">
            <?php if ($resultExpensePercent > 50) : ?>
                <div class="mt-3" style="display: inline-flex">
                    <i class="fa-solid fa-triangle-exclamation fa-2x text-warning"></i>
                    <span class="warning-text-expense">
                        <strong>Cuidado despesas já são <?= $resultExpensePercent ?>% da sua renda!
                            Até 50% é o indicado para a saúde financeira.
                        </strong>
                    </span>
                </div>
            <?php endif; ?>
        </div> -->
        <!-- End Balance Warning if expenses user is above 50% -->

        <!-- Section Revenue, Enpenses , balance and shortcuts -->
        <section>
            <div class="card-div mb-3 my-3 text-center">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="card mb-3 ">
                            <div class="card-header">
                                <h4 class="my-0 font-weight-normal">Receita Mensal</h4>
                            </div>
                            <div class="card-body">
                                <h1 class="card-title pricing-card-title text-success" id="revenue_h1">+ R$ <?= $totalCashInflow ?> </h1>
                                <small class="text-muted"><strong>Menor receita</strong> <br> <?= $lowerValueIncome ?> <br>
                                    <strong>Maior receita</strong> <br> <?= $highValueIncome ?>
                                </small>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card mb-3 ">
                            <div class="card-header">
                                <h4 class="my-0 font-weight-normal">Despesa Mensal</h4>
                            </div>
                            <div class="card-body">
                                <h1 class="card-title pricing-card-title" id="expense_h1">- R$ <?= $totalCashOutflow ?></h1>
                                <small class="text-muted"><strong>Menor despesa</strong> <br> <?= $lowerExpense ?> <br>
                                    <strong>Maior despesa</strong> <br> <?= $biggetsExpense ?>
                                </small>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card mb-3 ">
                            <div class="card-header">
                                <h4 class="my-0 font-weight-normal">Saldo
                                    <a href="#!" id="btn"><i class="fa-solid fa-eye-slash" id="eye_icon" style="float: right"></i></a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <h1 class="card-title pricing-card-title <?= $balance_color_text ?>" id="ammount"> R$
                                    <?= $total_balance ?>
                                </h1>
                                <i class="fa-solid fa-sack-dollar fa-4x <?= $balance_color_text ?>"></i> <br>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h4 class="my-0 font-weight-normal">Atalhos</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex ">
                                        <strong>Cadastrar lembrete: </strong>
                                        <span class="badge d-block position-absolute" style="right: 10px; top: 8px">
                                            <a href="" data-toggle="modal" data-target="" title="Editar menu">
                                                <i class="fa-regular fa-square-plus fa-2x text-success"></i>
                                            </a>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex ">
                                        <strong>Cadastrar despesa: </strong>
                                        <span class="badge d-block position-absolute" style="right: 10px; top: 8px">
                                            <a href="" data-toggle="modal" data-target="" title="Editar menu">
                                                <i class="fa-regular fa-square-plus fa-2x text-success"></i>
                                            </a>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex ">
                                        <strong>Cadastrar conta: </strong>
                                        <span class="badge d-block position-absolute" style="right: 10px; top: 8px">
                                            <a href="" data-toggle="modal" data-target="" title="Editar menu">
                                                <i class="fa-regular fa-square-plus fa-2x text-success"></i>
                                            </a>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex ">
                                        <strong>Cadastrar usuário: </strong>
                                        <span class="badge d-block position-absolute" style="right: 10px; top: 8px">
                                            <a href="" data-toggle="modal" data-target="" title="Editar menu">
                                                <i class="fa-regular fa-square-plus fa-2x text-success"></i>
                                            </a>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>
        <!-- Section Revenue, Enpenses , balance and shortcuts -->

        <!-- Cash Inflow | Cash outflow form  -->
        <section>
            <div class="actions p-4 mb-4 bg-light rounded">
                <form action="<?= $BASE_URL ?>moviment_process.php" method="post">
                    <input type="hidden" name="type" value="create">
                    <div class="row">
                        <div class="col-lg-3">
                            <h4 class="font-weight-normal">Descriçao</h4>
                            <input type="text" name="description" id="description" class="form-control" placeholder="fatura 1">
                        </div>
                        <div class="col-lg-2">
                            <h4 class="font-weight-normal">Emissão</h4>
                            <input class="form-control" type="date" name="emissao" id="emissao" value="<?= $current_date ?>">
                        </div>
                        <div class="col-lg-2">
                            <h4 class="font-weight-normal">Valor</h4>
                            <input type="text" name="value" id="value" class="form-control money" placeholder="Ex: 80,00:">
                        </div>
                        <div class="col-lg-2">
                            <h4 class="font-weight-normal">Anotação</h4>
                            <input type="text" name="value" id="value" class="form-control money" placeholder="importante:">
                        </div>
                        <div class="col-lg-3 text-center">
                            <h4 class="font-weight-normal">Tipo</h4>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_action" id="entry" value="1" checked>
                                <label class="form-check-label" for="inlineRadio1">Boleto</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_action" id="out" value="2">
                                <label class="form-check-label" for="inlineRadio2">Pix</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_action" id="out" value="2" >
                                <label class="form-check-label" for="inlineRadio2">TED</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-3">
                                <h4 class="font-weight-normal">Descriçao</h4>
                                <input type="text" name="description" id="description" class="form-control" placeholder="fatura 2">
                        </div>
                        <div class="col-lg-2">
                            <h4 class="font-weight-normal">Vencimento</h4>
                            <input class="form-control" type="date" name="" id="">
                        </div>
                        <div class="col-lg-3">
                                <h4 class="font-weight-normal">Referência</h4>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Ref: 8723434">
                        </div>
                        <div class="col-lg-2" id="category_div_entry">
                            <h4 class="font-weight-normal">Conta</h4>
                            <select class="form-control" name="category" id="category_entry">
                                <option value="">Selecione</option>
                                <option value="">Caixa</option>
                                <option value="">Bradesco</option>
                                <option value="">Itaú</option>
                                <option value="">Santander</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                                <input type="submit" class="btn btn-lg btn-success" value="Adicionar"></input>
                        </div>
                    </div>
                   
                    
                </form>
            </div>
        </section>
        <!-- Cash Inflow | Cash outflow form  -->

        <!-- My reminders container -->
        <section>
            <div class="row">
                <div class="col-md-12">
                    <div class="actions mb-4 py-3 bg-light rounded">
                        <h4 class="text-center my-3">Meus Lembretes
                            <span class="d-inline-block" tabindex="3" data-toggle="tooltip" title="Adicionar novo lembrete">
                                <a href="#reminder_modal_create" data-toggle="modal" data-target="#reminder_modal_create">
                                    <i class="fa-regular fa-square-plus"></i>
                                </a>
                            </span>
                        </h4>
                        <hr class="hr">
                        <div class="row px-4">
                            <?php if (count($latestReminders) > 0) : ?>
                                <?php foreach ($latestReminders as $reminder) : ?>
                                    <div class="col-md-3 col-sm-3">
                                        <div class="card card-reminder mb-3 border-0">
                                            <div class="card-header border border-white"><small> <strong> <?= $reminder->title ?> </strong> <br> <?= $reminder->reminder_date ?></small></div>
                                            <div class="card-body">
                                                <p class="card-text"><?= $reminder->description ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <h5 class="mx-auto">Não há lembretes cadastrados.</h5>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <!-- End My reminders container -->

        <!--  latest financial moviments -->
        <section class="row">
            <div class="col-md-12" id="latest_moviments">

                <div class="actions mb-5 py-2 px-5 bg-light rounded">
                    <h4 class="font-weight-normal text-center my-3" id="latest-text">Últimos 5 registros</h4>
                    <hr class="hr">
                    <div class="row d-block text-right my-2 px-3 info">
                        <div>  <i class="fa-solid fa-copy fa-2x text-info"></i> <span> Copiar </span> </div>
                        <div>  <i class="fa-solid fa-receipt fa-2x text-sucsess"></i> <span> Status </span> </div>
                        <div> <i class="fa-solid fa-file-pen fa-2x"></i></a> <span> Editar </span> </div>
                        <div>  <i class="fa-solid fa-trash-can fa-2x"></i></a> <span> Deletar </span> </div>
                    </div>
                    <!-- <hr class="dashed"> -->
                    <div class="row">

                        <table class="table table-hover table-bordered">
                            <thead class="thead-dark">
                                <th>id</th>
                                <th>Data</th>
                                <th>Referência</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Conta</th>
                                <th>Anotação</th>
                                <th>Ação</th>
                            </thead>
                            <tbody>
                                <?php foreach ($latestFinancialMoviments as $financialMoviment) : ?>
                                    <tr class="pb-2">
                                        <td>
                                            <span class="table_description"> <strong> <?= $financialMoviment->id ?>
                                                </strong></span>
                                        </td>
                                        <td>
                                            <span> <?= $financialMoviment->create_at ?> </span>
                                        </td>
                                        <td>
                                            <span class="table_description"> <strong> <?= $financialMoviment->description ?>
                                                </strong></span>
                                        </td>
                                        <td>
                                            <span> R$ <?= $financialMoviment->value ?></span>
                                        </td>
                                        <td>
                                            <span> <?= $financialMoviment->update_at ?> </span>
                                        </td>
                                        <td>
                                            <?php if ($financialMoviment->type == 1) : ?>
                                                <img src="<?= $BASE_URL ?>assets/home/logo-bradesco.png" width="30" height="30" alt="">
                                            <?php else : ?>
                                                <img src="<?= $BASE_URL ?>assets/home/logo-banco-do-brasil.png" width="30" height="30" alt="">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span> <?= $financialMoviment->category ?> </span>
                                        </td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#exampleModalCenter<?= $financialMoviment->id ?>" title="Editar">
                                                <i class="fa-solid fa-copy text-info"></i>
                                            </a>
                                            <a href="#" data-toggle="modal" data-target="#exampleModalCenter<?= $financialMoviment->id ?>" title="Editar">
                                                <i class="fa-solid fa-receipt text-sucsess"></i>
                                            </a>
                                            <a href="#" data-toggle="modal" data-target="#exampleModalCenter<?= $financialMoviment->id ?>" title="Editar">
                                                <i class="fa-solid fa-file-pen"></i></a>
                                            <a href="#" data-toggle="modal" data-target="#del_latest_finance_moviment<?= $financialMoviment->id ?>" title="Deletar">
                                                <i class="fa-solid fa-trash-can"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>

                    <div class="offset-md-3 col-md-6 pb-3 text-center icons_latest">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 my-2 ver_entradas">
                                <a href="<?= $BASE_URL ?>financial_entry_report.php">
                                    <i class="fa-solid fa-plus"></i>
                                    &nbsp; Ver todas Entradas
                                </a>
                            </div>

                            <div class="col-md-6 col-sm-12 my-2 ver_saidas">
                                <a href="<?= $BASE_URL ?>financial_exit_report.php">
                                    <i class="fa-solid fa-minus"></i>
                                    &nbsp; Ver todas Saídas
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </section>

        <!-- Modal forms -->

            <!-- Finance moviment modal Edit -->
            <?php foreach ($latestFinancialMoviments as $financialMoviment) : ?>
                <div class="modal fade" id="exampleModalCenter<?= $financialMoviment->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Movimentação</h5>
                                <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                                    <span arial-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= $BASE_URL ?>moviment_process.php?id=<?= $financialMoviment->id ?>" method="post">
                                    <input type="hidden" name="type" value="edit">
                                    <div class="form-group">
                                        <label for="description">Descriçao:</label>
                                        <input type="text" name="description_edit" id="" class="form-control" placeholder="Insira uma nova descrição" value="<?= $financialMoviment->description ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="value">Valor:</label>
                                        <input type="text" name="value_edit" id="" class="form-control money" placeholder="Insira um novo valor" value="<?= $financialMoviment->value ?>">
                                    </div>
                                    <?php if ($financialMoviment->type == 2) : ?>
                                        <div class="form-group">
                                            <label for="expense_type">Despesa:</label>
                                            <?php if ($financialMoviment->expense == "Fixa") : ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio1" value="F" checked>
                                                    <label class="edit_moviment_label" for="inlineRadio1">Fixa</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio2" value="V">
                                                    <label class="edit_moviment_label" for="inlineRadio2">Váriavel</label>
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
                                    <input type="submit" value="Enviar" class="btn btn-lg btn-success" onclick="scrollToTop()">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- End Finance moviment modal edit -->

            <!-- Finance modal delete -->
            <?php foreach ($latestFinancialMoviments as $financialMoviment) : ?>
                <div class="modal fade" id="del_latest_finance_moviment<?= $financialMoviment->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
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
            <!-- End Finance modal delete -->

            <!--  Reminder modal create -->
            <div class="modal fade" id="reminder_modal_create" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Criar Lembrete</h5>
                            <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                                <span arial-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= $BASE_URL ?>reminders_process.php" method="post">
                                <input type="hidden" name="type" value="register">
                                <div class="form-group">
                                    <label for="description">Titulo:</label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Insira um titulo para o lembrete" value="">
                                </div>
                                <div class="form-group">
                                    <label for="value">Descrição:</label>
                                    <input type="text" name="description" id="" class="form-control" placeholder="Insira uma descrição" value="">
                                </div>

                                <div class="form-group">
                                    <label for="obs">Data:</label>
                                    <input class="form-control" type="date" name="reminder_date" id="" value="">
                                </div>
                                <input type="submit" value="Enviar" class="btn btn-lg btn-success" onclick="scrollToTop()">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Reminder modal create -->

        <!-- End modal forms -->

        <!-- Check today reminders -->
        <?php require_once("utils/check_reminders.php") ?>

</body>
<?php require_once("templates/footer.php"); ?>
<script>

    // tooltip
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })

    // load the page from the top
    function scrollToTop() {
        window.scrollTo(0, 0);
    }

    // Hide and Show balance ammount by click in the eye icon
    $(document).ready(function() {

        if (localStorage.getItem('hidden_ammount') == 'S') {
            $("#expense_h1").addClass("blur");
            $("#revenue_h1").addClass("blur");
            $("#ammount").addClass("blur");
        } else {
            $("#expense_h1").removeClass("blur");
            $("#revenue_h1").removeClass("blur");
            $("#ammount").removeClass("blur");
        }

        $('#btn').on("click", function() {
            $('#ammount').toggleClass('blur');
            $('#expense_h1').toggleClass('blur');
            $('#revenue_h1').toggleClass('blur');
            if ($('#ammount').hasClass('blur')) {
                localStorage.setItem('hidden_ammount', 'S');
                $("#eye_icon").removeClass("fa-eye-slash");
                $("#eye_icon").addClass("fa-eye");
            } else {
                localStorage.setItem('hidden_ammount', 'N');
                $("#eye_icon").removeClass("fa-eye");
                $("#eye_icon").addClass("fa-eye-slash");
            }
        });

    });
</script>