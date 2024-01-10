<?php
require_once("templates/header_iframe.php");
require_once("utils/check_levels_acess_admin.php");
require_once("dao/BanksDAO.php");

$banksDao = new banksDAO($conn, $BASE_URL);

$allBanks = $banksDao->getAllBanks();


isset($_SESSION['cod']) ? $_SESSION['cod'] : $_SESSION['cod'] = null;
isset($_SESSION['name']) ? $_SESSION['name'] : $_SESSION['name'] = null;


?>

<div class="container-fluid">
    <!-- Form cadastrar banco -->
    <section>
        <div class="container my-5 actions p-3 mb-3 bg-light rounded-3 shadow-sm">
            <h1 class="text-center mb-5 text-secondary">Adicionar banco <i class="fa-solid fa-building-columns"></i></h1>
            <form action="<?= $BASE_URL ?>bank_process.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="type" value="create">
                <div class="row d-flex justify-content-center text-center">
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <h4 class="font-weight-normal">Codigo:</h4>
                            <input class="form-control" type="number" name="cod" id="cod" placeholder="001" value="<?= $_SESSION['cod'] ?>">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <h4 class="font-weight-normal">Nome do banco:</h4>
                            <input class="form-control" type="text" name="name" id="name" placeholder="ex: BB" value="<?= $_SESSION['name'] ?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <h4 class="font-weight-normal">Logo:</h4>
                            <input class="form-control" type="file" name="image" id="image" placeholder="ex: BB">
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-6 text-center">
                        <input class="btn btn-lg btn-success" type="submit" value="Adicionar">
                    </div>
                </div>

            </form>
        </div>
    </section>
    <!-- Form cadastrar banco -->

    <div class="container">
        <?php if(count($allBanks) > 0): ?>
        <hr class="hr">
        <!-- Tabela contas cadastradas -->
        <section>
            <div class="table-responsive ">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <th colspan="7">
                            <h4 class="text-white"> Bancos cadastrados </h4>
                        </th>
                    </thead>
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">id</th>
                            <th scope="col">logo</th>
                            <th scope="col">Codigo</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Criado em</th>
                            <th scope="col">Atualizado em</th>
                            <th scope="col">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allBanks as $bank) : ?>
                            <tr class="mb-2">
                                <td scope="row" class="bg-light"> <?= $bank->id ?> </td>
                                <td scope="row">
                                    <div class="invoice_card_img ">
                                        <img clss="" src="<?= $BASE_URL ?>assets/home/contas/<?= $bank->logo ?>" alt="">
                                    </div>
                                </td>
                                <td><?= $bank->cod ?></td>
                                <td scope=""> <?= $bank->name ?> </td>
                                <td> <?= date("d-m-Y H:i:s", strtotime($bank->created_at)) ?> </td>
                                <td> <?= $bank->updated_at ?> </td>
                                <td id="latest_moviments">
                                    <a href="#" data-toggle="modal" data-target="#editInvoiceModal<?= $bank->id ?>" title="Editar fatura">
                                        <i class="fa-solid fa-file-pen"></i></a>

                                    <a href="#" data-toggle="modal" data-target="#del_bank_modal<?= $bank->id ?>" title="Deletar">
                                        <i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>

            </div>

        </section>
        <!-- Tabela contas cadastradas -->
        <?php endif ?>
    </div>

</div>

<!-- Card modal delete -->
<?php foreach ($allBanks as $bank) : ?>
    <div class="modal fade" tabindex="-1" id="del_bank_modal<?= $bank->id ?>">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Tem certeza que deseja excluir o registro?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                    <form action="<?= $BASE_URL ?>bank_process.php" method="POST">
                        <input type="hidden" name="type" value="delete">
                        <input type="hidden" name="id" value="<?= $bank->id?>">
                        <input type="hidden" name="current_file" value="<?= $bank->logo ?>">
                        <button type="submit" class="btn btn-primary">Sim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<!-- End card modal delete -->


<?php require_once("templates/footer.php"); ?>