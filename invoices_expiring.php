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

$currentDate = date("Y-m-d");

$sql = " AND DATE(dt_expired) = '$currentDate' ";


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

    if (isset($_POST['account_invoice']) && $_POST['account_invoice'] != '') {
        $account_invoice = $_POST['account_invoice'];
        $sql .= " AND account = $account_invoice";
    }

    if (isset($_POST['day']) && $_POST['day'] != '') {
        $day = $_POST['day'];
        $sql .= " AND DAY(dt_expired) = '$day'";
    }

    if (isset($_POST['date']) && $_POST['date'] != '') {
        $date = $_POST['date'];
        $sql .= " AND DATE(dt_expired) = '$date'";
    }

    if (isset($_POST['month_invoice']) && $_POST['month_invoice'] != '') {
        $month_invoice_input = $_POST['month_invoice'];
        $sql .= " AND MONTH(dt_expired) = '$month_invoice_input' ";
    }

    // echo $sql . "<br>";
}

// Traz total de saídas do usuário default ou com paginação
$invoicesUser = $invoiceDao->getAllInvoicesUserExpiringToPagination($userData->id, $sql, $resultsPerPage, $offset);
//echo $totalRegistros;
$total_entry_value = 0;

$dias = [];
for ($i = 1; $i <= 31; $i++) {
    $dias[] = $i;
}


?>

<div class="container-fluid">
    <h1 class="text-center my-5"> Receitas Vencendo hoje
        <img src="<?= $BASE_URL ?>assets/home/dashboard-main/invoice_expiring.png" width="64" height="64" alt="">
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
            <div class="row ">
                <div class="col-lg-1">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por id:</h4>
                        <input type="number" name="invoice_id" id="invoice_id" class="form-control" placeholder="Ex: 10" value="<?= $invoice_id ?>">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por referência:</h4>
                        <input type="text" name="reference_invoice" id="reference_invoice" class="form-control" placeholder="Ex: REF: 10" value="<?= $invoice_expense ?>">
                    </div>
                </div>
                <div class="col-lg-2">
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
                <div class="col-lg-2 col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por dia:</h4>
                        <select class="form-control" name="day" id="day">
                            <option value="<?= $d ?>">Selecione</option>
                            <?php foreach ($dias as $d) : ?>
                                <option value="<?= $d ?>" <?= $day == $d ? "selected" : ""; ?>><?= $d ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <h4>Por data</h4>
                        <input class="form-control" type="date" name="date" id="date" value="<?= $date ?>">
                    </div>
                </div>
                <div class="col-lg-2 ">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por mês:</h4>
                        <select class="form-control" name="month_invoice" id="">
                            <option value="">Selecione</option>
                            <?php foreach ($meses as $index => $mes) : ?>
                                <option value="<?= $index ?>"><?= $mes ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-1">
                    <input class="btn btn-lg btn-success" type="submit" value="Buscar">
                    <!-- <button class="btn btn-lg btn-secondary" id="print_btn" onclick="print()"> Imprimir</button> -->
                </div>
            </div>
        </form>
    </div>

    <!-- table div thats receive all entrys without customize inputs parameters  -->
    <?php if (count($invoicesUser) > 0) : ?>
        <div class="table_report table-responsive my-3" id="table_report_entry">
            <h3 class="text-center text-secondary">Resultados:</h3>
            <hr class="hr">
            <div class=" d-flex justify-content-end  my-2 info">
                <div> <i class="fa-solid fa-square text-success"></i> <span> Fatura paga </span> </div>
                <div> <i class="fa-solid fa-square text-danger"></i> <span> Fatura não paga </span> </div>
                <div> <i class="fa-solid fa-receipt fa-2x text-sucsess"></i> <span> Status da fatura</span> </div>
                <div> <i class="fa-solid fa-copy fa-2x text-secondary"></i> <span> Copiar </span> </div>
            </div>
            <table class="table table-hover table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Data</th>
                        <th scope="col">Fatura 1</th>
                        <th scope="col">Fatura 2</th>
                        <th scope="col">Referência</th>
                        <th scope="col">Valor da fatura</th>
                        <th scope="col">Valor pago</th>
                        <th scope="col">Vencimento</th>
                        <th scope="col">Conta</th>
                        <th scope="col">Anotação</th>
                        <th scope="col" class="report-action">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoicesUser as $invoice) : ?>
                        <?php $value = $invoice->value;
                        $total_entry_value += (float) $value; ?>

                        <tr>
                            <th scope="row">
                                <?= $invoice->id ?>
                            </th>
                            <td>
                                <?= $invoice->emission ?>
                            </td>
                            <?php if ($invoice->invoice_one_status == "PAGO - Baixado" || $invoice->invoice_one_status == "PAGO - Liquidado") : ?>
                                <td class="bg-success text-white">
                                    <small> <?= $invoice->invoice_one_status ?> </small>
                                </td>
                            <?php elseif ($invoice->invoice_one_status == "NAO PAGO - Em Aberto") : ?>
                                <td class="bg-danger text-white">
                                    <small> <?= $invoice->invoice_one_status ?></small>
                                </td>
                            <?php else : ?>
                                <td class="">
                                    <small> <?= $invoice->invoice_one_status ?></small>
                                </td>
                            <?php endif ?>
                            <?php if ($invoice->invoice_two_status == "PAGO - Baixado" || $invoice->invoice_two_status == "PAGO - Liquidado") : ?>
                                <td class="bg-success text-white">
                                    <small> <?= $invoice->invoice_two_status ?></small>
                                </td>
                            <?php elseif ($invoice->invoice_two_status == "NAO PAGO - Em Aberto") : ?>
                                <td class="bg-danger text-white">
                                    <small> <?= $invoice->invoice_two_status ?></small>
                                </td>
                            <?php else : ?>
                                <td class="">
                                    <small> <?= $invoice->invoice_two_status ?></small>
                                </td>
                            <?php endif ?>
                            <td>
                                <?= $invoice->reference ?>
                            </td>
                            <td>
                                <?= $invoice->value ?>
                            </td>
                            <td><?= $invoice->ammount_paid ?></td>
                            <td>
                                <?= $invoice->dt_expired ?>
                            </td>
                            <td>
                                <div class="invoice_card_img px-2">
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
                            <td id="latest_moviments" class="report-action">
                                <a href="#" data-toggle="modal" data-target=".copyCodigoBoleto<?= $invoice->id ?>" title="Editar">
                                    <i class="fa-solid fa-copy text-info"></i>
                                </a>
                                <a href="#" data-toggle="modal" data-target=".checkStatusInvoice<?= $invoice->id ?>">
                                    <i class="fa-solid fa-receipt fa-2x text-sucsess"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="11"> <strong> Total: </strong> R$
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
    <?php else : ?>
        <div class="col-md-12">
            <hr class="hr">
            <h5 class="py-2 text-center text-info">Sem faturas a vencer hoje.</h5>
        </div>
    <?php endif ?>


</div>

<!-- Status Invoice modal -->
<?php foreach ($invoicesUser as $invoice) : ?>
    <div class="modal fade checkStatusInvoice<?= $invoice->id ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Status do boleto</h5>
                    <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                        <span arial-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <form action="<?= $BASE_URL ?>consulta.php" method="post">
                            <input type="hidden" name="linha_digitavel" value="<?= decryptData($invoice->invoice_one, $encryptionKey) ?>">
                            <input type="hidden" name="id" value="<?= $invoice->id ?>">
                            <input type="hidden" name="invoice_type" value="invoice_one_status">
                            <input type="hidden" name="current_status" value="<?= $invoice->invoice_one_status ?>">
                            <div class="form-group">
                                <label for="invoice_one_copy">Fatura 1</label>
                                <div class="input-group">
                                    <input type="text" class="form-control <?= $invoice->invoice_one_status == "S" ? "bg-success" : ($invoice->invoice_one_status == "N" ? "text-white bg-danger" : "") ?>" id="invoice_one_copy" value="<?= decryptData($invoice->invoice_one, $encryptionKey) ?>" readonly>
                                </div>
                            </div>

                            <input class="btn btn-success" type="submit" value="Checar">
                        </form>
                    </div>

                    <div class="form-group">
                        <form action="<?= $BASE_URL ?>consulta.php" method="post">
                            <input type="hidden" name="linha_digitavel" value="<?= decryptData($invoice->invoice_two, $encryptionKey) ?>">
                            <input type="hidden" name="id" value="<?= $invoice->id ?>">
                            <input type="hidden" name="invoice_type" value="invoice_two_status">
                            <input type="hidden" name="current_status" value="<?= $invoice->invoice_two_status ?>">
                            <div class="form-group">
                                <label for="invoice_two_copy">Fatura 2</label>
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
                            <input type="text" class="form-control" id="invoice_one_copy" value="<?= decryptData($invoice->invoice_one, $encryptionKey) ?>" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-success copy-btn" data-clipboard-target="#invoice_one_copy">Copiar</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="invoice_two_copy">Fatura 2</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="invoice_two_copy" value="<?= decryptData($invoice->invoice_two, $encryptionKey) ?>" readonly>
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