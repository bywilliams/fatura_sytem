<?php
$time = date("H:i");

// Desloga usuário por inatividade de 15 minutos caso renova automáticamente 
if ($time ==  '18:10') {    
    // Destrói a sessão atual
    session_unset();
    session_destroy();
    echo '<script>window.top.location.href = "index.php";</script>';
    exit;

}

?>
