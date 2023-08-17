<?php
require_once("templates/header_iframe.php");
require_once("utils/check_levels_acess_admin.php");
require_once("globals.php");
require_once("utils/config.php");
require_once("connection/conn.php");
require_once("dao/BankAccountsDAO.php");

// $bankAccout = new BankAccounts();
$bankAccountsDao = new BankAccountsDAO($conn, $BASE_URL);

// traz todos os cartões do usuário
$accounts = $bankAccountsDao->getAllBankAccounts();


?>

<div class="container-fluid">
    <h1 class="text-center text-secondary my-3">Contas cadastradas</h1>

    <hr class="hr">
    <section>

        <!-- Each Card  -->
        <div class="row card_example" id="cards-page">
            <?php foreach ($accounts as $account) : ?>
                <div class="col-lg-4 col-md-6  my-3">
                    <div class="card-credit shadow" id="card-credit-bg" style="background: <?= $account->card_color ?>">

                        <div class="card_info">
                            <div class="bg-white rounded" style="display: inline-block">
                                <img src="<?= $BASE_URL ?>assets/home/contas/<?= $account->bank_logo ?>" alt="">
                            </div>
                            <span class="text-white"><?= $account->cod . " - " . $account->bank_name ?></span>
                            <p class="mt-3" id="card_number"> CNPJ <?= decryptData($account->cnpj, $encryptionKey) ?></p>
                        </div>
                        <div class="card_pix">
                            <p class="text-white ml-2" id="chave_pix">Pix: <?= decryptData($account->pix, $encryptionKey)?></p>
                        </div>

                        <div class="card_crinfo">
                            <p id="card_name">
                                <small> Razão social: </small> <br>
                                <?= decryptData($account->razao_social, $encryptionKey) ?>
                            </p>

                            <div class="form-group d-flex text-center">
                                <div class="px-3">
                                    <small class="text-light">Agencia</small>
                                    <p id="agencia"><?= decryptData($account->agencia,$encryptionKey)?></p>
                                </div>
                                <div>
                                    <small class="text-light">Conta</small>
                                    <p id="conta"><?= decryptData($account->conta, $encryptionKey) ?></p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="text-center my-2">
                        <a href="#" data-toggle="modal" data-target="#card_modal_edit<?= $account->id?>" title="Editar">
                            <i class="fa-solid fa-file-pen"></i>
                        </a>
                        <a href="#" data-toggle="modal" data-target="#card_del<?= $account->id ?>"><i class="fa-solid fa-trash-can"></i></a>
                    </div>
                </div>
            <?php endforeach ?>


            <?php if (count($accounts) === 0) : ?>
                <h4 class="col text-center">Nenhuma conta cadastrada</h4>
            <?php endif; ?>


        </div>
    </section>
    <!-- Each Card  -->
</div>

<!-- Card modal edit -->
    <?php foreach($accounts as $account): ?>
    <div class="modal fade" id="card_modal_edit<?= $account->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar conta</h5>
                    <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                        <span arial-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= $BASE_URL ?>account_process.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $account->id ?>">
                        <input type="hidden" name="type" value="update">
                        <input type="hidden" name="banco" value="<?= $account->banco ?>">
                        <div class="form-group">
                            <label for="razao">Razão Social:</label>
                            <input type="text" name="razao" id="razap" class="form-control" value="<?= decryptData($account->razao_social, $encryptionKey) ?>">
                        </div>
                        <div class="form-group">
                            <label for="cnpj">CNPJ:</label>
                            <input type="text" name="cnpj" id="cnpj" class="form-control" value="<?= decryptData($account->cnpj, $encryptionKey) ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ag">Agência:</label>
                                    <input type="text" name="ag" id="ag" class="form-control" value="<?= decryptData($account->agencia, $encryptionKey) ?>">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="cc">Conta:</label>
                                    <input type="text" name="cc" id="cc" class="form-control" value="<?= decryptData($account->conta, $encryptionKey) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="pix">Chave Pix:</label>
                                    <input type="text" name="pix" id="pix" class="form-control" value="<?= decryptData($account->pix, $encryptionKey) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="color">Cor:</label>
                                    <input type="color" name="color" id="color" class="form-control" value="<?= $account->card_color ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="logo">Logo:</label>
                            <input type="hidden" name="current_file" value="<?= $account->logo_img ?>">
                            <input class="form-control" type="file" name="image" id="image" value="<?= $account->logo_img ?>">
                        </div>
                        <input type="submit" value="Atualizar" class="btn btn-lg btn-success" onclick="scrollToTop()">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach ?>
<!-- End Card modal edit -->

<!-- Card modal delete -->
<?php foreach ($accounts as $account) : ?>
    <div class="modal fade" tabindex="-1" id="card_del<?= $account->id ?>">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Tem certeza que deseja excluir o registro?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                    <form action="<?= $BASE_URL ?>account_process.php" method="POST">
                        <input type="hidden" name="type" value="delete">
                        <input type="hidden" name="id" value="<?= $account->id?>">
                        <input type="hidden" name="current_file" value="<?= $account->logo_img ?>">
                        <button type="submit" class="btn btn-primary">Sim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<!-- End card modal delete -->

<?php require_once("templates/footer.php"); ?>
