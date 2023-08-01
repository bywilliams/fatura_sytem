<?php
require_once("templates/header_iframe.php");
require_once("dao/FinancialMovimentDAO.php");
require_once("dao/CategorysDAO.php");

$financialMovimentDao = new FinancialMovimentDAO($conn, $BASE_URL);
$categorysDao = new CategorysDAO($conn);

// Traz todas as categorias disponiveis para entradas
$entry_categorys = $categorysDao->getAllEntryCategorys();

// Traz o array com os dados de entradas da query personalizada para modal
$sql = "";
$getEntryReports = $financialMovimentDao->getReports($sql, 1, $userData->id);

// Paginação do relatório
$totalRegistros = $financialMovimentDao->countTypeFinancialCurrentMonth($userData->id, 1);

$resultsPerPage = 10;
$numberPages = ceil($totalRegistros / $resultsPerPage);

// Pega numero da página atual
$page = isset($_GET["page"]) ? $_GET["page"] : 1;

// calcula o indice do primeiro registro da página atual
$offset = ($page - 1) * $resultsPerPage;

// Traz total de Entradas do usuário default e páginação 
$entryFinancialMoviments = $financialMovimentDao->getAllEntryFinancialMoviment($userData->id, $resultsPerPage, $offset);

?>

<style>
    input[type="date"]::-webkit-inner-spin-button,
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: none !important;
        -webkit-appearance: none !important;
    }
</style>

<div class="container-fluid">
    <h1 class="text-center my-5">Todas as faturas
        <!-- <img src="<?= $BASE_URL ?>assets/home/dashboard-main/full-wallet.png" width="64" height="64" alt=""> -->
        <i class="fa-solid fa-file-invoice"></i>
    </h1>

    <!-- <div class="entrys-search" id="entrys-search">
        <form method="POST">
            <input type="hidden" name="user_id" id="user_id" value="<?= $userData->id ?>">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por nome:</h4>
                        <input type="text" name="name_search" id="name_search_entry" class="form-control" placeholder="Ex: salário">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Por valor:</h4>
                        <select class="form-control" name="values_entry" id="values_entry">
                            <option value="">Selecione</option>
                            <option value="500">até R$ 500,00</option>
                            <option value="1500">de R$ 500 até R$ 1.500,00</option>
                            <option value="3000">de R$ 1.500 até R$ 3.000,00</option>
                            <option value="5000">R$ 3.000 até R$ 5.000,00</option>
                            <option value="10000">Acima de R$ 5.000,00</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">De:</h4>
                        <input type="text" name="from_date_entry" id="from_date_entry" class="form-control placeholder" placeholder="__/__/____">
                        <div class="p-date" onclick="show_password()">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Até:</h4>
                        <input type="text" name="to_date_entry" id="to_date_entry" class="form-control placeholder" placeholder="__/__/____">
                        <div class="p-date" onclick="show_password()">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Categoria:</h4>
                        <select class="form-control" name="category_entry" id="category_entry">
                            <option value="">Selecione</option>
                            <?php foreach ($entry_categorys as $category) : ?>
                                <option value="<?= $category->id ?>"> <?= $category->category_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">

                    <button class="btn btn-lg btn-secondary" id="print_btn" onclick="print()"> Imprimir </button>
                </div>
            </div>
        </form>
    </div> -->

    <div class="table_report my-3" id="search_entry"></div>

    <!-- table div thats receive all entrys without customize inputs parameters  -->
    <div class="table_report" id="table_report_entry">
        <h3 class="text-center text-secondary">Resultados:</h3>
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
                <?php foreach ($entryFinancialMoviments as $entryFinancialMovimentItem) : ?>
                    <?php $value = str_replace('.', '', $entryFinancialMovimentItem->value);
                    $total_entry_value += (float) $value; ?>

                    <tr>
                        <th scope="row">
                            <?= $entryFinancialMovimentItem->id ?>
                        </th>
                        <td>
                            01/07/2023 09:15
                        </td>
                        <td>
                            <?= $entryFinancialMovimentItem->description ?>
                        </td>
                        <td>
                            <?= $entryFinancialMovimentItem->value ?>
                        </td>
                        <td>
                            <?= $entryFinancialMovimentItem->create_at ?>
                        </td>
                        <td>
                            010101-01
                        </td>

                        <td>
                            <?php if ($entryFinancialMovimentItem->obs != "") : ?>
                                <a href="#!" id="grupos<?= $entryFinancialMovimentItem->id ?>" onclick="openTooltip(<?= $entryFinancialMovimentItem->id ?>)"><img src="<?= $BASE_URL ?>assets/home/dashboard-main/message_alert.gif" alt="message_alert" title="ver observação" width="33" height="30"> </a>
                                <div class="tooltip_" id="tooltip_<?= $entryFinancialMovimentItem->id ?>">
                                    <div id="conteudo">
                                        <div class="bloco">
                                            <h5>Observação</h5>
                                            <a href="#!" id="close<?= $entryFinancialMovimentItem->id ?>"><i class="fa-solid fa-xmark"></i></a>
                                        </div>
                                        <div class="bloco">
                                            <small>
                                                <?= $entryFinancialMovimentItem->obs ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td id="latest_moviments" class="report-action"><a href="#" data-toggle="modal" data-target="#exampleModalCenter<?= $entryFinancialMovimentItem->id ?>" title="Editar">
                                <i class="fa-solid fa-file-pen"></i></a>
                            <a href="#!" data-toggle="modal" data-target="#modal_del_finance_moviment<?= $entryFinancialMovimentItem->id ?>" title="Deletar"><i class="fa-solid fa-trash-can"></i></a>
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


    <!-- Finance all revenue moviment modal -->
    <?php foreach ($entryFinancialMoviments as $entryFinancialMovimentItem) : ?>
        <div class="modal fade" id="exampleModalCenter<?= $entryFinancialMovimentItem->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Movimentação</h5>
                        <button type="button" class="close_reports" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= $BASE_URL ?>moviment_process.php?id=<?= $entryFinancialMovimentItem->id ?>" method="post">
                            <input type="hidden" name="type" value="edit">
                            <div class="form-group">
                                <label for="description">Descriçao:</label>
                                <input type="text" name="description_edit" id="" class="form-control" placeholder="Insira uma nova descrição" value="<?= $entryFinancialMovimentItem->description ?>">
                            </div>
                            <div class="form-group">
                                <label for="value">Valor:</label>
                                <input type="text" name="value_edit" id="" class="form-control money" placeholder="Insira um novo valor" value="<?= $entryFinancialMovimentItem->value ?>">
                            </div>
                            <div class="form-group">
                                <label for="category">Categoria:</label>
                                <select name="category_edit" id="" class="form-control">
                                    <?php foreach ($entry_categorys as $category) : ?>
                                        <?php if ($category->category_name == $entryFinancialMovimentItem->category) : ?>
                                            <option value="<?= $category->id ?>" selected> <?= $category->category_name ?></option>
                                        <?php else : ?>
                                            <option value="<?= $category->id ?>"> <?= $category->category_name ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="obs">Observação:</label>
                                <textarea class="form-control" name="obs" id="obs" rows="5" placeholder="Adicione uma observação..."><?= $entryFinancialMovimentItem->obs ?></textarea>
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
    <!-- End Finance moviment modal -->

    <!-- Edit Finance customize expense moviment modal -->
    <?php foreach ($getEntryReports as $customizeFinancialMovimentItem) : ?>
        <div class="modal fade" id="customizeExpenseQuery<?= $customizeFinancialMovimentItem->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Movimentação</h5>
                        <button type="button" class="close close_reports" data-dismiss="modal" arial-label="fechar">
                            <span arial-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= $BASE_URL ?>moviment_process.php?id=<?= $customizeFinancialMovimentItem->id ?>" method="post">
                            <input type="hidden" name="type" value="edit">
                            <div class="form-group">
                                <label for="description">Descriçao:</label>
                                <input type="text" name="description_edit" id="" class="form-control" placeholder="Insira uma nova descrição" value="<?= $customizeFinancialMovimentItem->description ?>">
                            </div>
                            <div class="form-group">
                                <label for="value">Valor:</label>
                                <input type="text" name="value_edit" id="" class="form-control money" placeholder="Insira um novo valor" value="<?= $customizeFinancialMovimentItem->value ?>">
                            </div>
                            <?php if ($customizeFinancialMovimentItem->type == 2) : ?>
                                <div class="form-group">
                                    <label for="expense_type">Despesa:</label>
                                    <?php if ($customizeFinancialMovimentItem->expense == "Fixa") : ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio1" value="F" checked>
                                            <label class="edit_moviment_label" for="inlineRadio1">Fixa</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="expense_type_edit" id="inlineRadio2" value="V">
                                            <label class="edit_moviment_label" for="inlineRadio2">Variada</label>
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
    <!-- End Edit Finance moviment modal -->

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