<?php
require_once("templates/header_iframe.php");
require_once("utils/config.php");
include_once("utils/sessions_forms.php");
require_once("dao/ExpenseDAO.php");
require_once("dao/InvoicesDAO.php");
require_once("dao/RemindersDAO.php");
require_once("dao/BankAccountsDAO.php");

// ---> Objeto contas e seus processos -------  //
$bankAccountsDao = new BankAccountsDAO($conn, $BASE_URL);
$accounts = $bankAccountsDao->getAllBankAccounts();

// ---> Objeto contas <-------  //

// --- Objeto Despesas e seus processos <------ //

$expenseDao = new ExpenseDAO($conn, $BASE_URL);
// Maior despesa do usuário
$biggetsExpense = $expenseDao->getBiggestExpense($userData->id);
// Menor despesa
$lowersExpense = $expenseDao->getLowerExpense($userData->id);
// Traz total de saídas do usuário
$totalCashOutflow = $expenseDao->getAllCashOutflow($userData->id);

// ---> Objeto Despesas e seus processos <------ //

// ----> Objeto faturas e seus processos ------ //

$invoiceDao = new InvoicesDAO($conn, $BASE_URL);

// Traz as últimas faturas cadastradas do usuário
$latestInvoices = $invoiceDao->getLatestInvoices($userData->id);

// Traz total de receitas do usuário
$totalCashInflow = $invoiceDao->getAllCashInflow($userData->id);
// Traz a maior receita do usuário
$biggestRevenueUser = $invoiceDao->getBiggestInvoiceValueUser($userData->id);
// Traz a menor receita do usuário
$lowerRevenueUser = $invoiceDao->getLowerInvoiceValueUser($userData->id);

$ammount = $invoiceDao->getTotalBalance($userData->id);

// ---- Objeto faturas e seus processos <------ //

// traz os últimos 4 lembretes cadastrados pelo usuário 
$reminderDao = new RemindersDAO($conn, $BASE_URL);
$latestReminders = $reminderDao->getLatestReminders($userData->id);

?>

<body id="iframe-body">

    <div class="container-fluid">

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
                                <small class="text-muted"><strong>Menor receita</strong> <br>
                                    <?php foreach ($lowerRevenueUser as $lowerRevenue) : ?>
                                        <?= $lowerRevenue->reference ?> <?= $lowerRevenue->value ?>
                                    <?php endforeach ?>
                                    <?php if (count($lowerRevenueUser) < 1) {
                                        echo 'Não há dados registrados';
                                    } ?>
                                    <br>
                                    <strong>Maior receita</strong> <br>
                                    <?php foreach ($biggestRevenueUser as $biggestRevenue) : ?>
                                        <?= $biggestRevenue->reference ?> <?= $biggestRevenue->value ?>
                                    <?php endforeach ?>
                                    <?php if (count($biggestRevenueUser) <  1) {
                                        echo 'Não há dados registrados';
                                    } ?>
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
                                <small class="text-muted">
                                    <strong>Menor despesa</strong> <br>

                                    <?php foreach ($lowersExpense as $lowerExpense) : ?>
                                        <?= $lowerExpense->description ?> <?= $lowerExpense->value ?>
                                    <?php endforeach ?>
                                    <?= count($lowersExpense) == 0 ? 'Não há dados registrados' : ""; ?>
                                    <br>
                                    <strong>Maior despesa</strong> <br>
                                    <?php foreach ($biggetsExpense as $bigExpense) : ?>
                                        <?= $bigExpense->description ?> <?= $bigExpense->value ?>
                                    <?php endforeach ?>
                                    <?= count($biggetsExpense) == 0 ? 'Não há dados registrados' : ""; ?>
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
                                <h1 class="card-title pricing-card-title text-success" id="ammount"> R$
                                    <?= $ammount ?>
                                </h1>
                                <i class="fa-solid fa-sack-dollar fa-4x text-success"></i> <br>
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
                                            <a href="" data-toggle="modal" data-target="#reminder_modal_create" title="Editar menu">
                                                <i class="fa-regular fa-square-plus fa-2x text-success"></i>
                                            </a>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex ">
                                        <strong>Cadastrar despesa: </strong>
                                        <span class="badge d-block position-absolute" style="right: 10px; top: 8px">
                                            <a href="#" data-toggle="modal" data-target="#expenseEditModal" title="Editar menu">
                                                <i class="fa-regular fa-square-plus fa-2x text-success"></i>
                                            </a>
                                        </span>
                                    </li>
                                    <?php if ($userData->levels_access_id == 1) : ?>
                                        <li class="list-group-item d-flex ">
                                            <strong>Cadastrar conta: </strong>
                                            <span class="badge d-block position-absolute" style="right: 10px; top: 8px">
                                                <a href="" data-toggle="modal" data-target="#card_modal_create" title="Editar menu">
                                                    <i class="fa-regular fa-square-plus fa-2x text-success"></i>
                                                </a>
                                            </span>
                                        </li>
                                        <!-- <li class="list-group-item d-flex ">
                                            <strong>Cadastrar usuário: </strong>
                                            <span class="badge d-block position-absolute" style="right: 10px; top: 8px">
                                                <a href="" data-toggle="modal" data-target="" title="Editar menu">
                                                    <i class="fa-regular fa-square-plus fa-2x text-success"></i>
                                                </a>
                                            </span>
                                        </li> -->
                                    <?php endif ?>
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
                <form action="<?= $BASE_URL ?>invoice_process.php" method="post">
                    <input type="hidden" name="type" value="create">
                    <div class="row">
                        <div class="col-lg-3">
                            <h4 class="font-weight-normal">Descriçao</h4>
                            <input type="text" name="invoice_one" id="invoice_one" class="form-control" placeholder="fatura 1" value="<?= $_SESSION['invoice_one'] ?>">
                        </div>
                        <div class="col-lg-2">
                            <h4 class="font-weight-normal">Emissão</h4>
                            <input class="form-control" type="date" name="date_emission" id="emissao" value="<?= $current_date ?>">
                        </div>
                        <div class="col-lg-2">
                            <h4 class="font-weight-normal">Valor</h4>
                            <input type="text" name="value" id="value" class="form-control money" placeholder="Ex: 80,00:" value="<?= $_SESSION['value'] ?>">
                        </div>
                        <div class="col-lg-2">
                            <h4 class="font-weight-normal">Anotação</h4>
                            <input type="text" name="notation" id="notation" class="form-control" placeholder="importante:" value="<?= $_SESSION['notation'] ?>">
                        </div>
                        <div class="col-lg-3 text-center">
                            <h4 class="font-weight-normal">Tipo</h4>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_paid" value="1" checked>
                                <label class="form-check-label" for="inlineRadio1">Boleto</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_paid" value="2">
                                <label class="form-check-label" for="inlineRadio2">Pix</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_paid" value="2">
                                <label class="form-check-label" for="inlineRadio2">TED</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-3">
                            <h4 class="font-weight-normal">Descriçao</h4>
                            <input type="text" name="invoice_two" id="invoice_two" class="form-control" placeholder="fatura 2" value="<?= $_SESSION['invoice_two'] ?>">
                        </div>
                        <div class="col-lg-2">
                            <h4 class="font-weight-normal">Vencimento</h4>
                            <input class="form-control" type="date" name="dt_expired" id="dt_expired" value="<?= $_SESSION['dt_expired'] ?>">
                        </div>
                        <div class="col-lg-3">
                            <h4 class="font-weight-normal">Referência</h4>
                            <input type="text" name="reference" id="reference" class="form-control" placeholder="Ref: 8723434" value="<?= $_SESSION['reference'] ?>">
                        </div>
                        <div class="col-lg-2" id="category_div_entry">
                            <h4 class="font-weight-normal">Conta</h4>
                            <select class="form-control" name="account" id="account">
                                <option value="">Selecione</option>
                                <?php foreach ($accounts as $account) : ?>
                                    <option value="<?= $account->id ?>" value="<?= $_SESSION['account'] ?>">
                                    <?= $account->cod . " " .  $account->bank_name . " - " ?>
                                    <?= decryptData($account->razao_social, $encryptionKey) ?>
                                    </option>
                                <?php endforeach ?>
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
                            <span class="d-inline-block" tabindex="3" data-toggle="tooltip" title="Adicionar novo lembrete"></span>
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

        <!--  latest invoices moviments -->
        <section>
            <div class="row">
                <div class="col-md-12" id="latest_moviments">

                    <div class="actions mb-5 py-2 px-5 bg-light rounded">
                        <h4 class="font-weight-normal text-center my-3" id="latest-text">Últimos 5 registros</h4>
                        <hr class="hr">
                        <div class="row d-block text-right my-2 px-3 info">
                            <div> <i class="fa-solid fa-copy fa-2x text-info"></i> <span> Copiar </span> </div>
                            <div> <i class="fa-solid fa-receipt fa-2x text-sucsess"></i> <span> Status </span> </div>
                            <div> <i class="fa-solid fa-file-pen fa-2x"></i></a> <span> Editar </span> </div>
                            <div> <i class="fa-solid fa-trash-can fa-2x"></i></a> <span> Deletar </span> </div>
                        </div>

                        <div class="row">

                            <table class="table table-hover table-bordered">
                                <thead class="thead-dark">
                                    <th>id</th>
                                    <th>Data</th>
                                    <th>Fatura 1</th>
                                    <th>Fatura 2</th>
                                    <th>Referência</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <!-- <th>Status</th> -->
                                    <th>Conta</th>
                                    <th>Anotação</th>
                                    <th>Ação</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($latestInvoices as $invoice) : ?>
                                        <tr class="pb-2">
                                            <td>
                                                <strong> <?= $invoice->id ?>
                                                </strong>
                                            </td>
                                            <td class="table_description">
                                                <?= $invoice->emission ?>
                                            </td>
                                            <td class="d-flex justify-content-between <?= $invoice->invoice_one_status == "S" ? "bg-success" : ($invoice->invoice_one_status == "N" ? "bg-danger" : "table_description"); ?>"><?= $invoice->invoice_one ?>
                                                <?php if ($invoice->invoice_one_status == "A"): ?>
                                                <form action="" method="post">                                                   
                                                    <label for="submit">
                                                     <a href=""> <i class="fa-solid fa-file-invoice text-dark" title="clique para consultar o status"></i> </a>
                                                    </label>
                                                    <input type="submit" id="submit" value="">
                                                </form>
                                                <?php endif ?>
                                            </td>
                                            <td class=" <?= $invoice->invoice_two_status == "S" ? "bg-success" : ($invoice->invoice_two_status == "N" ? "bg-danger" : "table_description") ?>"><?= $invoice->invoice_two ?></td>
                                            <td class="table_description">
                                                <?= $invoice->reference ?>
                                            </td>
                                            <td>
                                                <span> R$ <?= number_format($invoice->value, 2, ",", ".") ?></span>
                                            </td>
                                            <td>
                                                <span> <?= $invoice->dt_expired ?> </span>
                                            </td>
                                            <!-- <td class="info">
                                                <?php if ($invoice->paid == "S") : ?>
                                                    <i class="fa-regular fa-square-check text-success"></i>
                                                <?php else : ?>
                                                    <i class="fa-regular fa-square-check text-danger"></i>
                                                <?php endif ?>
                                            </td> -->
                                            <td class="">
                                                <div class="invoice_card_img text-left px-2">
                                                    <img clss="" src="<?= $BASE_URL ?>assets/home/contas/<?= $invoice->conta_img ?>" alt="">
                                                    <span class="ml-2 text-center"></span> <?= decryptData($invoice->razao_social, $encryptionKey) ?> </span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($invoice->notation != "") : ?>
                                                    <a href="#!" id="grupos<?= $invoice->id ?>" onclick="openTooltip(<?= $invoice->id ?>)"><img src="<?= $BASE_URL ?>assets/home/dashboard-main/message_alert.gif" alt="message_alert" title="ver observação" width="33" height="30"> </a>
                                                    <div class="tooltip_" id="tooltip_<?= $invoice->id ?>">
                                                        <div id="conteudo">
                                                            <div class="bloco">
                                                                <h5>Observação</h5>
                                                                <a href="#!" id="close<?= $invoice->id ?>"><i class="fa-solid fa-xmark"></i></a>
                                                            </div>
                                                            <div class="bloco">
                                                                <small>
                                                                    <?= $invoice->notation ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="#" data-toggle="modal" data-target=".copyCodigoBoleto<?= $invoice->id ?>" title="Editar">
                                                    <i class="fa-solid fa-copy text-info"></i>
                                                </a>
                                                <?php if ($userData->levels_access_id == 1) : ?>
                                                    <a href="#" data-toggle="modal" data-target="#editInvoiceModal<?= $invoice->id ?>" title="Editar fatura">
                                                        <i class="fa-solid fa-file-pen"></i></a>

                                                    <a href="#" data-toggle="modal" data-target="#del_latest_invoice<?= $invoice->id ?>" title="Deletar">
                                                        <i class="fa-solid fa-trash-can"></i></a>
                                                <?php endif ?>
                                                <!-- <a href="#" data-toggle="modal" data-target="#exampleModalCenter<?= $invoice->id ?>" title="Editar">
                                                    <i class="fa-solid fa-receipt text-sucsess"></i>
                                                </a> -->

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>

                        <div class="offset-md-3 col-md-6 pb-3 text-center icons_latest">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 my-2 ver_entradas">
                                    <a href="<?= $BASE_URL ?>invoices_user.php">
                                        <i class="fa-solid fa-plus"></i>
                                        &nbsp; Ver todas as receitas
                                    </a>
                                </div>

                                <div class="col-md-6 col-sm-12 my-2 ver_saidas">
                                    <a href="<?= $BASE_URL ?>financial_exit_report.php">
                                        <i class="fa-solid fa-minus"></i>
                                        &nbsp; Ver todas as despesas
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--  End latest invoices moviments -->

        <!-- Modal forms -->

        <!--  Reminder modal create -->
        <div class="modal fade" id="reminder_modal_create" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
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

        <!-- End Expense moviment Edit modal -->
        <div class="modal fade" id="expenseEditModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cadastrar despesa </h5>
                        <button type="button" class="close_reports" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= $BASE_URL ?>expense_process.php" method="post">
                            <input type="hidden" name="type" value="create">
                            <div class="form-group">
                                <label for="description">Descriçao:</label>
                                <input type="text" name="description" id="" class="form-control" placeholder="Insira uma nova descrição" required>
                            </div>
                            <div class="form-group">
                                <label for="value">Valor:</label>
                                <input type="text" name="value" id="" class="form-control money" placeholder="Insira um novo valor" required>
                            </div>
                            <div class="form-group">
                                <label for="obs">Data da despesa:</label>
                                <input type="date" class="form-control" name="date_expense" id="date_expense" value="" required>
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
        <!-- End Expense moviment Edit modal -->

        <!-- Card modal edit -->
        <div class="modal fade" id="card_modal_create" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cadastrar conta</h5>
                        <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= $BASE_URL ?>account_process.php?id=" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="type" value="create">
                            <div class="form-group">
                                <label for="razao">Razão Social:</label>
                                <input type="text" name="razao" id="razap" class="form-control" value="<?= $_SESSION['razao'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="cnpj">CNPJ:</label>
                                <input type="text" name="cnpj" id="cnpj" class="form-control" value="<?= $_SESSION['cnpj'] ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ag">Agência:</label>
                                        <input type="text" name="ag" id="ag" class="form-control" value="<?= $_SESSION['ag'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="cc">Conta:</label>
                                        <input type="text" name="cc" id="cc" class="form-control" value="<?= $_SESSION['cc'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="pix">Chave Pix:</label>
                                        <input type="text" name="pix" id="pix" class="form-control" value="<?= $_SESSION['pix'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="color">Cor:</label>
                                        <input type="color" name="color" id="color" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="logo">Logo:</label>
                                <input class="form-control" type="file" name="image" id="image" value="">
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
        <!-- End Card modal edit -->

        <!-- Invoice moviment modal Edit -->
        <?php foreach ($latestInvoices as $invoice) : ?>
            <div class="modal fade" id="editInvoiceModal<?= $invoice->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar fatura</h5>
                            <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                                <span arial-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= $BASE_URL ?>invoice_process.php" method="post">
                                <input type="hidden" name="type" value="update">
                                <input type="hidden" name="id" value="<?= $invoice->id ?>">
                                <div class="form-group">
                                    <label for="invoice_one">Descriçao: <small>(fatura 1)</small> </label>
                                    <input class="form-control" type="text" name="invoice_one" id="" value="<?= $invoice->invoice_one ?>" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="value">Valor:</label>
                                            <input class="form-control money" type="text" name="value" id="" value="<?= $invoice->value ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type_edit">Tipo:</label>
                                            <select class="form-control" name="type_edit" id="type_edit" required>
                                                <option value="">Selecione</option>
                                                <option value="1" <?= $invoice->type == 1 ? "selected" : "" ?>>Boleto</option>
                                                <option value="2" <?= $invoice->type == 2 ? "selected" : "" ?>>Pix</option>
                                                <option value="3" <?= $invoice->type == 3 ? "selected" : "" ?>>TED</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="notation">Anotação:</label>
                                            <input class="form-control" type="text" name="notation" id="notation" value="<?= $invoice->notation ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="dt_expired">Vencimento:</label>
                                            <input class="form-control" type="date" name="dt_expired" id="" value="<?= date("Y-m-d", strtotime($invoice->dt_expired)) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="invoice_two">Descriçao: <small>(fatura 2)</small> </label>
                                    <input class="form-control" type="text" name="invoice_two" id="" value="<?= $invoice->invoice_two ?>" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reference">Referência:</label>
                                            <input class="form-control" type="text" name="reference" id="" value="<?= $invoice->reference ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="account">Conta:</label>
                                            <select class="form-control" name="account" id="">
                                                <?php foreach ($accounts as $account) : ?>
                                                    <option value="<?= $account->id ?>" <?= $account->id == $invoice->account ? "selected" : "" ?> required>
                                                        <?= decryptData($account->razao_social, $encryptionKey) ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
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
        <!-- End Invoice moviment modal edit -->

        <!-- Invoice modal delete -->
        <?php foreach ($latestInvoices as $invoice) : ?>
            <div class="modal fade" id="del_latest_invoice<?= $invoice->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <p>Tem certeza que deseja excluir o registro?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                            <form action="<?= $BASE_URL ?>invoice_process.php" method="POST">
                                <input type="hidden" name="type" value="delete">
                                <input type="hidden" name="id" value="<?= $invoice->id ?>">
                                <button type="submit" class="btn btn-primary">Sim</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <!-- End Invoice modal delete -->

        <!-- Copy Invoice numbers modal -->
        <?php foreach ($latestInvoices as $invoice) : ?>
            <div class="modal fade copyCodigoBoleto<?= $invoice->id ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Copiar código do boleto</h5>
                            <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                                <span arial-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="cnpinvoice_one_copy">Fatura 1</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="invoice_one_copy" value="<?= $invoice->invoice_one ?>" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success copy-btn" data-clipboard-target="#invoice_one_copy">Copiar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="invoice_two_copy">Fatura 2</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="invoice_two_copy" value="<?= $invoice->invoice_two ?>" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success copy-btn" data-clipboard-target="#invoice_two_copy">Copiar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
<?php endforeach; ?>
<!-- End Invoice modal delete -->

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

    // para modal cadastro de contas
    // Referência para o input do tipo "number"
    var inputNumber1 = $("#cc");
    var inputNumber2 = $("#ag");

    // Função para verificar se o valor contém apenas números
    function validateNumberInput(input) {
        var inputValue = input.val();
        var regex = /^\d+$/;
        if (!regex.test(inputValue)) {
            input.val("");
        }
    }

    // Evento "input" para ambos os inputs
    inputNumber1.on("input", function() {
        validateNumberInput(inputNumber1);
    });

    inputNumber2.on("input", function() {
        validateNumberInput(inputNumber2);
    });
    // para modal cadastro de contas


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

    // Auto preenchimento de valor e data de vencimento da fatura

    // Função para formatar um número no padrão "1.000,00"
    function formatNumber(number) {
        return (number / 100).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
    }

    $(document).ready(function() {
        // Quando o valor do input de texto for alterado
        $("#invoice_one").on("input", function() {
            // Obtenha o valor do input de texto
            var linhaBoleto = $(this).val();

            // Verifique se o campo de texto está vazio
            if (linhaBoleto.trim() === "") {
                // Se estiver vazio, limpe o campo de data
                $("#dt_expired").val("");
                $("#value").val("");
                return; // Encerre o manipulador de eventos aqui para evitar processamento desnecessário
            }

            // Remove pontos e espaços do código de boleto
            linhaBoleto = linhaBoleto.replace(/[.\s]/g, "");

            // Extrai os últimos 10 caracteres do código de boleto
            var valorLinhaBoleto = linhaBoleto.substr(-10).replace(/^0+/, "");

            // Calcula a data de vencimento a partir dos 4 dígitos antes dos últimos 10 caracteres
            var vencimentoEmDias = parseInt(linhaBoleto.substr(-14, 4));
            var dataBase = new Date(1997, 9, 7); // Data base instituída pelo BACEN (07/10/1997)
            var dataVencimento = new Date(dataBase);
            dataVencimento.setDate(dataBase.getDate() + vencimentoEmDias); // Adiciona os dias à data base
            var dataFormatada = dataVencimento.toISOString().slice(0, 10);

            // Verifique se o valor é um número válido
            if ($.isNumeric(valorLinhaBoleto)) {
                // Se for um número válido, insira-o no input de número e data
                $("#value").val(formatNumber(parseFloat(valorLinhaBoleto)));
                $("#dt_expired").val(dataFormatada);
            } else {
                // Caso contrário, limpe os inputs de número e data
                $("#value").val("");
                $("#dt_expired").val("");
            }
        });
    });

    // JS do modal de copiar codigos do boleto
    var clipboard = new ClipboardJS('.copy-btn');

    clipboard.on('success', function(event) {
        alert('Texto copiado com sucesso!');
        event.clearSelection();
    });

    clipboard.on('error', function(event) {
        //alert('Erro ao copiar texto. Tente novamente.');
    });
</script>