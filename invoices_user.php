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
$account_invoice =
$value_invoice =
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
        $month_invoice_input = $_POST['month_invoice'];
        $sql .= " AND MONTH(dt_expired) = '$month_invoice_input' ";
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

    <div class="entrys-search" id="entrys-search">
        <!-- <h3 class="text-secondary mb-3">Pesquisar:</h3> -->
        <form method="POST">
            <input type="hidden" name="user_id" id="user_id" value="<?= $userData->id ?>">
            <div class="row offset-sm-2">
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por id:</h4>
                        <input type="number" name="invoice_id" id="invoice_id" class="form-control" placeholder="Ex: 10" value="<?= $invoice_id ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por referência:</h4>
                        <input type="text" name="reference_invoice" id="reference_invoice" class="form-control" placeholder="Ex: REF: 10" value="<?= $invoice_expense ?>">
                    </div>
                </div>
                <!-- <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por valor:</h4>
                        <select class="form-control" name="value_invoice" id="value_invoice">
                            <option value="">Selecione</option>
                            <optgroup label="de 10,00 até 100,00">
                                <option value="10-100">10,00 até 100,00</option>
                            </optgroup>
                            <optgroup label="de 100,00 até 500,00">
                                <option value="100-500">100,00 até 500,00</option>
                            </optgroup>
                            <optgroup label="de 500,00 até 1.500,00">
                                <option value="500-1500">500,00 até 1.500,00</option>
                            </optgroup>
                            <optgroup label="de 1.500,00 até 3.000,00">
                                <option value="1500-3000">1.500,00 até 3.000,00</option>
                            </optgroup>
                            <optgroup label="acima de 3.000,00">
                                <option value="+3000">acima de 3.000,00</option>
                            </optgroup>
                        </select>

                    </div>
                </div> -->
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por conta:</h4>
                        <select class="form-control" name="account_invoice" id="account_invoice">
                            <option value="">Selecione</option>
                            <?php foreach ($accounts as $account) : ?>
                                <option value="<?= $account->id ?>" value="<?= $account_invoice == $account->id ? "selected" : ""; ?>"><?= decryptData($account->razao_social, $encryptionKey) ?></option>
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
                                <option value="<?= $index ?>"><?= $mes ?></option>
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
    <h3 class="text-center text-secondary">Resultados:</h3>
        <div class="row d-block text-right my-2 px-3 info">
        <div> <i class="fa-regular fa-square-check text-success"></i> <span> Fatura paga </span> </div>
        <div> <i class="fa-regular fa-square-check text-danger"></i> <span> Fatura não paga </span> </div>
        <div> <i class="fa-regular fa-square-check text-secondary"></i> <span> Aguard. pagamento </span> </div>
        </div>
        <table class="table table-hover table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Data</th>
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
                            <?php if ($invoices->paid == "S"): ?>
                            <i class="fa-regular fa-square-check text-success"></i>
                            <?php else: ?>
                            <i class="fa-regular fa-square-check text-danger"></i>
                            <?php endif ?>
                        </td> -->
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

                        <td id="latest_moviments" class="report-action">
                            <a href="#" data-toggle="modal" data-target="#editInvoiceModal<?= $invoices->id ?>" title="Editar">
                                <i class="fa-solid fa-file-pen"></i>
                            </a>
                            <a href="#" data-toggle="modal" data-target="#del_invoice<?= $invoices->id ?>" title="Deletar">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
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

        <!-- Pagination buttons -->
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

        <!-- End pagination buttons -->
    </div>


   <!-- Invoice moviment modal Edit -->
   <?php foreach ($invoicesUser as $invoice) : ?>
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
                                        <input class="form-control" type="date" name="dt_expired" id="" value="<?= $invoice->dt_expired ?>" required>
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
    <?php foreach ($invoicesUser as $invoice) : ?>
        <div class="modal fade" id="del_invoice<?= $invoice->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
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