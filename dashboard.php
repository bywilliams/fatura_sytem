<?php
require_once("globals.php");
require_once("connection/conn.php");
require_once("models/User.php");
require_once("models/FinancialMoviment.php");
require_once("dao/UserDAO.php");
require_once("dao/MenuDAO.php");
require_once("dao/FinancialMovimentDAO.php");
require_once("dao/PopupDAO.php");

$financialMoviment = new FinancialMoviment();
$financialMovimentDao = new FinancialMovimentDAO($conn, $BASE_URL);

$user = new User();
$userDao = new UserDao($conn, $BASE_URL);

// pega o menu dinamico para o sidebar
$menu_Dao = new MenuDAO($conn);
$menus = $menu_Dao->findMenu();

// Pega todos os dados do usuário
$userData = $userDao->verifyToken(true);

// Nome completo para sidebar
$fullName = $user->getFullName($userData);

// Imagem default caso o usuário não tem cadastrado
if ($userData->image == "") {
    $userData->image = "user.png";
}

// Traz total de entradas do usuário
$totalCashInflow = $financialMovimentDao->getAllCashInflow($userData->id);

// Traz total de saídas do usuário
$totalCashOutflow = $financialMovimentDao->getAllCashOutflow($userData->id);

// Pega o resultado da função que faz o calculo da % que as despesas representam sobre a receita
$resultExpensePercent = (float) $financialMoviment->balancePercent($totalCashInflow, $totalCashOutflow);

// Popups
$popupDao = new PopupDAO($conn, $BASE_URL);
$popup = $popupDao->popup($userData->id);

?>

<?php require_once("templates/header.php"); ?>
<!-- Navbar top -->
<nav class="navbar sticky-top navbar-dark shadow">
    <div class="container-fluid">
        <a class="navbar-brand font-weight-bolder" href="<?= $BASE_URL ?>dashboard.php">
        <i class="fa-solid fa-file-invoice text-white"></i>
            <span>INTRANET</span>
        </a>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link text-white" href="<?= $BASE_URL ?>logout.php"> <i class="fa-solid fa-right-from-bracket"></i> Sair</a>
            </li>
        </ul>
    </div>
</nav>
<!-- End Navbar top -->

<div class="wrapper">
    <!-- Sidebar  -->
    <nav id="sidebar">
        <div class="sidebar-header text-center">
            <div id="profile-image-container" style="background-image: url('<?= $BASE_URL ?>assets/home/avatar/<?= $userData->image ?>')">
            </div>
            <!-- user name in sidebar -->
            <h5 class="user_name">
                <?= $fullName ?>
            </h5>
            <!-- User Greet -->
            <span id="DisplayClock" onload="showTime()"></span>
            <?= $msg_saudacao; ?>
        </div>

        <!-- Menu items  sidebar -->
        <?php require_once("utils/menu_items.php"); ?>
    </nav>

    <!-- Page Content  -->
    <div id="content">

        <nav class="navbar navbar-expand-lg navbar-light nav-toggle" style="background-color: #ccc;">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-outline-dark" onclick="mudaIconeToogle()">
                    <i class="fa-solid fa-arrows-left-right fa-1x"></i>
                </button>
                <h5 class=" text-dark ml-auto pt-2">Mês:
                    <?= $nome_mes_atual ?>
                </h5>
            </div>
        </nav>

        <div class="row">
            <div class="container-fluid">
                <iframe src="dashboard-main.php" name="myFrame" id="myFrame" fullscreen="allow" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
            </div>
        </div>
    </div>
    <!-- End Page Content  -->

    <!-- Welcome Popup message  -->
    <?php if (!empty($popup)) : ?>

        <div class="container-popup" id="container-popup">

            <div class="popup text-center" id="popup-card">
                <button class="popup-close close_popup">x</button>
                <h2><?= $popup->title ?></h2>
                <p><?= $popup->description ?></p>
                <?php if ($popup->image != "") : ?>
                    <div>
                        <img class="animated-gif" src="<?= $BASE_URL ?>assets/home/popup/<?= $popup->image ?>" alt="imagm popup">
                    </div>
                <?php endif; ?>
                <form action="<?= $BASE_URL ?>popup_process.php" method="post">
                    <div class="form-group">
                        <label for="no_show_again"><small> Marque a caixa abaixo e clique em OK <br> para não mostrar esta
                                mensagem novamente.</small></label>
                        <input class="form-control" type="checkbox" name="no_show_popup" id="no_show_popup" value="<?= $popup->id ?>">
                    </div>
                    <input type="submit" class="btn btn-lg btn-info" id="popup_submit" value="OK"></input>
                </form>
            </div>

        </div>

    <?php endif; ?>
    <!-- Popup messages  -->

</div>

<?php require_once("templates/footer.php"); ?>

<script>
    // deixar item do menu clicado como active
    $(document).ready(function() {
        $('ul li a').click(function() {
            $('li a').removeClass("active_item");
            $(this).addClass("active_item");
        });
    });


    // Abrir e fechar Popup
    const popupWindow = document.querySelector("#popup-card");
    const popupClose = document.querySelectorAll(".popup-close");
    const containerClose = document.getElementById("container-popup");

    window.addEventListener("load", () => {
        popupWindow.classList.add("active_item");
    });

    popupClose.forEach((close) =>
        close.addEventListener("click", () => {
            popupWindow.classList.remove("active_item");
            containerClose.style.display = "none";
        })
    );

    // submit desligado enquanto checkbox de confirmação estiver vazio
    $(document).ready(function() {
        $('#popup_submit').prop('disabled', true);
        $('#no_show_popup').click(function() {
            if ($(this).is(':checked')) {
                $('#popup_submit').prop('disabled', false);
            } else {
                $('#popup_submit').prop('disabled', true);
            }
        });
    });
    // Fim Popup

    </script>

</script>