<?php
require_once("globals.php");
require_once("templates/header.php");
require_once("models/Message.php");
require_once("connection/conn.php");
require_once("dao/UserDAO.php");

$userDao = new UserDAO($conn, $BASE_URL);
$message = new Message($BASE_URL);

$flashMessage = $message->getMessage();

if (!empty($flashMessage)) {
    $message->clearMessage();
}

?>

<main id="login-main">

    <div class="container login-container">
        <?php if (!empty($flashMessage["msg"])): ?>
            <div class="container text-center <?= ($flashMessage["type"]) ?> mb-5 p-2">
                <span id="msg-status">
                    <?= $flashMessage["msg"] ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="row px-3">

            <!-- Login Form -->
            <div class="col-md-6 offset-md-3 login-form-1">
                <div class="text-center mb-3">
                   <img src="<?= $BASE_URL ?>assets/fatura.svg" width="60%" alt="">
                </div>
                <h3>Login</h3>
                <form action="<?= $BASE_URL ?>auth_process.php" method="POST">
                    <input type="hidden" name="type" value="login">
                    <div class="form-group">
                        <label for="email" class="text-white">E-mail:</label>
                        <input type="email" class="form-control" name="email_login" placeholder="Digite seu e-mail *"
                            value="<?php if (isset($_SESSION["email_login"])) {
                                echo $_SESSION["email_login"];
                            } ?>" />
                    </div>
                    <div class="form-group">
                        <label for="password" class="text-white">Senha:</label>
                        <input type="password" class="form-control" name="password" placeholder="Digite sua senha *"
                            value="" />
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" class="btnSubmit" value="Entrar" />
                    </div>
                </form>
            </div>
            <!-- End Login Form -->

            <!-- Modal password recovery -->
            <div class="modal fade" id="recoveryModal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Recuperar senha</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="post" id="demo-form">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="passwrod">Informe o e-mail de login:</label>
                                    <input type="email" class="form-control" id="recovery_email" name="recovery_email"
                                        placeholder="Seu Email *">
                                </div>
                                <p class="text-center">Você receberá um e-mail com instruções para recuperar a senha, se necessário confira a caixa de spam ou lixo eletrônico.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary g-recaptcha" data-sitekey="6LerVrQlAAAAAGTqG_0Ir32eNErvWsVHEjKp5rjs"
                                data-callback="onSubmit"
                                data-action="submit">Recuperar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Modal password recovery -->
        </div>
    </div>
</main>

<?php require_once("templates/footer.php"); ?>
<script src="js/jquery.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>