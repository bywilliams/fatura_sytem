<?php
require_once("templates/header_iframe.php");
require_once("dao/InvoicesDAO.php");
require_once("dao/UserDAO.php");

$uersDao = new UserDAO($conn, $BASE_URL);
$allUsers = $uersDao->findAllUsers();

$user_id = "";
$sql = "";
if ($_GET['user_id'] != "") {
    $user_id = $_GET['user_id'];
    $sql = " AND user_id = $user_id";
}

$invoiceDao = new InvoicesDao($conn, $BASE_URL);
$balaceGeneral = $invoiceDao->getBalanceGeneral($sql);
?>


<div class="container">
    <h1 class="text-center text-secondary my-5">Balanço geral
        <!-- <img src="<?= $BASE_URL ?>assets/home/dashboard-main/full-wallet.png" width="64" height="64" alt=""> -->
        <i class="fa-solid fa-scale-balanced"></i>
    </h1>
    <h3 class="text-center">Filtrar por funcionário:</h3>
    <div class="row offset-md-4 my-3">
   
        <div class="col-lg-4 ">
            <div class="form-group" id="meuFormulario">
                <form action="" method="GET">
                   
                    <select class="form-control" name="user_id" id="user_id" >
                        <option value="">Todos</option>
                        <?php foreach ($allUsers as $user): ?>
                            <option value="<?= trim($user->id) ?>" <?= $user_id == $user->id ? "selected" : ""; ?>> <?= $user->getFullName($user) ?> </option>
                        <?php endforeach ?>
                    </select>
               
            </div>
        </div>
        <div class="col-lg-4">
            <input class="btn btn-md btn-success" type="submit" value="Enviar">
            </form>
        </div>
    </div>

    <div class="col-lg-12">
        <?php if(!empty($sql)): ?>
            <?php foreach($allUsers as $user): ?>
                <?php if($user->id == $user_id): ?>
                    <?php if(!empty($user->image)): ?>
                        <div id="profile-image-container" style="background-image: url('<?= $BASE_URL ?>assets/home/avatar/<?= $user->image ?>')"></div>               
                    <?php else: ?>
                    <div id="profile-image-container" style="background-image: url('<?= $BASE_URL ?>assets/home/avatar/user.png')"></div>               
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>
        <hr class="hr">
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-3 text-center shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="my-0 ">Receita  </h3>
                    <small>Faturas geradas</small>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title text-success" id="revenue_h1">+ R$ <?= number_format($balaceGeneral[0]['total_invoices'], 2, ",", ".") ?> </h1>
                    <!-- <small class="text-muted"><strong>Menor receita</strong> <br>
                         'Não há dados registrados';
                        <br>
                        <strong>Maior receita</strong> <br>
                         'Não há dados registrados';
                    </small> -->
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3 text-center shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="my-0 font-weight-normal">Saldo </h3>
                    <small>Faturas pagas</small>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title text-success" id="revenue_h1">+ R$ <?= number_format($balaceGeneral[0]['total_paid_invoices'], 2, ",", ".") ?> </h1>
                    <!-- <small class="text-muted"><strong>Menor receita</strong> <br>
                         'Não há dados registrados';
                        <br>
                        <strong>Maior receita</strong> <br>
                         'Não há dados registrados';
                    </small> -->
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3 text-center shadow">
                <div class="card-header bg-danger text-white">
                    <h3 class="my-0 font-weight-normal">Despesas </h3>
                    <small>Despesas geradas</small>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title text-danger" id="revenue_h1">- R$ <?= number_format($balaceGeneral[0]['total_expenses'], 2, ",", ".") ?> </h1>
                    <!-- <small class="text-muted"><strong>Menor receita</strong> <br>
                         'Não há dados registrados';
                        <br>
                        <strong>Maior receita</strong> <br>
                         'Não há dados registrados';
                    </small> -->
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3 text-center shadow">
                <div class="card-header text-white <?= $balaceGeneral[0]['balance'] > 0 ? "bg-success" : "bg-danger" ?>">
                    <h3 class="my-0 font-weight-normal">Saldo final</h3>
                    <small>Saldo x Despesas</small>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title <?= $balaceGeneral[0]['balance'] > 0 ? "text-success" : "text-danger" ?>" id="revenue_h1"> R$ <?= number_format($balaceGeneral[0]['balance'], 2, ",", ".") ?> </h1>
                    <!-- <small class="text-muted"><strong>Menor receita</strong> <br>
                         'Não há dados registrados';
                        <br>
                        <strong>Maior receita</strong> <br>
                         'Não há dados registrados';
                    </small> -->
                </div>
            </div>
        </div>
    </div>

</div>


<?php require_once("templates/footer.php"); ?>
