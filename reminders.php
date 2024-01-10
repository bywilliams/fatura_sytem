<?php
    require_once("globals.php");
    require_once("templates/header_iframe.php");
    require_once("connection/conn.php");
    require_once("dao/RemindersDAO.php");

    $reminderDao = new RemindersDAO($conn, $BASE_URL);

    $allReminders = $reminderDao->getAllReminders($userData->id);

    // Mantem ou limpa inputs preenchidos
    isset($_SESSION['title']) ? $_SESSION['title'] : $_SESSION['title'] = null ;
    isset($_SESSION['description']) ? $_SESSION['description'] : $_SESSION['description'] = null ;
    isset($_SESSION['reminder_date']) ? $_SESSION['reminder_date'] : $_SESSION['reminder_date'] = null;

    require_once("utils/check_reminders.php");
?>


<div class="container">
    <h1 class="text-center my-5">Meus Lembretes <i class="fa-solid fa-bell text-warning"></i></h1>

    <!-- Reminder register form -->
    <section>
        <h4 class="font-weight-normal">Cadastrar lembrete</h4>
        <form action="<?= $BASE_URL ?>reminders_process.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="type" value="register">
            <div class="reminder bg-light rounded-3 shadow-sm my-3">
                <!-- <div class="image flex-item">
                    <label for="file">
                        <i class="fa-solid fa-camera fa-3x"></i> 
                    </label>
                    <input type="file" id="file" name="file">
                </div> -->
                <div class="form-group titulo flex-item">
                    <h4 class="font-weight-normal">Titulo:</h4>
                    <input class="form-control "type="text" name="title" id="" placeholder="titulo do lembrete.." value="<?= $_SESSION['title'] ?>"> 
                </div>
                <div class="form-group descricao flex-item">
                    <h4 class="font-weight-normal">Descrição:</h4>
                    <input class="form-control "type="text" name="description" id="" placeholder="ex: descrição ..." value="<?= $_SESSION['description'] ?>">
                </div>
                <div class="form-group flex-item">
                    <h4 class="font-weight-normal">Data: </h4>
                    <input class="form-control "type="date" name="reminder_date" id="" value="<?= $_SESSION['reminder_date'] ?>">
                </div>
                <div class="button flex-item">
                    <label for="submit">
                        <i class="fa-regular fa-square-plus fa-3x" title="Adicionar"></i>
                    </label>
                    <input type="submit" id="submit" value="">
                </div>
            </div>
        </form>
    </section>
    <!-- Reminder register form -->

    <h4 class="font-weight-normal mt-5">Últimos lembretes cadastrados</h4>
    
    <!-- All Reminders container -->
    <div  iv class=" bg-light rounded-3 shadow-sm my-3">
        <div class="row px-3 py-2">
            <?php if (count($allReminders) > 0): ?>
                <?php foreach ($allReminders as $reminder): ?>
                    <div class="col-md-3 reminder-card pb-3">
                        <div class="card card-reminder mb-3 border-0" >
                            <div class="card-header border border-white"><small><?= $reminder->title ?> <br> <?= $reminder->reminder_date ?></small></div>
                            <div class="card-body">
                                <p class="card-text"><?= $reminder->description ?>.</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="" title="Editar" data-toggle="modal" data-target="#reminder_modal_edit<?= $reminder->id ?>"> <i class="fa-solid fa-file-pen"></i> </a>
                            <a href="" data-toggle="modal" data-target="#modal_del_reminder<?= $reminder->id ?>" title="Deletar"> <i class="fa-solid fa-trash-can"></i> </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <h5 class="py-2 text-center text-info">Ainda não há lembretes cadastrados.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- End All Reminders container -->

</div>

    <!--  Reminder modal Edit -->
    <?php foreach ($allReminders as $reminder_edit) : ?>
            <div class="modal fade" id="reminder_modal_edit<?= $reminder_edit->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Lembrete</h5>
                            <button type="button" class="close" data-dismiss="modal" arial-label="fechar">
                                <span arial-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php $reminder_date = date("Y-m-d", strtotime($reminder_edit->reminder_date))?>
                            <form action="<?= $BASE_URL ?>reminders_process.php?id=<?= $reminder_edit->id ?>" method="post">
                                <input type="hidden" name="id" value="<?= $reminder_edit->id ?>">
                                <input type="hidden" name="type" value="edit">
                                <div class="form-group">
                                    <label for="description">Titulo:</label>
                                    <input type="text" name="title" id="" class="form-control" placeholder="Insira uma nova descrição" value="<?= $reminder_edit->title ?>">
                                </div>
                                <div class="form-group">
                                    <label for="value">Descrição:</label>
                                    <input type="text" name="description" id="" class="form-control" placeholder="Insira um novo valor" value="<?= $reminder_edit->description ?>">
                                </div>
                               
                                <div class="form-group">
                                    <label for="obs">Data:</label>
                                    <input class="form-control" type="date" name="reminder_date" id="" value="<?= $reminder_date ?>">
                                </div>
                                <input type="submit" value="Enviar" class="btn btn-lg btn-success" onclick="scrollToTop()">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
    <?php endforeach; ?>
    <!-- End Reminder modal Edit -->


    <!-- Reeminder modal delete -->
    <?php foreach ($allReminders as $reminder_del) : ?>
            <div class="modal fade" tabindex="-1" id="modal_del_reminder<?= $reminder_del->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <p>Tem certeza que deseja excluir o registro?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                            <form action="<?= $BASE_URL ?>reminders_process.php" method="POST">
                                <input type="hidden" name="type" value="delete">
                                <input type="hidden" name="id" value="<?= $reminder_del->id ?>">
                                <button type="submit" class="btn btn-primary">Sim</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    <?php endforeach; ?>
    <!-- End Reminder modal delete -->

<?php require_once("templates/footer.php"); ?>
