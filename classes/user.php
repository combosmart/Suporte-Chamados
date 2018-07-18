<?php
/**
* Secure login/registration user class.
*/

class User {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;

    function __construct($db) {
       $this->pdo = $db;
    }
    
    /**
    * Login function
    * @param string $email User email.
    * @param string $password User password.
    *
    * @return bool Returns login success.
    */
    public function login($email,$password) {
        try {
                $sql = 'SELECT u.usr_id,u.usr_active,u.grp_id,u.usr_password,u.usr_name,u.usr_email,u.usr_photo,g.grp_name
                          FROM hlp_user u, hlp_group g 
                         WHERE g.grp_id = u.grp_id
                           AND u.usr_email = ? AND u.usr_active = 1';
                $pdo = $this->pdo;
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if(password_verify($password,$user['usr_password'])) {
                    $this->user = $user;
                    session_regenerate_id();
                    $_SESSION['user']['id']       = $user['usr_id'];
                    $_SESSION['user']['name']     = $user['usr_name'];
                    $_SESSION['user']['grp_id']   = $user['grp_id'];
                    $_SESSION['user']['email']    = $user['usr_email'];
                    $_SESSION['user']['group']    = $user['grp_name'];
                    $_SESSION['user']['photo']    = $user['usr_photo'];
                    $_SESSION['user']['isLogged'] = true;
                    return true;                    
                } else {
                    return false;
                }

        } catch(PDOException $e) {
            echo '<p class="bg-danger">'.$e->getMessage().'</p>';
        }
        
    }

    /**
    * Função para registrar um novo usuário
    * @param string $name Nome do usuário.
    * @param string $group grupo de acesso do usuário.
    * @param string $email Email do usuário.        
    * @param string $pass senha.
    * @param booleal $active senha.
    * @return boolean of success.
    */
    public function registration($name,$group,$email,$pass,$active) {
        $sql = 'INSERT INTO hlp_user (usr_name,grp_id,usr_email,usr_password,usr_active) VALUES (?,?,?,?,?)';
        $pdo = $this->pdo;
        if($this->checkEmail($email)) {
            $this->msg = 'Email já existe cadastrado no sistema.';
            return false;
        }        

        $pass = $this->hashPass($pass);
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$name,$group,$email,$pass,$active])) { 
            return true;
        } else {            
            return false; 
        }        
    }

    /**
    * Email the confirmation code function
    * @param string $email User email.
    * @return boolean of success.
    */
    private function sendConfirmationEmail($email){
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT confirm_code FROM users WHERE email = ? limit 1');
        $stmt->execute([$email]);
        $code = $stmt->fetch();

        $subject = 'Confirm your registration';
        $message = 'Please confirm you registration by pasting this code in the confirmation box: '.$code['confirm_code'];
        $headers = 'X-Mailer: PHP/' . phpversion();

        if(mail($email, $subject, $message, $headers)){
            return true;
        }else{
            return false;
        }
    }

    /**
    * Activate a login by a confirmation code and login function
    * @param string $email User email.
    * @param string $confCode Confirmation code.
    * @return boolean of success.
    */
    public function emailActivation($email,$confCode){
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE users SET confirmed = 1 WHERE email = ? and confirm_code = ?');
        $stmt->execute([$email,$confCode]);
        if($stmt->rowCount()>0){
            $stmt = $pdo->prepare('SELECT id, fname, lname, email, wrong_logins, user_role FROM users WHERE email = ? and confirmed = 1 limit 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            $this->user = $user;
            session_regenerate_id();
            if(!empty($user['email'])){
            	$_SESSION['user']['id'] = $user['id'];
	            $_SESSION['user']['fname'] = $user['fname'];
	            $_SESSION['user']['lname'] = $user['lname'];
	            $_SESSION['user']['email'] = $user['email'];
	            $_SESSION['user']['user_role'] = $user['user_role'];
	            return true;
            }else{
            	$this->msg = 'Account activitation failed.';
            	return false;
            }            
        }else{
            $this->msg = 'Account activitation failed.';
            return false;
        }
    }

    /**
    * Password change function
    * @param int $id User id.
    * @param string $pass New password.
    * @return boolean of success.
    */
    public function passwordChange($id,$pass){
        $pdo = $this->pdo;
        if(isset($id) && isset($pass)){
            $stmt = $pdo->prepare('UPDATE hlp_user SET usr_password = ? WHERE usr_id = ?');
            if($stmt->execute([$this->hashPass($pass),$id])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
    * Assign a role function
    * @param int $id User id.
    * @param int $role User role.
    * @return boolean of success.
    */
    public function assignRole($id,$role){
        $pdo = $this->pdo;
        if(isset($id) && isset($role)){
            $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
            if($stmt->execute([$id,$role])){
                return true;
            }else{
                $this->msg = 'Role assign failed.';
                return false;
            }
        }else{
            $this->msg = 'Provide a role for this user.';
            return false;
        }
    }



    /**
    * User information change function
    * @param int $id User id.
    * @param int $active Status do usuário no sistema.
    * @param string $name Nome do usuário.
    * @param string $group Grupo de acesso
    * @return boolean of success.
    */
    public function userUpdate($active,$name,$group,$id){
        $sql = 'UPDATE hlp_user SET usr_active = ?, usr_name = ?, grp_id = ? WHERE usr_id = ?';
        $pdo = $this->pdo;
        if(isset($id) && isset($name) && isset($group)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([$active,$name,$group,$id])){
                return true;
            }else{
                $this->msg = 'Erro na alteração de dados de usuário.';
                return false;
            }
        }else{
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

    /**
    * Função que verifica a existência de um email
    * @param string $email Email do usuário.
    * @return boolean of success.
    */
    public function checkEmail($email){
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT usr_id FROM hlp_user WHERE usr_email = ? limit 1');
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }


    /**
    * Register a wrong login attemp function
    * @param string $email User email.
    * @return void.
    */
    private function registerWrongLoginAttemp($email){
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE users SET wrong_logins = wrong_logins + 1 WHERE email = ?');
        $stmt->execute([$email]);
    }

    /**
    * Password hash function
    * @param string $password User password.
    * @return string $password Hashed password.
    */
    private function hashPass($pass){
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
    * Print error msg function
    * @return void.
    */
    public function printMsg(){
        print $this->msg;
    }

    /**
    * Logout the user and remove it from the session.
    *
    * @return true
    */
    public function logout() {
        session_unset();
        session_regenerate_id();
        return true;
    }

    /**
    * Extrair os dados de um usuário baseado em seu id
    *
    * @return array returna um array com os dados do usuário.
    */
    public function getUser($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT u.usr_id,u.usr_active,u.grp_id,u.usr_password,u.usr_name,u.usr_email,u.usr_photo
                      FROM hlp_user u
                     WHERE u.usr_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(); 
            return $result;
        }
    }

    /**
    * Listar os grupos de acesso
    *
    * @return array Returns list of groups.
    */
    public function listGroups() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT grp_id, grp_name FROM hlp_group';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * List users function
    *
    * @return array Returns list of users.
    */
    public function listUsers($group = null){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT u.usr_id,u.usr_active,u.grp_id,u.usr_password,u.usr_name,u.usr_email,u.usr_photo,g.grp_name
                      FROM hlp_user u, hlp_group g 
                     WHERE g.grp_id = u.grp_id ';
            if (isset($group)) { $sql .= 'AND u.grp_id = ' . $group; }
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    public function is_logged_in(){
        if(isset($_SESSION['user']['isLogged']) && $_SESSION['user']['isLogged'] == true){
            return true;
        }
    }

    /**
    * Simple template rendering function
    * @param string $path path of the template file.
    * @return void.
    */
    public function render($path,$vars = '') {
        ob_start();
        include($path);
        return ob_get_clean();
    }

    /**
    * Template for index head function
    * @return void.
    */
    public function indexHead() {
        print $this->render(indexHead);
    }

    /**
    * Template for index top function
    * @return void.
    */
    public function indexTop() {
        print $this->render(indexTop);
    }

    /**
    * Template for login form function
    * @return void.
    */
    public function loginForm() {
        print $this->render(loginForm);
    }

    /**
    * Template for activation form function
    * @return void.
    */
    public function activationForm() {
        print $this->render(activationForm);
    }

    /**
    * Template for index middle function
    * @return void.
    */
    public function indexMiddle() {
        print $this->render(indexMiddle);
    }

    /**
    * Template for register form function
    * @return void.
    */
    public function registerForm() {
        print $this->render(registerForm);
    }

    /**
    * Template for index footer function
    * @return void.
    */
    public function indexFooter() {
        print $this->render(indexFooter);
    }

    /**
    * Template for user page function
    * @return void.
    */
    public function userPage() {
	$users = [];
	if($_SESSION['user']['user_role'] == 2){
		$users = $this->listUsers();
	}
        print $this->render(userPage,$users);
    }
}
