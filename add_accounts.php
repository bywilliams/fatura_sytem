<?php
    require_once("templates/header_iframe.php");
    
    isset($_SESSION['name_card']) ? $_SESSION['name_card'] : "";
    isset($_SESSION['card_number']) ? $_SESSION['card_number'] : "";
    isset($_SESSION['expired_card']) ? $_SESSION['expired_card'] : "";
    isset($_SESSION['limit_value']) ? $_SESSION['limit_value'] : "";

    $days = [];
    for ($i = 1; $i <= 31; $i++) {
        $days[] = $i;
    }

?>

<div class="container-fluid">
    <h1 class="text-center my-5 text-secondary">Adicionar conta</h1>
    <div class="container actions p-5 mb-4 bg-light rounded-3 shadow-sm">
        <form action="<?= $BASE_URL ?>cards_process.php" method="POST">
            <input type="hidden" name="type" value="create">
            <!-- <input type="hidden" name="csrf_token" value="<?= $token ?>"> -->
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Razão Social:</h4>
                        <input type="text" name="name_card" id="name_card" class="form-control"
                            placeholder="Empresa LTDA" value="<?= $_SESSION['name_card'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">CNPJ:</h4>
                        <input type="tel" name="cc" id="cc" class="form-control" maxlength="19"
                            placeholder="34.567.000/0001-01" value="<?= $_SESSION['card_number'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Agência:</h4>
                        <input type="number" name="expired_card" id="expired_card" class="form-control"
                            value="<?= $_SESSION['expired_card'] ?>" placeholder="0001">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Conta:</h4>
                        <input type="number" name="expired_card" id="expired_card" class="form-control"
                            value="<?= $_SESSION['expired_card'] ?>" placeholder="010101-01">
                    </div>
                </div>
            </div>
            <div class="row offset-sm-1 text-center">
                <div class="col-md-5">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Logo:</h4>
                        <input class="form-control" type="file" name="" id="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Cor do card:</h4>
                        <input class="form-control" type="color" name="" id="">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="submit" class="btn btn-lg btn-success" value="Adicionar"></input>
                </div>
            </div>
        </form>
    </div>

    <!-- Card contaider auto fill -->
    <div class="card_example" id="cards-page">
        <div class="offset-md-4 col-md-4">
            <div class="card-credit bg-secondary" id="card-credit-bg">
                <div class="card_logo">
                    <!-- <i class="" id="flag_icon"></i> -->
                    <div id="flag_icon" alt="">
                </div>
                <div class="card_info">
                    <img src="<?= $BASE_URL ?>assets/home/dashboard-main/chip.png" alt="">
                    <p class="mt-3" id="card_number">4586 7985 9271 6388</p>
                </div>

                <div class="card_crinfo">
                    <p id="card_name">
                        <!-- <small>Titular da conta:</small> <br> -->
                        João Silva Costa
                    </p>

                    <div class="form-group">
                        <small class="text-light">Expira</small>
                        <p id="expired_date">05/23</p>
                    </div>
                </div>
            </div>
            <div class="my-5">
                <a href="#" data-toggle="modal" data-target="#exampleModalCenter" title="Editar">
                    <i class="fa-solid fa-file-pen"></i>
                </a>
                <a href="#" data-toggle="modal" data-target="#modal_del_finance_moviment" title="Deletar">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- Card contaider auto fill -->


</div>
<?php require_once("templates/footer.php"); ?>

<script src="js/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript">
    // Imput nome do cartão aceitará apenas letras
    $("#name_card").on("input", function () {
        var regexp = /[^a-zA-Z]/g;
        if (this.value.match(regexp)) {
            $(this).val(this.value.replace(regexp, ' '));
        }
    });


    // Auto Preenchimento do cartão exemplo e identificação da bandeira do cartão
    $(document).ready(function () {

        $("input").keyup(function () {
            var card_name = $("#name_card").val();
            var account = $("#cc").val();
            var flag_number = account.substr(0, 2);


            $("#card_number").html(account);
            $("#card_name").html(card_name);

            // TODO: <!-- americam 34 e 37 diners 36 e 38 -->
            if(flag_number == 34 || flag_number == 37) {
                $("#card-credit-bg").removeClass("master-card visa diners");
                $("#flag_icon").removeClass("master-card-icon visa-icon diners-icon");
                $("#flag_icon").addClass("amex-icon");
                $("#card-credit-bg").last().addClass("amex");

                
            } else if(flag_number == 36 || flag_number == 38) {

                $("#card-credit-bg").removeClass("master-card visa amex");
                $("#flag_icon").removeClass("master-card-icon visa-icon amex-icon");
                $("#flag_icon").addClass("diners-icon");
                $("#card-credit-bg").last().addClass("diners");

            } else if (flag_number >= 40 && flag_number < 50) {

                $("#card-credit-bg").removeClass("master-card diners amex");
                $("#flag_icon").removeClass("master-card-icon diners-icon amex-icon");
                $("#flag_icon").addClass("visa-icon");
                $("#card-credit-bg").last().addClass("visa");

            } else if (flag_number >= 50 && flag_number <= 55) {

                $("#card-credit-bg").removeClass("visa diners amex");
                $("#flag_icon").removeClass("visa-icon diners-icon amex-icon");
                $("#flag_icon").addClass("master-card-icon");
                $("#card-credit-bg").last().addClass("master-card");
            }

            if (account == "") {
            
                $("#card-credit-bg").removeClass("master-card visa diners amex");
                $("#flag_icon").removeClass("master-card-icon diners-icon visa-icon amex-icon");

                var nome_ex = "Joao Silva Costa";
                var num_ex = "1234 5678 9876 5432";
                if (card_name == "") {
                    $("#card_number").append(num_ex);
                    $("#card_name").append(nome_ex);
                }
            }

            // Auto Preenchimento data de validade
            $("input").click(function () {
                var expired_card = $("#expired_card").val();
                $("#expired_date").html(expired_card);
            });

        });

        // fomata input numero do cartão
        $('#cc').inputmask({
            mask: '(3(4|7)99 9{6} 9{5}|3999 9{4} 9{4} 9{4}|9{4} 9{4} 9{4} 9{4})'
        }).change(function () {
            let value = $(this).val().substr(0, 2);
        });

        /// formata input limite do cartão
        $('.money').mask('000.000.000.000.000,00', {
            reverse: true
        });

    });
</script>
