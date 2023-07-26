<?php
require_once("templates/header_iframe.php");
require_once("dao/UserDAO.php");

$userDao = new UserDao($conn, $BASE_URL);

$users = $userDao->findAllUsers();
$levels_acess = $userDao->getAllLevelAcess();


?>

<div class="container-fluid my-5">

    <h1 class="text-center text-secondary">Lista de Funcionários</h1>

    <!-- Tabela lista de usuários -->
    <section>
        <div class="row px-5 admin-users-list">

            <table class="table table-hover table-bordered text-center shadow">

                <thead class="table-dark">
                    <tr>
                        <th scope="col">Foto</th>
                        <th scope="col">Id</th>
                        <th scope="col">Nome</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Data de Registro</th>
                        <th scope="col">Situação</th>
                        <th scope="col">Nível acesso</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <th scope="row">

                                <?php if ($user->image != "") : ?>
                                    <a href="<?= $BASE_URL ?>assets/home/avatar/<?= $user->image ?>">
                                        <div id="profile-image-container" style="background-image: url('<?= $BASE_URL ?>assets/home/avatar/<?= $user->image ?>')"> </div>
                                    </a>
                                <?php else : ?>
                                    <div id="profile-image-container" style="background-image: url('<?= $BASE_URL ?>assets/home/user.png')"> </div>
                                <?php endif ?>
                            </th>
                            <td class="align-middle"><?= $user->id ?></td>
                            <td class="align-middle"><?= $user->getFullName($user) ?></td>
                            <td class="align-middle"><?= $user->email ?></td>
                            <td class="align-middle">
                                <?php if (!empty($user->register_date)) : ?>
                                    <?= date("d-m-Y H:i:s", strtotime($user->register_date)) ?>
                                <?php else : ?>
                                    sem informação
                                <?php endif; ?>
                            </td>
                            <td class="align-middle"><?= $user->sits_user_id == 1 ? "Ativo" : ($user->sits_user_id == 2 ? "Inativo" : "Aguardando"); ?></td>
                            <td class="align-middle"><?= $user->levels_access_id == 1 ? "Super Administrador" : ($user->levels_access_id == 2 ? "Gerente" : "Usuário"); ?></td>
                            <td class="align-middle" id="latest_moviments">
                                <a href="#" data-toggle="modal" data-target="#editUser<?= $user->id ?>" title="Editar">
                                    <i class="fa-solid fa-file-pen"></i></a>
                                <a href="#" data-toggle="modal" data-target=".editUser<?= $user->id ?>" title="Deletar">
                                    <i class="fa-solid fa-trash-can"></i></a>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>

    </section>
    <!-- Tabela lista de usuários -->

</div>

<?php foreach ($users as $user) :  ?>

    <!-- Modal eidtar usuário-->
    <div class="modal fade" id="editUser<?= $user->id ?>" tabindex="-1" role="dialog" aria-labelledby="editUser<?= $user->id ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Editar <?= $user->getFullName($user) ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="text-danger" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= $BASE_URL ?>user_process.php" method="post">
                        <input type="hidden" name="type" value="edit_user_admin">
                        <input type="hidden" name="user_id" value="<?=$user->id?>">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="sits_user_id" id="sits_user_id" type="checkbox" value="1" <?= $user->sits_user_id == 1 ? "checked" : "" ?>>
                                <label class="form-check-label" for="inlineCheckbox1">Ativo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="sits_user_id" id="sits_user_id" type="checkbox" value="2" <?= $user->sits_user_id == 2 ? "checked" : "" ?>>
                                <label class="form-check-label" for="inlineCheckbox2">Inativo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="sits_user_id" id="sits_user_id" type="checkbox" value="3" <?= $user->sits_user_id == 3 ? "checked" : "" ?>>
                                <label class="form-check-label" for="inlineCheckbox3">Aguardando</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="levels_acess_id">Nível de acesso:</label>
                            <select class="form-control" name="levels_access_id" id="levels_acess_id">
                                <option value="">Selecione</option>
                                <?php foreach ($levels_acess as $level_acess) : ?>
                                    <?php if ($user->levels_access_id == $level_acess['id']) : ?>
                                        <option value="<?= $level_acess['id'] ?>" selected>
                                            <?= $level_acess['nome'] ?>
                                        </option>
                                    <?php else : ?>
                                        <option value="<?= $level_acess['id'] ?>">
                                            <?= $level_acess['nome'] ?>
                                        </option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="need_password">Precisa de uma nova senha?</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="new_password" id="new_password<?=$user->id?>" value="S" onclick="showPasswordDiv(<?=$user->id?>)">
                                <label class="form-check-label" for="inlineCheckbox1">Sim</label>
                            </div>
                            <!-- <div class="form-check form-check-inline">
                                <input class="form-check-input" id="need_password" name="new_password" type="checkbox" value="N" checked>
                                <label class="form-check-label" for="inlineCheckbox2">Não</label>
                            </div> -->
                        </div>
                        <div class="password_div<?=$user->id?>" style="display: none">
                            <div class="form-group ">
                                <label for="new_password">Nova senha</label>
                                <input class="form-control" type="password" name="password" id="password" placeholder="digite uma nova senha" value="<?=$user->password?>">
                            </div>
                            <div class="form-group">
                                <label for="confirm_new_password">Confirme a nova senha</label>
                                <input class="form-control" type="password" name="confirmPassword" id="confirmPassword" placeholder="confirme a nova senha" value="<?=$user->password?>">
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <input type="submit" class="btn btn-primary" value="Salvar"></input>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Fim Modal eidtar usuário-->
<?php endforeach; ?>

<?php require_once("templates/footer.php"); ?>


<script>
     
    // Função para mostrar ou ocultar div com inputs de senhas
    function showPasswordDiv (i) {

        // Define um evento de clique para o input com id "ne_password"
        $("#new_password"+ i).click(function() {
            
            // Obtém o valor atual do atributo "checked" do checkbox
            var isChecked = $(this).prop("checked");

            if (isChecked) {
                
                $(".password_div" + i).show();
            }else {
               
                $(".password_div" + i).hide();
            }
        });
    }
   


    // the selector will match all input controls of type :checkbox
    // and attach a click event handler 
    $("input:checkbox").on('click', function() {
    // in the handler, 'this' refers to the box clicked on
    var $box = $(this);
    if ($box.is(":checked")) {
        // the name of the box is retrieved using the .attr() method
        // as it is assumed and expected to be immutable
        var group = "input:checkbox[name='" + $box.attr("name") + "']";
        // the checked state of the group/box on the other hand will change
        // and the current value is retrieved using .prop() method
        $(group).prop("checked", false);
        $box.prop("checked", true);
    } else {
        $box.prop("checked", false);
    }
    });
</script>