<?php
require_once("globals.php");
require_once("templates/header_iframe.php");
require_once("connection/conn.php");
require_once("dao/UserDAO.php");

$userDao = new UserDao($conn, $BASE_URL);
// Pega todos os dados do usuário
$userData = $userDao->verifyToken(true);

$users = $userDao->findAllUsers();


?>

    <div class="container-fluid my-5 px-3">

        <h1 class="text-center text-secondary">Lista de Funcionários</h1>
        <section class="admin-users-list">
            
            <table class="table table-hover table-bordered text-center">
    
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
                    <?php foreach($users as $user): ?>
                    <tr>
                        <th scope="row">
                            <div id="profile-image-container"
                                <?php if ($user->image != "" ): ?>
                                    style="background-image: url('<?= $BASE_URL ?>assets/home/avatar/<?= $user->image ?>')">
                                <?php else: ?>
                                    style="background-image: url('<?= $BASE_URL ?>assets/home/user.png')">
                                <?php endif ?>
                            </div>
                        </th>
                        <td class="align-middle"><?= $user->id ?></td>
                        <td class="align-middle"><?= $user->getFullName($user)?></td>
                        <td class="align-middle"><?= $user->email ?></td>
                        <td class="align-middle">
                            <?php if (!empty($user->register_date)): ?>
                                <?= date("d-m-Y H:i:s", strtotime($user->register_date))?>
                            <?php else: ?>
                                    sem informação
                            <?php endif; ?>
                        </td>
                        <td class="align-middle"><?= $user->sits_user_id ?></td>
                        <td class="align-middle"><?= $user->levels_access_id ?></td>
                        <td class="align-middle"> <a href="" data-toggle="modal" data-target="#popupEditModal" title="Editar menu">
                            <i class="fa-solid fa-file-pen fa-2x text-warning"></i>
                        </a></td>
                    </tr>
                    <?php endforeach; ?>
                   
                </tbody>
            </table>
        </section>

    </div>




<?php require_once("inc/footer.php"); ?>