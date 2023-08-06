<?php
require_once("templates/header_iframe.php");
require_once("utils/config.php");
require_once("dao/InvoicesDAO.php");
require_once("dao/BankAccountsDAO.php");
require_once("dao/UserDAO.php");

$uersDao = new UserDAO($conn, $BASE_URL);
$allUsers = $uersDao->findAllUsers();

// ---> Objeto contas e seus processos <-------  //
$bankAccountsDao = new BankAccountsDAO($conn, $BASE_URL);
$accounts = $bankAccountsDao->getAllBankAccounts();
// ---> Objeto contas <-------  //

$invoiceDao = new InvoicesDao($conn, $BASE_URL);

// Traz o array com os dados de entradas da query personalizada para modal
$sql = "";
// $getEntryReports = $financialMovimentDao->getReports($sql, 1, $userData->id);

// // Paginação do relatório
$totalRegistros = count($invoiceDao->countAllInvoicesForAdmin());

$resultsPerPage = 10;
$numberPages = ceil($totalRegistros / $resultsPerPage);

// Pega numero da página atual
$page = isset($_GET["page"]) ? $_GET["page"] : 1;

// calcula o indice do primeiro registro da página atual
$offset = ($page - 1) * $resultsPerPage;


$invoice_id = 
$name_invoice = 
$account_invoice =
$reference_invoice =
$user_id =
$month_invoice = "";

if ($_POST) {
    //echo "pesquisa enviada";
    $sql = "";
    $totalRegistros = 0;

    if (isset($_POST['invoice_id']) && $_POST['invoice_id'] != '') { 
        $invoice_id = $_POST['invoice_id'];
        $sql .= "AND invoices.id = $invoice_id";
    }

    if (isset($_POST['reference_invoice']) && $_POST['reference_invoice'] != '') {
        $reference_invoice = $_POST['reference_invoice'];
        $sql .= " AND reference LIKE '%%$reference_invoice%%'";
    }

    if (isset($_POST['value_invoice']) && $_POST['value_invoice'] != '') {
        $value_invoice = $_POST['value_invoice'];
        $sql .= " AND value <= $value_invoice";
    }

    if (isset($_POST['account_invoice']) && $_POST['account_invoice'] != '') {
        $account_invoice = $_POST['account_invoice'];
        $sql .= " AND account = $account_invoice";
    }

    if (isset($_POST['month_invoice']) && $_POST['month_invoice'] != '') { 
        $month_invoice = $_POST['month_invoice'];
        $sql .= " AND MONTH(dt_expired) = '$month_invoice' ";
    }

    if (isset($_POST['user_id']) && $_POST['user_id'] != '') { 
        $user_id = $_POST['user_id'];
        $sql .= " AND user_id = '$user_id' ";
    }

    //echo $sql . "<br>";
}

// Traz total de Entradas do usuário default e páginação 
$allInvoicesUsers = $invoiceDao->getAllInvoicesForAdminToPagination($sql, $resultsPerPage, $offset);


?>

<div class="container-fluid">
    <h1 class="text-center my-5">Todas as faturas
        <!-- <img src="<?= $BASE_URL ?>assets/home/dashboard-main/full-wallet.png" width="64" height="64" alt=""> -->
        <i class="fa-solid fa-file-invoice"></i>
    </h1>

    <div class="entrys-search" id="entrys-search">
        <!-- <h3 class="text-secondary mb-3">Pesquisar:</h3> -->
        <form method="POST">
            <input type="hidden" name="user_id" id="user_id" value="<?= $userData->id ?>">
            <div class="row offset-sm-1">
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por id:</h4>
                        <input type="number" name="invoice_id" id="invoice_id" class="form-control" placeholder="Ex: 10" value="<?= $invoice_id ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por referência:</h4>
                        <input type="text" name="reference_invoice" id="reference_invoice" class="form-control" placeholder="Ex: REF: 10" value="<?= $reference_invoice ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por conta:</h4>
                        <select class="form-control" name="account_invoice" id="account_invoice">
                            <option value="">Selecione</option>
                            <?php foreach ($accounts as $account) : ?>
                                <option value="<?= $account->id ?>" <?= $account_invoice == $account->id ? "selected" : ""; ?>><?= decryptData($account->razao_social, $encryptionKey) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por mês:</h4>
                        <select class="form-control" name="month_invoice" id="">
                            <option value="">Selecione</option>
                            <?php foreach($meses as $index => $mes): ?>
                                <option value="<?= $index ?>" <?= $index == $month_invoice ? "selected" : ""; ?>><?= $mes ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por usuário:</h4>
                        <select class="form-control" name="user_id" id="">
                            <option value="">Selecione</option>
                           <?php foreach($allUsers as $user): ?>
                                <option value="<?= $user->id ?>" <?= $user->id == $user_id ? "selected" : ""; ?>><?= $user->getFullName($user) ?></option>
                           <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <input class="btn btn-lg btn-success" type="submit" value="Buscar">
                    <!-- <button class="btn btn-lg btn-secondary" id="print_btn" onclick="print()"> Imprimir</button> -->
                </div>
            </div>
        </form>
    </div>

    <!-- table div thats receive all entrys without customize inputs parameters  -->
    <div class="table_report my-3" id="table_report_entry">
        
        <div class="row d-block text-right my-2 px-3 info">
        <div> <i class="fa-regular fa-square-check text-success"></i> <span> Fatura paga </span> </div>
            <div> <i class="fa-regular fa-square-check text-danger"></i> <span> Fatura não paga </span> </div>
            <div> <i class="fa-regular fa-square-check text-secondary"></i> <span> Aguardando </span> </div>
        </div>
        <table class="table table-hover table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Data</th>
                    <th scope="col">Referência</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Vencimento</th>
                    <th scope="col">Status</th>
                    <th scope="col">Conta</th>
                    <th scope="col">Anotação</th>
                    <th scope="col">Funcionário</th>
                    <th scope="col" class="report-action">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allInvoicesUsers as $invoices) : ?>
                    <?php $value = $invoices->value;
                    $total_entry_value += (float) $value; ?>

                    <tr>
                        <th scope="row">
                            <?= $invoices->id ?>
                        </th>
                        <td>
                            <?= $invoices->emission ?>
                        </td>
                        <td>
                            <?= $invoices->reference ?>
                        </td>
                        <td>
                            <?= $invoices->value ?>
                        </td>
                        <td>
                            <?= $invoices->dt_expired ?>
                        </td>
                        <td class="info">
                            <?php if ($invoices->paid == "S"): ?>
                            <i class="fa-regular fa-square-check text-success"></i>
                            <?php else: ?>
                            <i class="fa-regular fa-square-check text-danger"></i>
                            <?php endif ?>
                        </td>
                        <td>
                            <div class="invoice_card_img">
                                <img src="<?= $BASE_URL ?>assets/home/contas/<?= $invoices->conta_img ?>"  alt="">
                            </div>
                        </td>

                        <td>
                            <?php if ($invoices->notation != "") : ?>
                                <a href="#!" id="grupos<?= $invoices->id ?>" onclick="openTooltip(<?= $invoices->id ?>)"><img src="<?= $BASE_URL ?>assets/home/dashboard-main/message_alert.gif" alt="message_alert" title="ver observação" width="33" height="30"> </a>
                                <div class="tooltip_" id="tooltip_<?= $invoices->id ?>">
                                    <div id="conteudo">
                                        <div class="bloco">
                                            <h5>Observação</h5>
                                            <a href="#!" id="close<?= $invoices->id ?>"><i class="fa-solid fa-xmark"></i></a>
                                        </div>
                                        <div class="bloco">
                                            <small>
                                                <?= $invoices->notation ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= $invoices->user_name ?>
                        </td>

                        <td id="latest_moviments" class="report-action"><a href="#" data-toggle="modal" data-target="#updateInvoiceUserAdmin<?= $invoices->id ?>" title="Editar">
                                <i class="fa-solid fa-file-pen"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9"> <strong> Total: </strong> R$
                        <?= number_format($total_entry_value, 2, ",", "."); ?>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Pagination buttons -->

        <div class="row justify-content-center">
            <nav aria-label="...">
                <ul class="pagination pagination-lg">
                    <?php for ($i = 1; $i <= $numberPages; $i++) : ?>
                        <?php $active = ($i == $page) ? "active-pagination" : ""; ?>

                        <li class="page-item <?= $active ?>">
                            <a class="page-link" href="<?= $BASE_URL ?>financial_entry_report.php?page=<?= $i ?>" tabindex="-1"><?= $i ?></a>
                        </li>

                    <?php endfor ?>
                </ul>
            </nav>
        </div>

        <!-- End pagination buttons -->
    </div>


    <!-- Invoice user edit status modal -->
    <?php foreach ($allInvoicesUsers as $invoices) : ?>
        <div class="modal fade" id="updateInvoiceUserAdmin<?= $invoices->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar status da fatura id: <?= $invoices->id ?></h5>
                        <button type="button" class="close_reports" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= $BASE_URL ?>invoice_process.php" method="post">
                            <input type="hidden" name="type" value="editInvoiceStatus">
                            <input type="hidden" name="id" value="<?= $invoices->id ?>">
                            <div class="form-group">
                                <label for="need_password">Mudar status para:?</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="invoice_status" id="invoice_status" value="S" <?= $invoices->paid == "S" ? "checked" : ""; ?> >
                                    <label class="form-check-label" for="inlineCheckbox1">Pago </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="invoice_status" id="invoice_status" value="N" <?= $invoices->paid == "N" ? "checked" : ""; ?> >
                                    <label class="form-check-label" for="inlineCheckbox2">Não Pago </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="invoice_status" id="invoice_status" value="A" <?= $invoices->paid == "A" ? "checked" : ""; ?>>
                                    <label class="form-check-label" for="inlineCheckbox3">Aguardando</label>
                                </div>
                            </div>
                            <input type="submit" value="Atualizar" class="btn btn-lg btn-success">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- End Invoice user edit status modal -->


    <!-- Modal para confirmação de exclusão de registro financeiro da busca personalizada -->
    <?php foreach ($getEntryReports as $financialMoviment) : ?>
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
    $(document).ready(function() {
        $('.placeholder').mask("00/00/0000", {
            placeholder: "__/__/____"
        });
    });

</script>

<script src="js/ajax_finance_entrys_request.js"></script>