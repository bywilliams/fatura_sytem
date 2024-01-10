<?php
require_once("templates/header_iframe.php");
require_once("utils/check_levels_acess_admin.php");
require_once("dao/BanksDAO.php");

$banksDao = new banksDAO($conn, $BASE_URL);

$allBanks = $banksDao->getAllBanks();

isset($_SESSION['razao']) ? $_SESSION['razao'] : $_SESSION['razao'] = null;
isset($_SESSION['cnpj']) ? $_SESSION['cnpj'] : $_SESSION['cnpj'] = null;
isset($_SESSION['ag']) ? $_SESSION['ag'] : $_SESSION['ag'] = null;
isset($_SESSION['cc']) ? $_SESSION['cc'] : $_SESSION['cc'] = null;
isset($_SESSION['pix']) ? $_SESSION['pix'] : $_SESSION['pix'] = null;
isset ($_SESSION['color']) ? $_SESSION['color'] : $_SESSION['color'] = null;


?>

<div class="container-fluid">

    <h1 class="text-center my-5 text-secondary">Adicionar conta <i class="fa-solid fa-credit-card"></i></h1>
    <div class="container actions p-3 mb-4 bg-light rounded-3 shadow-sm text-center">
        <form action="<?= $BASE_URL ?>account_process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="type" value="create">
            <!-- <input type="hidden" name="csrf_token" value="<?= $token ?>"> -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Razão Social:</h4>
                        <input type="text" name="razao" id="razao" class="form-control" placeholder="Empresa LTDA" value="<?= $_SESSION['razao'] ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">CNPJ:</h4>
                        <input type="tel" name="cnpj" id="cnpj" class="form-control" maxlength="19" placeholder="34.567.000/0001-01" value="<?= $_SESSION['cnpj'] ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Agência:</h4>
                        <input type="number" name="ag" id="ag" class="form-control" value="<?= $_SESSION['ag'] ?>" placeholder="0001" title="Apenas números" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Conta:</h4>
                        <input type="number" name="cc" id="cc" class="form-control" value="<?= $_SESSION['cc'] ?>" placeholder="01010101" title="Apenas números" required>
                    </div>
                </div>
            </div>
            <div class="row offset-sm-1 ">
                <div class="col-md-4">
                    <div class="form-group">
                        <h4 class="font-weight-nrmal">Chave Pix:</h4>
                        <input class="form-control" type="text" name="pix" id="pix" placeholder="123.456.789-10" value="<?= $_SESSION['pix'] ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Banco:</h4>
                        <select class="form-control" name="banco" id="banco">
                            <option value="">Selecione</option>
                            <?php foreach ($allBanks as $bank): ?>
                                <option value="<?= $bank->cod ?>"><?= $bank->cod ?> - <?= $bank->name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Cor:</h4>
                        <input class="form-control" type="color" name="color" id="color" value="<?= $_SESSION['color'] ?>" required>
                    </div>
                </div>
                <div class="col-md-1 text-center">
                    <input type="submit" class="btn btn-lg btn-success" value="Adicionar"></input>
                </div>
            </div>
        </form>
    </div>

    <!-- Card contaider auto fill -->
    <div class="row d-flex justify-content-center card_example pb-3" id="cards-page">
        <div class=" col-lg-4 col-md-6">
            <div class="card-credit px-2" id="card-credit-bg">

                <div class="card_info">
                    <img src="<?= $BASE_URL ?>assets/home/dashboard-main/chip.png" alt="">
                    <p class="mt-1" id="card_number">CPNJ: 34.567.000/0001-01</p>
                </div>
                <div class="card_pix">
                    <p class="text-white ml-2" id="chave_pix">Pix: 123.456.789-10</p>
                </div>

                <div class="card_crinfo">
                    <p id="card_name">
                        <small>Razão social:</small> <br>
                        Empresa LTDA
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
        </div>
    </div>
    <!-- Card contaider auto fill -->


</div>


<?php require_once("templates/footer.php"); ?>
<script src="js/jquery.inputmask.bundle.min.js"></script>

<script type="text/javascript">
    // Formata input CNPJ
    $('#cnpj').mask('00.000.000/0000-00', {
        reverse: false
    });

    // Imput razão social aceitará apenas letras
    // $("#razao").on("input", function() {
    //     var regexp = /[^a-zA-Z]/g;
    //     if (this.value.match(regexp)) {
    //         $(this).val(this.value.replace(regexp, ' '));
    //     }
    // });

    // Referência para o input do tipo "number"
    var inputNumber1 = $("#cc");
    var inputNumber2 = $("#ag");

    // Função para verificar se o valor contém apenas números
    function validateNumberInput(input) {
        var inputValue = input.val();
        var regex = /^\d+$/;
        if (!regex.test(inputValue)) {
            input.val("");
        }
    }

    // Evento "input" para ambos os inputs
    inputNumber1.on("input", function() {
        validateNumberInput(inputNumber1);
    });

    inputNumber2.on("input", function() {
        validateNumberInput(inputNumber2);
    });



    // Auto Preenchimento do cartão exemplo
    $(document).ready(function() {
        var color = $("#color").val();
        $("input").keyup(function() {

            var razao = $("#razao").val();
            var cnpj = $("#cnpj").val();
            var ag = $("#ag").val();
            var cc = $("#cc").val();
            var pix = $("#pix").val();

            $("#card_number").html('CNPJ: ' + cnpj); // autofill do cnpj digitado no cartão exemplo
            $("#card_name").html('<small>Razão social</small><br>' + razao); // autofill da razão social no cartão exemplo
            $("#agencia").html(ag);
            $("#conta").html(cc);
            $("#chave_pix").html('Pix: ' + pix);


            if (cnpj == "" && razao == "" && pix == "" && ag == "" && cc == "") {

                var razao_ex = "Empresa LTDA";
                var cnpj_ex = "34.567.000/0001-01";
                var chave_pix = "123.345.678-10";
                var agencia = "0001";
                var conta = "010101-01";
                $("#card_number").append(cnpj_ex);
                $("#card_name").append(razao_ex);
                $("#chave_pix").append(chave_pix);
                $("#conta").append(conta);
                $("#agencia").append(agencia);

            }

        });

        // Auto Preenchimento cor do cartão escolhida pelo usuário
        $("#color").change(function() {
            var color = $(this).val();
            $("#card-credit-bg").css("background-color", color);
        });

    });
</script>