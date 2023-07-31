<?php 
    
    trait Generates {

       
        // Função que gera nomes para imagens do projeto
        public function imageGenerateName (){
            return bin2hex(random_bytes(60)) . ".jpg";
        }

    }


?>