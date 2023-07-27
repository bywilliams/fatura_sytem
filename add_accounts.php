<?php
    require_once("templates/header_iframe.php");
    
    isset($_SESSION['razao']) ? $_SESSION['razao'] : "";
    isset($_SESSION['cnpj']) ? $_SESSION['cnpj'] : "";
    isset($_SESSION['ag']) ? $_SESSION['ag'] : "";
    isset($_SESSION['cc']) ? $_SESSION['cc'] : "";


?>

<div class="container-fluid">
    <h1 class="text-center my-5 text-secondary">Adicionar conta <i class="fa-solid fa-building-columns"></i></h1>
    <div class="container actions p-5 mb-4 bg-light rounded-3 shadow-sm">
        <form action="<?= $BASE_URL ?>cards_process.php" method="POST">
            <input type="hidden" name="type" value="create">
            <!-- <input type="hidden" name="csrf_token" value="<?= $token ?>"> -->
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Razão Social:</h4>
                        <input type="text" name="razao" id="razao" class="form-control"
                            placeholder="Empresa LTDA" value="<?= $_SESSION['razao'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">CNPJ:</h4>
                        <input type="tel" name="cnpj" id="cnpj" class="form-control" maxlength="19"
                            placeholder="34.567.000/0001-01" value="<?= $_SESSION['cnpj'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Agência:</h4>
                        <input type="number" name="ag" id="ag" class="form-control"
                            value="<?= $_SESSION['ag'] ?>" placeholder="0001">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Conta:</h4>
                        <input type="number" name="cc" id="cc" class="form-control"
                            value="<?= $_SESSION['cc'] ?>" placeholder="010101-01">
                    </div>
                </div>
            </div>
            <div class="row offset-sm-1 px-5 text-center">
                <div class="col-md-5">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Logo:</h4>
                        <input class="form-control" type="file" name="logo" id="logo">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <h4 class="font-weight-normal">Cor do card:</h4>
                        <input class="form-control" type="color" name="color" id="color">
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
            <div class="card-credit" id="card-credit-bg">
               
                <div class="card_info">
                    <img src="<?= $BASE_URL ?>assets/home/dashboard-main/chip.png" alt="">
                    <p class="mt-3" id="card_number">CPNJ: 34.567.000/0001-01</p>
                </div>

                <div class="card_crinfo">
                    <p id="card_name">
                        <small>Razão social:</small> <br>
                        Empresa LTDA
                    </p>

                    <div class="form-group d-flex text-center">
                        <div class="px-3">
                            <small class="text-light">Agencia</small>
                            <p id="expired_date">0001</p>
                        </div>
                        <div>
                            <small class="text-light">Conta</small>
                            <p id="expired_date">010101-01</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card contaider auto fill -->


</div>
<?php require_once("templates/footer.php"); ?>


<script type="text/javascript">
    // Imput nome do cartão aceitará apenas letras
    $("#razao").on("input", function () {
        var regexp = /[^a-zA-Z]/g;
        if (this.value.match(regexp)) {
            $(this).val(this.value.replace(regexp, ' '));
        }
    });


    // Auto Preenchimento do cartão exemplo e identificação da bandeira do cartão
    $(document).ready(function () {
        var color = $("#color").val();
        $("input").keyup(function () {
            var razao = $("#razao").val();
            var cnpj = $("#cnpj").val();
            
            $("#card_number").html('CNPJ: ' + cnpj);
            $("#card_name").html('<small>Razão social</small><br>' + razao);

            if (cnpj == "") {

                var nome_ex = "Empresa LTDA";
                var num_ex = "34.567.000/0001-01";
                if (razao == "") {
                    $("#card_number").append(num_ex);
                    $("#card_name").append(nome_ex);
                }
            }

        });

        // Auto Preenchimento data de validade
        $("#color").change(function () {
                
                var color = $(this).val();
                // $("#expired_date").html(expired_card);
                $("#card-credit-bg").css("background-color", color);
        });
        

    });
</script>
