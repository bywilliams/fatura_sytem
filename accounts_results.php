<?php
require_once("templates/header_iframe.php");
require_once("connection/conn.php");
require_once("globals.php");
require_once("utils/config.php");
require_once("dao/BankAccountsDAO.php");
require_once("dao/InvoicesDAO.php");


// ---> Objeto contas e seus processos <-------  //
$bankAccountsDao = new BankAccountsDAO($conn, $BASE_URL);
$accounts = $bankAccountsDao->getAllBankAccounts();
// ---> Objeto contas <-------  //

$bankAccountsDao = new BankAccountsDAO($conn, $BASE_URL);

// traz todos os cartões do usuário
$accounts = $bankAccountsDao->getAllBankAccounts();

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
$account_id = 
$user_id = $userData->id;


if ($_GET) {

    if (isset($_GET['account_id']) && $_GET['account_id'] != '') { 
        $account_id = $_GET['account_id'];
        $sql .= "AND account = $account_id";
    }
    //echo $sql . "<br>"; 
}

// Traz total de saídas do usuário default ou com paginação
if ($sql) {
    $invoicesUser = $invoiceDao->getAllInvoicesUserToPagination($userData->id, $sql, $resultsPerPage, $offset);
}

$total_entry_value = 0;

?>

<div class="container-fluid">
    <h1 class="text-center text-secondary my-3">Contas</h1>

    <hr class="hr">

    <!-- Contas com dados para copiar e mostrar resultados -->
    <section>
        <div class="card_results" id="cards-page">
            <div class="row">
                <?php foreach ($accounts as $account) : 
                     $account_id = $account->id;
                    ?>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card-credit px-2" id="card-credit-bg shadow" style="background: <?= $account->card_color ?>">
                            <div class="my-3 d-flex justify-content-between">
                                <div class="text-white">
                                    <img src="<?= $BASE_URL ?>assets/home/contas/<?= $account->bank_logo ?>" alt="">
                                    <span class="copy-data"> <?= $account->cod . " - " . $account->bank_name ?> </span>
                                </div>
                                <button class="copy-all-btn" data-clipboard-group="<?= $account->id ?>">Tudo</button>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <p class="text-white copy-data" id="razao<?= $account->id ?>"> <?= decryptData($account->razao_social, $encryptionKey) ?> </p>
                                <button class="copy-btn" data-clipboard-target="#razao<?= $account->id ?>">Copiar</button>
                            </div>
                            <div class="card_pix d-flex justify-content-between">
                                <p class="text-white copy-data" id="cnpj<?= $account->id ?>"><?= decryptData($account->cnpj, $encryptionKey) ?> </p>
                                <button class="copy-btn" data-clipboard-target="#cnpj<?= $account->id ?>">Copiar</button>
                            </div>
                            <div class="card_pix d-flex justify-content-between">
                                <p class="text-white copy-data" id="agencia<?= $account->id ?>">AG: <?= decryptData($account->agencia, $encryptionKey) ?> </p>
                                <button class="copy-btn" data-clipboard-target="#agencia<?= $account->id ?>">Copiar</button>
                            </div>
                            <div class="card_pix d-flex justify-content-between">
                                <p class="text-white copy-data" id="conta<?= $account->id ?>">Conta: <?= decryptData($account->conta, $encryptionKey) ?> </p>
                                <button class="copy-btn" data-clipboard-target="#conta<?= $account->id ?>">Copiar</button>
                            </div>
                            <div class="card_pix d-flex justify-content-between">
                                <p class="text-white copy-data" id="chave_pix<?= $account->id ?>">Chave pix: <?= decryptData($account->pix, $encryptionKey) ?> </p>
                                <button class="copy-btn" data-clipboard-target="#chave_pix<?= $account->id ?>">Copiar</button>
                            </div>

                        </div>
                        <div class="d-flex justify-content-center" id="account<?= $account->id ?>">
                            <form class="form" action="accounts_results.php" id="account_<?= $account->id ?>" method="GET">
                                <input type="hidden" name="account_id" value="<?= $account_id ?>">
                                <label for="submit<?= $account_id ?>">
                                </label>
                                <input class="account-btn" type="submit" id="submit<?= $account_id ?>" value="+" onclick="submitForm(<?= $account_id ?>)">
                            </form>
                        </div>
                    </div>
                <?php endforeach ?>


                <?php if (count($accounts) === 0) : ?>
                    <h4 class="col text-center">Nenhuma conta cadastrada</h4>
                <?php endif; ?>

            </div>
        </div>
    </section>
     <!-- Fim Contas com dados para copiar e mostrar resultados -->

     <!-- table div thats receive all entrys without customize inputs parameters  -->
    <?php if (!empty($sql)): ?>
    <div class="table_report my-3" id="table_report_entry">
    <h3 class="text-center text-secondary">Resultados:</h3>
        <div class="row d-block text-right my-2 px-3 info">
            <!-- <div> <i class="fa-regular fa-square-check text-success"></i> <span> Fatura paga </span> </div>
            <div> <i class="fa-regular fa-square-check text-danger"></i> <span> Fatura não paga </span> </div>
            <div> <i class="fa-regular fa-square-check text-secondary"></i> <span> Aguard. pagamento </span> </div> -->
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
                    <!-- <th scope="col">Status</th> -->
                    <th scope="col">Conta</th>
                    <th scope="col">Anotação</th>
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
                                    <?= $invoice->invoice_one_status ?>
                                </td>
                            <?php elseif ($invoice->invoice_one_status == "NAO PAGO - Em Aberto") : ?>
                                <td class="bg-danger text-white">
                                    <?= $invoice->invoice_one_status ?>
                                </td>
                            <?php else : ?>
                                <td class="">
                                    <?= $invoice->invoice_one_status ?>
                                </td>
                            <?php endif ?>
                            <?php if ($invoice->invoice_two_status == "PAGO - Baixado" || $invoice->invoice_two_status == "PAGO - Liquidado") : ?>
                                <td class="bg-success text-white">
                                    <?= $invoice->invoice_two_status ?>
                                </td>
                            <?php elseif ($invoice->invoice_two_status == "NAO PAGO - Em Aberto") : ?>
                                <td class="bg-danger text-white">
                                    <?= $invoice->invoice_two_status ?>
                                </td>
                            <?php else : ?>
                                <td class="">
                                    <?= $invoice->invoice_two_status ?>
                                </td>
                            <?php endif ?>
                        <td>
                            <?= $invoice->reference ?>
                        </td>
                        <td>
                            <?= number_format($invoice->value,2 , ",", ".") ?>
                        </td>
                        <td>
                        <?= number_format($invoice->ammount_paid,2 , ",", ".") ?>
                        </td>
                        <td>
                            <?= $invoice->dt_expired ?>
                        </td>
                       
                        <td>
                            <div class="invoice_card_img">
                                <img src="<?= $BASE_URL ?>assets/home/contas/<?= $invoice->conta_img ?>"  alt="">
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
    <?php endif ?>

</div>

<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      ...
    </div>
  </div>
</div>

<?php require_once("templates/footer.php"); ?>

<script>
var clipboard = new ClipboardJS('.copy-btn');

clipboard.on('success', function(event) {
    alert('Texto copiado com sucesso!');
    event.clearSelection();
});

clipboard.on('error', function(event) {
    //alert('Erro ao copiar texto. Tente novamente.');
});

// Aguarde o carregamento completo do DOM antes de executar o código
$(document).ready(function() {
    // Selecione todos os botões com a classe "copy-all-btn"
    $(".copy-all-btn").each(function() {
        var clipboard = new ClipboardJS(this, {
            // Copie os textos dos parágrafos associados ao card do botão
            text: function(trigger) {
                var card = $(trigger).closest(".card-credit");
                var textToCopy = card.find(".copy-data").map(function() {
                    return $(this).text();
                }).get().join("\n ");
                return textToCopy;
            }
        });

        // Exiba um aviso ou feedback quando o texto for copiado com sucesso
        clipboard.on("success", function(e) {
            alert("Texto copiado: " + e.text);
            // Aqui você pode exibir um aviso visual, como uma notificação ou um alerta
        });

        // Trate os erros, se houver
        clipboard.on("error", function(e) {
            alert.error("Erro ao copiar texto: ", e.action, e.trigger);
            // Aqui você pode exibir uma mensagem de erro ao usuário
        });
    });
});


function submitForm(account_id) {
        // Trigger the form submission for the corresponding account_id
        document.getElementById('account_' + account_id).submit();
}


</script>

