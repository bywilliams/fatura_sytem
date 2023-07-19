<?php

# Classe responsável pelo sistema de mensagens do sistema

class Message {

    private $url;
    public function __construct($url) {
        $this->url = $url;
    }

    // Pega uma mensagem do sistema
    public function getMessage(){
        if(!empty($_SESSION['msg'])){
            return [
                "msg" => $_SESSION['msg'],
                "type" => $_SESSION['type']
            ];
        }else {
            return false;
        }
    }

    // Insere uma mensagem no sistema
    // Parametros: seta msg, tipo da msg ex: sucesso|error e se redireciona para alguma url ou volta pra index
    public function setMessage($msg, $type, $redirect = "index.php"){
        $_SESSION['msg'] = $msg;
        $_SESSION['type'] = $type;

        if($redirect != "back"){
            // Volta para index
            header("Location: $this->url" . $redirect);
        }else {
            // Volta para a última página de referência que ele acessou
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
    }

    public function clearMessage(){
        $_SESSION['msg'] = "";
        $_SESSION['type'] = "";
    }

}
