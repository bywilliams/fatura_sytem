<?php
require_once("globals.php");
require_once("connection/conn.php");
require_once("models/User.php");
require_once("dao/UserDAO.php");
require_once("dao/MenuDAO.php");
require_once("dao/PopupDAO.php");
require_once("dao/InvoicesDAO.php");

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


// Popups
$invoiceDao = new InvoicesDao($conn, $BASE_URL);
$invoicesExpiringUser = $invoiceDao->checkInvoicesUserExpiringToday($userData->id);

$popupDao = new PopupDAO($conn, $BASE_URL);
$popup = $popupDao->popupInvoice($userData->id);
//print_r($popup); exit;

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
            <br>
            <?= $msg_saudacao; ?>
        </div>

        <!-- Menu items  sidebar -->
        <?php require_once("utils/menu_items.php"); ?>
    </nav>

    <!-- Page Content  -->
    <div id="content">

        <nav class="navbar navbar-expand-lg nav-toggle" style="background-color: #999;">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-outline-dark" onclick="mudaIconeToogle()">
                    <i class="fa-solid fa-arrows-left-right fa-1x"></i>
                </button>
                <h5 class=" text-white ml-auto pt-2">Mês:
                    <?= $nome_mes_atual ?>
                </h5>
            </div>
        </nav>

        <div class="row">
            <div class="container-fluid">
                <iframe src="dashboard-main.php" name="myFrame" id="myFrame" fullscreen="allow" frameborder="0" width="100%"></iframe>
            </div>
        </div>
    </div>
    <!-- End Page Content  -->

    <!-- Section Popup Invoice Epiring message  -->
    <section>
        <?php if (!empty($popup) && !empty($invoicesExpiringUser)) : ?>
            <?php
            $showPopup = true;
            $popupCookieName = "popup_displayed_" . date("Ymd");
            if (isset($_COOKIE[$popupCookieName])) {
                $showPopup = false;
            }

            if ($showPopup) : ?>

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
                                <!-- Add your form elements here -->
                            </div>
                            <button class="btn btn-lg btn-info" id="popup_submit">OK</button>
                        </form>
                    </div>

                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        document.querySelector(".close_popup").addEventListener("click", function() {
                            document.getElementById("container-popup").style.display = "none";
                            setPopupCookie();
                        });

                        document.getElementById("popup_submit").addEventListener("click", function() {
                            document.getElementById("container-popup").style.display = "none";
                            setPopupCookie();
                        });

                        function setPopupCookie() {
                            var date = new Date();
                            date.setTime(date.getTime() + (24 * 60 * 60 * 1000)); // Set cookie expiration to 1 day
                            var expires = "expires=" + date.toUTCString();
                            document.cookie = "<?= $popupCookieName ?>=true;" + expires + ";path=/";
                        }
                    });
                </script>

            <?php endif; ?>

        <?php endif; ?>
    </section>
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

    // Fim Popup
</script>