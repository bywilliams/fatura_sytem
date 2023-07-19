
<?php
require_once("globals.php");
require_once("templates/header.php");
require_once("connection/conn.php");
require_once("models/Message.php");
require_once("models/User.php");
require_once("dao/UserDAO.php");
require_once("dao/MenuDAO.php");
require_once("utils/check_inactivity.php");

$message = new Message($BASE_URL);
$user = new User();

// Pega todos os dados o usuário pelo Token
$userDao = new UserDao($conn, $BASE_URL);
$userData = $userDao->verifyToken(true);

$flashMessage = $message->getMessage();

if (!empty($flashMessage)) {
    $message->clearMessage();
}
?>

<?php if (!empty($flashMessage["msg"])) : ?>
    <div class="container sticky-top text-center <?= ($flashMessage["type"]) ?> my-3 p-2">
        <span id="msg-status"><?= $flashMessage["msg"] ?></span>
    </div>
<?php endif; ?>