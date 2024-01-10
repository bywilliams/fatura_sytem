<?php
require_once("templates/header_iframe.php");
require_once("utils/check_levels_acess_admin.php");
?>

<div class="container my-5">
    <h1 class="text-center mb-5 text-secondary"> Administrativo <i class="fa-solid fa-rectangle-list"></i></h1>
    <!-- <h4>Área dedicada a</h4> -->

    <div class="row">
        <div class="col-lg-4 mb-2">
            <div class="card shadow">
                <div class="card-header text-center bg-success">
                    <h4 class="text-white">Funcionários</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>add_users.php"><i class="fa-solid fa-user-tie"></i> Cadastar Funcionários</a></li>
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>users.php"><i class="fa-solid fa-user-group"></i> Ver funcionários</a></li>
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>logins_log.txt" download><i class="fa-solid fa-download"></i> Baixar Log de logins</a></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 mb-2">
            <div class="card shadow">
                <div class="card-header text-center bg-info">
                    <h4 class="text-white"> Bancos e Contas </h3>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>add_banks.php"><i class="fa-solid fa-building-columns"></i> Cadastar e listar bancos</a></li>
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>add_accounts.php"><i class="fa-solid fa-money-check"></i> Cadastrar contas</a></li>
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>accounts_list.php"><i class="fa-solid fa-money-check-dollar"></i> Ver contas cadastradas</a></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header text-center bg-danger">
                    <h4 class="text-white">Processos Operacionais</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>invoices.php"><i class="fa-solid fa-file-invoice"></i> Ver Faturas</a></li>
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>expenses_users.php"><i class="fa-solid fa-minus"></i>Ver despesas</a></li>
                    <li class="list-group-item"><a href="<?= $BASE_URL ?>balance.php"><i class="fa-solid fa-scale-balanced"></i> Ver balanço</a></li>
                </ul>
            </div>
        </div>
        
    </div>
</div>



<?php require_once("templates/footer.php"); ?>