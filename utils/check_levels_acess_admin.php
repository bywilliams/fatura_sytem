<?php 
require_once("templates/header_iframe.php");

if ($userData->levels_access_id != 1) {
    $message->setMessage("<script>
    Swal.fire({
        title: 'Aviso!',
        text: ' Você não possui acesso a está página!',
        confirmButtonText: 'OK',
        confirmButtonColor: '#0B666A', 
        cancelButtonText: 'Fechar',
    })
    ;</script>", "", "dashboard.php");
}

?>