<?php
require_once("templates/header_iframe.php");
require_once("utils/check_levels_acess_admin.php");
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

// Paginação do relatório
$totalRegistros = $invoiceDao->countAllInvoicesForAdmin();

$resultsPerPage = 10;
$numberPages = ceil($totalRegistros / $resultsPerPage);

// Pega numero da página atual
$page = isset($_GET["page"]) ? $_GET["page"] : 1;

// calcula o indice do primeiro registro da página atual
$offset = ($page - 1) * $resultsPerPage;


$sql = "";
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

    if (isset($_POST['emission']) && $_POST['emission'] != '') {
        $emission = $_POST['emission'];
        $sql .= " AND DATE(emission) = '$emission'";
    }

    if (isset($_POST['dt_expired']) && $_POST['dt_expired'] != '') {
        $dt_expired = $_POST['dt_expired'];
        $sql .= " AND DATE(dt_expired) = '$dt_expired'";
    }

    if (isset($_POST['user_id']) && $_POST['user_id'] != '') {
        $user_id = $_POST['user_id'];
        $sql .= " AND user_id = '$user_id' ";
    }

    if (isset($_POST['dt_de']) && $_POST['dt_de'] != '' && isset($_POST['dt_ate']) && $_POST['dt_ate'] != '') {
        $dt_de = $_POST['dt_de'];
        $dt_ate = $_POST['dt_ate'];
        $sql .= " AND emission BETWEEN '$dt_de' AND '$dt_ate 23:59:59' ";
    }

     //echo $sql . "<br>";
}

// Traz total de Entradas do usuário default e páginação 
$allInvoicesUsers = $invoiceDao->getAllInvoicesForAdminToPagination($sql, $resultsPerPage, $offset);
//echo count($allInvoicesUsers);

?>

<div class="container-fluid">
    <h1 class="text-center my-5">Todas as faturas
        <!-- <img src="<?= $BASE_URL ?>assets/home/dashboard-main/full-wallet.png" width="64" height="64" alt=""> -->
        <i class="fa-solid fa-file-invoice"></i>
    </h1>

    <div class="row my-2 px-2">
        <div class="col-lg-12 d-flex justify-content-end">
            <button class="btn btn-lg btn-outline-secondary" id="limparCampos" title="Limpa todos os campos">Limpar</button>
        </div>
    </div>

    <div class="entrys-search" id="entrys-search">
        <!-- <h3 class="text-secondary mb-3">Pesquisar:</h3> -->
        <form method="POST" id="meuFormulario">
            <input type="hidden" name="user_id" id="user_id" value="<?= $userData->id ?>">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-1 col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por id:</h4>
                        <input type="number" name="invoice_id" id="invoice_id" class="form-control" placeholder="Ex: 10" value="<?= $invoice_id ?>">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por referência:</h4>
                        <input type="text" name="reference_invoice" id="reference_invoice" class="form-control" placeholder="Ex: REF: 10" value="<?= $reference_invoice ?>">
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por conta:</h4>
                        <select class="form-control" name="account_invoice" id="account_invoice">
                            <option value="">Selecione</option>
                            <?php foreach ($accounts as $account) : ?>
                                <option value="<?= $account->id ?>" <?= $account_invoice == $account->id ? "selected" : ""; ?>>
                                    <?= $account->cod . " " .  $account->bank_name . " - " ?>
                                    <?= decryptData($account->razao_social, $encryptionKey) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por emissão:</h4>
                        <input class="form-control" type="date" name="emission" id="emission" value="<?= $emission ?>">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por vencimento:</h4>
                        <input class="form-control" type="date" name="dt_expired" id="dt_expired" value="<?= $dt_expired ?>">
                    </div>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
            <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por usuário:</h4>
                        <select class="form-control" name="user_id" id="">
                            <option value="">Selecione</option>
                            <?php foreach ($allUsers as $user) : ?>
                                <option value="<?= $user->id ?>" <?= $user->id == $user_id ? "selected" : ""; ?>><?= $user->getFullName($user) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h4>De:</h4>
                    <input class="form-control" type="date" name="dt_de" id="dt_de">
                </div>
                <div class="col-lg-2 col-md-4">
                    <h4>Até:</h4>
                    <input class="form-control" type="date" name="dt_ate" id="dt_ate">
                </div>
                <div class="col-lg-1 col-md-2">
                    <input class="btn btn-lg btn-success" type="submit" value="Buscar">
                    <!-- <button class="btn btn-lg btn-secondary" id="print_btn" onclick="print()"> Imprimir</button> -->
                </div>
            </div>
        </form>
    </div>

    <!-- table div thats receive all entrys without customize inputs parameters  -->
    <?php if (count($allInvoicesUsers) > 0) : ?>
        <div class="table_report my-3" id="latest_moviments">
            <h3 class="text-center text-secondary">Resultados:</h3>
            <hr class="hr">
            <div class="row my-2 px-2">
                <div class="col-lg-12 d-flex justify-content-end ">
                    <a href="<?= $BASE_URL ?>invoices_paid.php" class="btn btn-md btn-outline-dark mr-3" id="faturas pagas" title="Limpa todos os campos"><i class="fa-solid fa-receipt fa-2x text-success"></i> Faturas pagas</a>
                </div>
            </div>
            <hr class="hr">
            <div class="row d-block text-right my-2 px-3 info">
                <div class=" d-flex justify-content-end  my-2 info">
                    <!-- <div> <i class="fa-solid fa-copy fa-2x text-info"></i> <span> Copiar </span> </div> -->
                    <!-- <div> <i class="fa-solid fa-square text-success"></i> <span> Fatura paga </span> </div>
                <div> <i class="fa-solid fa-square text-danger"></i> <span> Fatura não paga </span> </div> -->
                    <div> <i class="fa-solid fa-check-double text-info"></i> <span> Informar Pgto.</span> </div>
                    <div> <i class="fa-solid fa-receipt fa-2x text-sucsess"></i> <span> Status da fatura</span> </div>
                    <div> <i class="fa-solid fa-file-pen fa-2x"></i></a> <span> Editar </span> </div>
                    <div> <i class="fa-solid fa-trash-can fa-2x"></i></a> <span> Deletar </span> </div>
                </div>
            </div>
            <table class="table table-hover table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Data</th>
                        <th scope="col">Referência</th>
                        <th scope="col">Valor da fatura</th>
                        <th scope="col">Valor pago</th>
                        <th scope="col">Vencimento</th>
                        <th scope="col">Checada</th>
                        <th scope="col">Conta</th>
                        <th scope="col">Anotação</th>
                        <th scope="col">Funcionário</th>
                        <th scope="col" class="report-action">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allInvoicesUsers as $invoices) : ?>
                        <?php
                        $total_value += (float)$invoices->value;
                        $total_paid += (float) $invoices->ammount_paid;
                        ?>

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
                                <?= number_format($invoices->value, 2, ",", "."); ?>
                            </td>
                            <td>
                                <?= number_format($invoices->ammount_paid, 2, ",", ".") ?>
                            </td>
                            <td>
                                <?= $invoices->dt_expired ?>
                            </td>
                            <td class="info">
                                <?php if ($invoices->invoice_two_status != "Não checado") : ?>
                                    <i class="fa-regular fa-square-check text-success"></i>
                                <?php else : ?>
                                    <i class="fa-regular fa-square-check text-secondary"></i>
                                <?php endif ?>
                            </td>
                            <td>
                                <div class="invoice_card_img px-2">
                                    <img clss="" src="<?= $BASE_URL ?>assets/home/contas/<?= $invoices->conta_img ?>" alt="">
                                    <span class="ml-2 text-center"></span> <?= decryptData($invoices->razao_social, $encryptionKey) ?> </span>
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
                            <td id="latest_moviments" class="report-action">
                                <div class="d-flex">
                                    <?php if ($invoices->ammount_paid <= 0) : ?>
                                        <a href="#" data-toggle="modal" data-target="#updateInvoiceUserAdmin<?= $invoices->id ?>" title="Informe pagamento">
                                            <i class="fa-solid fa-check-double"></i>
                                        </a>
                                    <?php endif ?>
                                    <a href="#" data-toggle="modal" data-target=".checkStatusInvoice<?= $invoices->id ?>" title="Checar status">
                                        <i class="fa-solid fa-receipt fa-2x text-sucsess"></i>
                                    </a>
                                    <a href="#" data-toggle="modal" data-target="#editInvoiceModal<?= $invoices->id ?>" title="Editar">
                                        <i class="fa-solid fa-file-pen"></i>
                                    </a>
                                    <a href="#" data-toggle="modal" data-target="#del_latest_invoice<?= $invoices->id ?>" title="Deletar">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="11"> <strong> Total valor fatura: </strong> R$
                            <?= number_format($total_value, 2, ",", "."); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11"> <strong> Total valor pago: </strong> R$ <?= number_format($total_paid, 2, ",", ".") ?>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- Pagination buttons -->
            <?php if ($totalRegistros > 10) : ?>
                <div class="row justify-content-center">
                    <nav aria-label="...">
                        <ul class="pagination pagination-lg">
                            <?php for ($i = 1; $i <= $numberPages; $i++) : ?>
                                <?php $active = ($i == $page) ? "active-pagination" : ""; ?>

                                <li class="page-item <?= $active ?>">
                                    <a class="page-link" href="<?= $BASE_URL ?>invoices.php?page=<?= $i ?>" tabindex="-1"><?= $i ?></a>
                                </li>

                            <?php endfor ?>
                        </ul>
                    </nav>
                </div>
            <?php endif ?>

            <!-- End pagination buttons -->
        </div>
    <?php else : ?>
        <div class="col-md-12">
            <hr class="hr">
            <h5 class="py-2 text-center text-info">Ainda não há faturas cadastradas.</h5>
        </div>
    <?php endif ?>

    <!-- Invoice user edit paid status modal -->
    <?php foreach ($allInvoicesUsers as $invoices) : ?>
        <div class="modal fade" id="updateInvoiceUserAdmin<?= $invoices->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
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
                                <label for="need_password">Confirmar pagamento da fatura:?</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="invoice_status" id="invoice_status_total<?= $invoices->id ?>" value="<?= number_format($invoices->value, 2, ",", ".") ?>" onclick="showPaidValue(<?= $invoices->id ?>)">
                                    <label class="form-check-label" for="inlineCheckbox2">Valor total </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="invoice_status" id="invoice_status<?= $invoices->id ?>" value="P" onclick="showPaidValue(<?= $invoices->id ?>)">
                                    <label class="form-check-label" for="inlineCheckbox1">Valor parcial </label>
                                </div>
                            </div>
                            <div class="form-group" id="paid_value_div<?= $invoices->id; ?>" style="display: none;">
                                <label for="">Informe o valor pago:</label>
                                <input class="form-control money" type="text" name="value_paid" id="value_paid<?=$invoices->id?>">
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
    <!-- End Invoice user edit paid status modal -->

    <!-- Invoice moviment modal Edit -->
    <?php foreach ($allInvoicesUsers as $invoice) : ?>
        <div class="modal fade" id="editInvoiceModal<?= $invoice->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
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
                                <input class="form-control" type="text" name="invoice_one" id="" value="<?= decryptData($invoice->invoice_one, $encryptionKey) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="value">Valor:</label>
                                        <input class="form-control money" type="text" name="value" id="" value="<?= number_format($invoice->value, 2, ",", ".") ?>" required>
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
                                <input class="form-control" type="text" name="invoice_two" id="" value="<?= decryptData($invoice->invoice_two, $encryptionKey) ?>" required>
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
    <?php foreach ($allInvoicesUsers as $invoice) : ?>
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

    <!-- Status Invoice modal -->
    <?php foreach ($allInvoicesUsers as $invoice) : ?>
        <div class="modal fade checkStatusInvoice<?= $invoice->id ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Status do boleto</h5>
                        <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <form action="<?= $BASE_URL ?>consulta_admin.php" method="post">
                                <input type="hidden" name="linha_digitavel" value="<?= decryptData($invoice->invoice_one, $encryptionKey) ?>">
                                <input type="hidden" name="id" value="<?= $invoice->id ?>">
                                <input type="hidden" name="invoice_type" value="invoice_one_status">
                                <input type="hidden" name="current_status" value="<?= $invoice->invoice_one_status ?>">
                                <div class="form-group">
                                    <label for="invoice_one_copy">Fatura 1</label>
                                    <small class="bg-secondary text-white px-2 rounded">Status atual no sistema: <?= $invoice->invoice_one_status ?> </small>
                                    <div class="input-group">
                                        <input type="text" class="form-control <?= $invoice->invoice_one_status == "S" ? "bg-success" : ($invoice->invoice_one_status == "N" ? "text-white bg-danger" : "") ?>" id="invoice_one_copy" value="<?= decryptData($invoice->invoice_one, $encryptionKey) ?>" readonly>
                                    </div>
                                </div>

                                <input class="btn btn-success" type="submit" value="Checar">
                            </form>
                        </div>

                        <div class="form-group">
                            <form action="<?= $BASE_URL ?>consulta_admin.php" method="post">
                                <input type="hidden" name="linha_digitavel" value="<?= decryptData($invoice->invoice_two, $encryptionKey) ?>">
                                <input type="hidden" name="id" value="<?= $invoice->id ?>">
                                <input type="hidden" name="invoice_type" value="invoice_two_status">
                                <input type="hidden" name="current_status" value="<?= $invoice->invoice_two_status ?>">
                                <div class="form-group">
                                    <label for="invoice_two_copy">Fatura 2</label>
                                    <small class="bg-secondary text-white px-2 rounded">Status atual no sistema: <?= $invoice->invoice_two_status ?> </small>
                                    <div class="input-group">
                                        <input type="text" class="form-control <?= $invoice->invoice_two_status == "S" ? "bg-success" : ($invoice->invoice_two_status == "N" ? "text-white bg-danger" : "") ?>" id="invoice_two_copy" value="<?= decryptData($invoice->invoice_two, $encryptionKey) ?>" readonly>
                                    </div>
                                </div>
                                <input class="btn btn-success" type="submit" value="Checar">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- End Status Invoice modal  -->

</div>

<?php require_once("templates/footer.php"); ?>

<script>
    function showPaidValue(i) {

        $("#invoice_status" + i).click(function() {

            var isChecked = $(this).prop("checked");

            if (isChecked) {
                $("#paid_value_div" + i).show();
                $("#value_paid" + i).val(0);
            } else {
                $("#paid_value_div" + i).hide();
            }

        });

        $("#invoice_status_total" + i).click(function() {
            var isChecked = $(this).prop("checked");
            var invoices_total = $("#invoice_status_total" + i).val(); // Correção: troque .value() por .val()

            if (isChecked) {
                $("#paid_value_div" + i).show();
                $("#value_paid" + i).val(invoices_total); // Correção: troque .value por .val()
            } else {
                $("#paid_value_div" + i).hide();
            }
        });


    }
</script>