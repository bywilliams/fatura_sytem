<?php 
require_once("globals.php");
require_once("models/User.php");
require_once("models/Message.php");

Class UserDAO implements UserDAOInterface{

    private $conn;
    private $url;
    private $message;

    public function __construct(PDO $conn, $url){
        $this->conn = $conn;
        $this->url = $url;
        $this->message = new Message($url);
    }

    // constrói o objeto usuário
    public function buildUser($data){

        $user = new User();

        $user->id = $data['id'];
        $user->name = $data['name'];
        $user->lastname = $data['lastname'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->image = $data['image'];
        $user->token = $data['token'];
        $user->bio = $data['bio'];
        $user->sits_user_id = $data['sits_user_id'];
        $user->levels_access_id = $data['levels_access_id'];
        $user->register_date = $data['register_date'];

        return $user;

    } 

    public function create(User $user, $authUser = false){

        $stmt = $this->conn->prepare("INSERT INTO users (
            name, lastname, email, password, token, register_date
        ) VALUES (
            :name, :lastname, :email, :password, :token, NOW()
        )");

        $stmt->bindParam(":name", $user->name);
        $stmt->bindParam(":lastname", $user->lastname);
        $stmt->bindParam(":email", $user->email);
        $stmt->bindParam(":password", $user->password);
        $stmt->bindParam(":token", $user->token);

        $stmt->execute();

        if ($authUser) {
            $this->setTokenSession($user->token);
        }

    }

    public function update(User $user, $redirect = true){

        $stmt = $this->conn->prepare("UPDATE users SET
            name = :name,
            lastname = :lastname,
            email = :email,
            image = :image,
            bio = :bio,
            token = :token
            WHERE id = :id        
        ");

        $stmt->bindParam(":name", $user->name);
        $stmt->bindParam(":lastname", $user->lastname);
        $stmt->bindParam(":email", $user->email);
        $stmt->bindParam(":image", $user->image);
        $stmt->bindParam(":bio", $user->bio);
        $stmt->bindParam(":token", $user->token);
        $stmt->bindParam(":id", $user->id);


        $stmt->execute();
        
        if ($redirect) {
            
            // redireciona para a dashboard
            $this->message->setMessage("Dados atualizados com sucesso!", "success", "edit_profile.php");

        }

    }

    public function verifyToken($protected = false){

       

        if (!empty($_SESSION['token'])) {
            
            // pega o token
            $token = $_SESSION['token'];

            // verifica se o token existe
            $user = $this->findByToken($token);

            if ($user) {
                // retorna o usuário para o front
                return $user;
            }else {
                // Redireciona o usuário não autenticado
                $this->message->setMessage("É necessário estar autenticado para acessar esta página!", "error", "index.php");
            }

        }else if ($protected){
             // Redireciona o usuário não autenticado
             $this->message->setMessage("É necessário estar autenticado para acessar esta página!", "error", "index.php");
        }

    } 

    public function setTokenSession($token, $redirect = true){

        // salva token na Session
        $_SESSION["token"] = $token;

        // Rediciona o novo usuário para a dashboard
        if ($redirect) {
            $this->message->setMessage("Seja bem vindo!", "success", "dashboard.php");
        }

    } 
    
    public function authenticatorUser($email, $password){

        $user = $this->findByEmail($email);

        // Se encontrar - email no BD
        if ($user) {

            // situação 1 = Ativo, situação 2 = Inativo, situação 3 = Aguardando confirmação
            if ($user->sits_user_id != 2 && $user->sits_user_id != 3) {

                if (password_verify($password, $user->password)) {

                    // gera um novo token e insere na session
                    $token = $user->generateToken();
    
                    $this->setTokenSession($token, false);
                    
                    //atualiza o token do usuário no objeto e depois no BD
                    $user->token = $token;

                    try {

                        $this->update($user, false); 
                        return true;

                    } catch (\PDOException $e) { 

                        //echo "Falha ao cadastrar o cartao : {$e->getMessage()}";
                        return false;
                    }
                    
                }else {
                    return false;
                }
            }else {

                //echo "entrou no if user"; exit;
                $this->message->setMessage("Sua assinatura foi suspensa, entre em contato com o administrador do sistema!", "error","index.php"); exit;
            }
            

        }else {
            return false;
        }

    } 


    public function findByEmail($email){

        // Checa se existe valor na variável
        if ($email != "") {
            
            $stmt = $this->conn->prepare("SELECT 
            users.id, name, lastname, email, password, image, token, bio, 
            sits_user_id, levels_access_id, register_date, sits_users.id AS 'situacao' FROM users 
            INNER JOIN sits_users ON users.sits_user_id  = sits_users.id 
            WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            // verifica se a query retornou algo
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $user = $this->buildUser($data);

                return $user;
            }else {
                return false;
            }

        }else {
            return false;
        }

    }

    
    public function findById($id){

         // Checa se existe valor na variável
         if ($id != "") {
            
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            // verifica se a query retornou algo
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $user = $this->buildUser($data);

                return $user;
            }else {
                return false;
            }

        }else {
            return false;
        }

    } 

    public function findByToken($token){

         // Checa se existe valor na variável
         if ($token != "") {
            
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = :token");
            $stmt->bindParam(":token", $token);
            $stmt->execute();

            // verifica se a query retornou algo
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch();
                $user = $this->buildUser($data);

                return $user;
            }else {
                return false;
            }

        }else {
            return false;
        }

    } 
    public function destroyToken(){

        // Remove o tokeen da Session
        $_SESSION["token"] = "";

        // Redireciona e apresenta a mensaem de sucesso
        $this->message->setMessage("Loggout efetuado com sucesso.", "success", "index.php");

    } 
    
    public function changePassword(User $user){

        $stmt = $this->conn->prepare("UPDATE users set
            password = :password
            WHERE id = :id
        ");

        $stmt->bindParam(":password", $user->password);
        $stmt->bindParam(":id", $user->id);

        $stmt->execute();

        //redireciona e apresenta a mensagem de sucesso
        $this->message->setMessage("Senha alterada com sucesso!", "success", "edit_profile.php");

    }
    
    public function recoveryPassword($email, $password){

        $stmt = $this->conn->prepare("UPDATE users set
            password = :password
            WHERE email = :email
        ");

        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":email", $email);

        $stmt->execute();

        if (!$stmt->execute()) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->conn->errorInfo());
        }else {
            //echo "sucesso";
        }

    }  

}