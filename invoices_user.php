<?php
require_once("templates/header_iframe.php");
require_once("utils/config.php");
require_once("dao/InvoicesDAO.php");
require_once("dao/BankAccountsDAO.php");

// ---> Objeto contas e seus processos <-------  //
$bankAccountsDao = new BankAccountsDAO($conn, $BASE_URL);
$accounts = $bankAccountsDao->getAllBankAccounts();
// ---> Objeto contas <-------  //

$invoiceDao = new InvoicesDao($conn, $BASE_URL);

// Paginação do relatório
$totalRegistros = $invoiceDao->countInvoicesUser($userData->id);

$resultsPerPage = 10;
$numberPages = ceil($totalRegistros / $resultsPerPage);

// Pega numero da página atual
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
// calcula o indice do primeiro registro da página atual
$offset = ($page - 1) * $resultsPerPage;

$sql = "";
$invoice_id =
    $name_invoice =
    $reference_invoice =
    $account_invoice =
    $value_invoice =
    $emission =
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

    if (isset($_POST['emission']) && $_POST['emission'] != '') {
        $emission = $_POST['emission'];
        $sql .= " AND DATE(emission) = '$emission'";
    }

    if (isset($_POST['month_invoice']) && $_POST['month_invoice'] != '') {
        $month_invoice = $_POST['month_invoice'];
        $sql .= " AND MONTH(emission) = '$month_invoice' ";
    }

    //echo $sql . "<br>";
}

// Traz total de saídas do usuário default ou com paginação
$invoicesUser = $invoiceDao->getAllInvoicesUserToPagination($userData->id, $sql, $resultsPerPage, $offset);
$total_entry_value = 0;

?>

<div class="container-fluid">
    <h1 class="text-center my-5"> Receitas
        <img src="<?= $BASE_URL ?>assets/home/dashboard-main/full-wallet.png" width="64" height="64" alt="">
    </h1>

    <div class="row my-2 px-2">
        <div class="col-lg-12 d-flex justify-content-end">
            <button class="btn btn-lg btn-outline-secondary" id="limparCampos" title="Limpa todos os campos">Limpar</button>
        </div>
    </div>

    <div class=" entrys-search" id="entrys-search">
        <!-- <h3 class="text-secondary mb-3">Pesquisar:</h3> -->
        <form method="POST" id="meuFormulario">
            <input type="hidden" name="user_id" id="user_id" value="<?= $userData->id ?>">
            <div class="row ">
                <div class="col-lg-2 col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por id:</h4>
                        <input type="number" name="invoice_id" id="invoice_id" class="form-control" placeholder="Ex: 10" value="<?= $invoice_id ?>">
                    </div>
                </div>
                <div class="col-lg-3 col-md-5">
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
                <div class="col-lg-2 col-md-5">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por emissão:</h4>
                        <input class="form-control" type="date" name="emission" id="emission" value="<?= $emission ?>">
                    </div>
                </div>
                <div class="col-lg-2 col-md-5">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por mês:</h4>
                        <select class="form-control" name="month_invoice" id="">
                            <option value="">Selecione</option>
                            <?php foreach ($meses as $index => $mes) : ?>
                                <option value="<?= $index ?>" <?= $month_invoice == $index ? "selected" : ""; ?>><?= $mes ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-1 col-md-2">
                    <input class="btn btn-lg btn-success" type="submit" value="Buscar">
                    <!-- <button class="btn btn-lg btn-secondary" id="print_btn" onclick="print()"> Imprimir</button> -->
                </div>
            </div>
        </form>
    </div>

    <!-- table div thats receive all entrys without customize inputs parameters  -->
    <?php if($totalRegistros > 0): echo "aqui";?>
    <div class="table_report table-responsive my-3" id="table_report_entry">
        <h3 class="text-center text-secondary">Resultados:</h3>
        <div class="row d-block text-right my-2 px-3 info">
        </div>
        <table class="table table-hover table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Data</th>
                    <th scope="col">Fatura 1</th>
                    <th scope="col">Fatura 2</th>
                    <th scope="col">Referência</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Vencimento</th>
                    <th scope="col">Conta</th>
                    <th scope="col">Anotação</th>
                    <th scope="col" class="report-action">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoicesUser as $invoices) : ?>
                    <?php $value = $invoices->value;
                    $total_entry_value += (float) $value; ?>
                    <tr>
                        <th scope="row">
                            <?= $invoices->id ?>
                        </th>
                        <td>
                            <?= $invoices->emission ?>
                        </td>
                        <td class="d-flex justify-content-between <?= $invoices->invoice_one_status == "S" ? "bg-success" : ($invoices->invoice_one_status == "N" ? "bg-danger" : "table_description"); ?>"><?= $invoices->invoice_one ?>
                            <?php if ($invoices->invoice_one_status == "A") : ?>
                                <form action="" method="post">
                                    <label for="submit">
                                        <a href=""> <i class="fa-solid fa-file-invoice text-dark" title="clique para consultar o status"></i> </a>
                                    </label>
                                    <input type="submit" id="submit" value="">
                                </form>
                            <?php endif ?>
                        </td>
                        <td class=" <?= $invoices->invoice_two_status == "S" ? "bg-success" : ($invoices->invoice_two_status == "N" ? "bg-danger" : "table_description") ?>"><?= $invoices->invoice_two ?></td>
                        <td>
                            <?= $invoices->reference ?>
                        </td>
                        <td>
                            <?= number_format($invoices->value, 2, ",", ".") ?>
                        </td>
                        <td>
                            <?= $invoices->dt_expired ?>
                        </td>
                        <!-- <td class="info">
                            <?php if ($invoices->paid == "S") : ?>
                            <i class="fa-regular fa-square-check text-success"></i>
                            <?php else : ?>
                            <i class="fa-regular fa-square-check text-danger"></i>
                            <?php endif ?>
                        </td> -->
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

                        <td id="latest_moviments" class="report-action">
                            <a href="#" data-toggle="modal" data-target=".copyCodigoBoleto<?= $invoices->id ?>" title="Editar">
                                <i class="fa-solid fa-copy text-info"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10"> <strong> Total: </strong> R$
                        <?= number_format($total_entry_value, 2, ",", "."); ?>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Pagination buttons -->
        <?php if ($totalRegistros > 10) : ?>
            <!-- Pagination buttons -->
            <div class="row justify-content-center">
                <nav aria-label="...">
                    <ul class="pagination pagination-lg">
                        <?php for ($i = 1; $i <= $numberPages; $i++) : ?>
                            <?php $active = ($i == $page) ? "active-pagination" : ""; ?>

                            <li class="page-item <?= $active ?>">
                                <a class="page-link" href="<?= $BASE_URL ?>financial_exit_report.php?page=<?= $i ?>" tabindex="-1"><?= $i ?></a>
                            </li>

                        <?php endfor ?>
                    </ul>
                </nav>
            </div>
            <!-- End pagination buttons -->
        <?php endif ?>

        <!-- End pagination buttons -->
    </div>
    <?php else: ?>
        <div class="col-md-12">
            <hr class="hr">
            <h5 class="py-2 text-center text-info">Ainda não há faturas cadastradas.</h5>
        </div>
    <?php endif ?>

</div>

    <!-- Copy Invoice numbers modal -->
    <?php foreach ($invoicesUser as $invoice) : ?>
        <div class="modal fade copyCodigoBoleto<?= $invoice->id ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top">
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
    <?php endforeach; ?>
    <!-- End Invoice modal Copy -->

<?php require_once("templates/footer.php"); ?>

<script>
    // Limpa inputs dos formulário
    document.addEventListener('DOMContentLoaded', function() {
        const formulario = document.getElementById('meuFormulario');
        const limparBotao = document.getElementById('limparCampos');

        limparBotao.addEventListener('click', function() {
            const inputs = formulario.querySelectorAll('input, select');
            inputs.forEach(function(input) {
                if (input.type !== 'submit' && input.type !== 'reset') {
                    input.value = '';
                }
            });
        });
    });
    // Fim limpa inputs do formulário

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