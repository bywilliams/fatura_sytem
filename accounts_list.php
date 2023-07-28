<?php
require_once("templates/header_iframe.php");
require_once("globals.php");
require_once("connection/conn.php");
// require_once("dao/CardsDAO.php");

// $cardDao = new CardsDAO($conn, $BASE_URL);
// // traz todos os cartões do usuário
// $cards = $cardDao->getAllCards($userData->id);

?>

<div class="container-fluid">
    <h1 class="text-center text-secondary my-3">Contas cadastradas</h1>

    <hr class="hr">
    <section>

        <!-- Each Card  -->
        <div class="row card_example" id="cards-page">

            <div class="col-md-4  my-3">
                <div class="card-credit shadow" id="card-credit-bg">

                    <div id="flag_icon" class="" alt=""></div>

                    <div class="card_info">
                        <div class="bg-white rounded" style="display: inline-block">
                            <img src="<?= $BASE_URL ?>assets/home/logo-santander.png" alt="">
                        </div>
                        <p class="mt-3" id="card_number"> CNPJ: 34.567.432/0001-01</p>
                    </div>

                    <div class="card_crinfo">
                        <p id="card_name">
                            <small> Razão social: </small> <br>
                            Banco Santander
                        </p>

                        <div class="form-group d-flex text-center">
                            <div class="px-3">
                                <small class="text-light">Agencia</small>
                                <p id="agencia">0001</p>
                            </div>
                            <div>
                                <small class="text-light">Conta</small>
                                <p id="conta">010101-01</p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="text-center my-2">
                    <!-- <p>Limíte de crédito R$<?= $card->limit_value ?> </p>
                        <p> <strong> Fecha dia: </strong> <?= $card->close_day ?> <br>
                        <strong> Vence dia: </strong> <?= $card->due_day ?></p> -->
                    <a href="#" data-toggle="modal" data-target="#exampleModalCenter" title="Editar">
                        <i class="fa-solid fa-file-pen"></i>
                    </a>
                    <a href="" data-toggle="modal" data-target="#modal_del_cards<?= $card->id ?>"><i class="fa-solid fa-trash-can"></i></a>
                </div>
            </div>


            <!-- <?php if (count($cards) === 0) : ?>
                <h4 class="col text-center">Nenhuma Cartão cadastrado</h4>
            <?php endif; ?>  -->


        </div>
    </section>
    <!-- Each Card  -->
</div>

<!-- Card modal delete -->
<?php foreach ($cards as $card) : ?>
    <div class="modal" tabindex="-1" id="modal_del_cards<?= $card->id ?>">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Tem certeza que deseja excluir o registro?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                    <form action="<?= $BASE_URL ?>cards_process.php" method="POST">
                        <input type="hidden" name="type" value="delete">
                        <input type="hidden" name="id" value="<?= $card->id ?>">
                        <button type="submit" class="btn btn-primary">Sim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<!-- End card modal delete -->

<?php require_once("templates/footer.php"); ?>