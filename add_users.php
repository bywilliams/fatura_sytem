<?php 
    require_once("globals.php");
    require_once("templates/header_iframe.php");
    require_once("connection/conn.php");
    require_once("dao/UserDAO.php");

    $userDao = new UserDao($conn, $BASE_URL);
    $level_acess = $userDao->getAllLevelAcess();
   
?>

<div class="container">
    <h1 class="text-center my-5">Adicionar usuários</h1>
     <!-- Register Form -->
     <div class="col-md-8 offset-md-2 login-form-2 rounded">
               
                <form action="<?= $BASE_URL ?>auth_process.php" method="POST">
                    <input type="hidden" name="type" value="register">
                    <div class="form-group">
                        <label for="email" class="text-white">E-mail:</label>
                        <input type="email" class="form-control" name="email" placeholder="E-mail *" value="<?php if (isset($_SESSION['email'])) {
                                echo $_SESSION['email'];
                            } ?>" />
                    </div>
                    <div class="form-group">
                        <label class="text-white" for="nome">Nome:</label>
                        <input type="text" class="form-control" name="name" id="" placeholder="Nome *" value="<?php if (isset($_SESSION['name'])) {
                                echo $_SESSION['name'];
                            } ?>">
                    </div>
                    <div class="form-group">
                        <label class="text-white" for="lastname">Sobrenome:</label>
                        <input type="text" class="form-control" name="lastname" id="" placeholder="Sobrenome *" value="<?php if (isset($_SESSION['lastname'])) {
                                echo $_SESSION['lastname'];
                            } ?>">
                    </div>
                    <div class="form-group">
                        <small id="alert_psw" class="text-warning">A senha deve ter 8 caracteres, sendo 1 letra
                            maiúscula, 1 minúscula, 1 número e 1 simbolo.</small>
                        <div class="pwd" style="position: relative">
                            <label class="text-white" for="password">Senha:</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Digite a senha *" value="" />
                            <div class="p-viewer" onclick="show_password()">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pwd" style="position: relative">
                            <label class="text-white" for="confirmPassword">Confirmação da senha:</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                placeholder="Confirme a senha *" value="" />
                            <div class="p-viewer" onclick="show_password()">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nivel_acesso" class="text-white">Nível de acesso:</label>
                        <select class="form-control" name="levels_access_id" id="">
                            <option class="bg-dark text-white" value="">Selecione</option>
                            <?php foreach ($level_acess as $level_acess): ?>
                                <option class="bg-dark text-white" value="<?= $level_acess['id']?>"><?= $level_acess['nome'] ?></option>
                            <?php endforeach ?>

                        </select>
                    </div>
                    <div class="form-group text-white mb-3">
                        <label for="situacao">Situação:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sits_user_id" id="inlineRadio1" value="1">
                            <label class="form-check-label" for="inlineRadio1">Ativo</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sits_user_id" id="inlineRadio2" value="2">
                            <label class="form-check-label" for="inlineRadio2">Inativo</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sits_user_id" id="inlineRadio3" value="3">
                            <label class="form-check-label" for="inlineRadio3">Aguardando</label>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" class="btnSubmit" value="Cadastrar" />
                    </div>
                </form>
            </div>
            <!-- End Register Form -->

</div>
<?php require_once("templates/footer.php"); ?>

<script>

    // Mostra as regras para o password
    $('#password').keyup(function () {

    // Se o valor estiver vazio esconde
    if ($(this).val().length == 0) {
        $('#alert_psw').hide();
    } else {
        $('#alert_psw').show();
    }

    }).keyup(); // Aciona o evento keyup, executando assim o manipulador no carregamento da página

    function show_password() {
    var password = document.getElementById('password');
    var confirmPassword = document.getElementById('confirmPassword');

    (password.type == "password") ? password.type = "text" : password.type = "password";

    (confirmPassword.type == "password") ? confirmPassword.type = "text" : confirmPassword.type = "password";
    }
</script>