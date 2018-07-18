<?php
/**
* Classe com os métodos de manipulação do objeto Player (Pontos).
*/

class Player {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;

    function __construct($db) {
       $this->pdo = $db;
    }
    
    /**
    * Função para registrar um novo ponto
    * @param ArrayObject $player Dados do Ponto.
    * @return boolean of success.
    */
    public function addPlayer($player) {
        $sql = 'INSERT INTO hlp_ponto (cli_id, pnt_name, pnt_notes, pnt_address, pnt_number, pnt_neighbor, pnt_city, pnt_state, pnt_zip, pnt_active, usr_id, pnt_creation_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([ $player->client,
                            $player->name,
                            $player->notes,
                            $player->address,
                            $player->number,
                            $player->neighbor,
                            $player->city,
                            $player->state,
                            $player->zip,
                            $player->active,                            
                            $_SESSION['user']['id']                      
                          ])) { 
            return $pdo->lastInsertId();
        } else {            
            return -1; 
        }        
    }

    /**
    * Função para alterar dados do ponto
    * @param ArrayObject $player Dados do Ponto.
    * @return boolean of success.
    */
    public function playerUpdate($player){
        $sql = 'UPDATE hlp_ponto 
                   SET cli_id  = ?, 
                       pnt_name = ?, 
                       pnt_notes = ?, 
                       pnt_address = ?, 
                       pnt_number = ?, 
                       pnt_neighbor = ?, 
                       pnt_city = ?, 
                       pnt_state = ?, 
                       pnt_zip = ?, 
                       pnt_active = ?
                 WHERE pnt_id = ?';
        $pdo = $this->pdo;
        if(isset($player)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([
                $player->client,
                $player->name,
                $player->notes,
                $player->address,
                $player->number,
                $player->neighbor,
                $player->city,
                $player->state,
                $player->zip,
                $player->active,
                $player->id
            ])){
                return true;
            }else{
                $this->msg = 'Erro na alteração de dados do cliente.';
                return false;
            }
        }else{
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

    /**
    * Extrair os dados de um ponto baseado em seu id
    *
    * @param int $player id do Ponto.
    * @return array retorna um array com os dados do cliente.
    */
    public function getPlayer($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT p.cli_id, 
                           p.pnt_name, 
                           p.pnt_notes, 
                           p.pnt_address, 
                           p.pnt_number, 
                           p.pnt_neighbor, 
                           p.pnt_city, 
                           p.pnt_state, 
                           p.pnt_zip, 
                           p.pnt_active, 
                           p.usr_id, 
                           p.pnt_creation_date,
                           c.cli_name
                      FROM hlp_ponto p, hlp_client c
                     WHERE p.cli_id = c.cli_id 
                       AND p.pnt_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(); 
            return $result;
        }
    }

    /**
    * Listar os pontos
    *
    * @return array Retorna a lista de players.
    */
    public function listPlayers($client = null) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT p.pnt_id, 
                            p.cli_id, 
                            p.pnt_name, 
                            p.pnt_notes, 
                            p.pnt_address, 
                            p.pnt_number, 
                            p.pnt_neighbor, 
                            p.pnt_city, 
                            p.pnt_state, 
                            p.pnt_zip, 
                            p.pnt_active, 
                            p.usr_id, 
                            p.pnt_creation_date,
                            c.cli_name,
                            u.usr_name
                      FROM hlp_ponto p, hlp_client c, hlp_user u
                     WHERE p.cli_id = c.cli_id
                       AND p.usr_id = u.usr_id ';
            if (isset($client)) { $sql .= 'AND c.cli_id = ' . $client; }
            $sql .= ' ORDER BY c.cli_name, pnt_name';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os pontos
    *
    * @return array Retorna a lista de players.
    */
    public function searchPlayers($player) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT p.pnt_id, 
                            p.cli_id, 
                            p.pnt_name, 
                            p.pnt_notes, 
                            p.pnt_address, 
                            p.pnt_number, 
                            p.pnt_neighbor, 
                            p.pnt_city, 
                            p.pnt_state, 
                            p.pnt_zip, 
                            p.pnt_active, 
                            p.usr_id, 
                            p.pnt_creation_date,
                            c.cli_name,
                            u.usr_name
                      FROM hlp_ponto p, hlp_client c, hlp_user u
                     WHERE p.cli_id = c.cli_id
                       AND p.usr_id = u.usr_id ';
            if (!empty($player->client)) { $sql .= ' AND p.cli_id = ' . $player->client; }
            if (!empty($player->name))   { $sql .= ' AND upper(p.pnt_name) LIKE "%' . strtoupper($player->name) . '%"'; }
            if (!empty($player->city))   { $sql .= ' AND upper(p.pnt_city) LIKE "%' . strtoupper($player->city) . '%"'; }
            if (!empty($player->state))  { $sql .= ' AND p.pnt_state = "' . strtoupper($player->state) . '"'; }
            if (is_numeric($player->active)) { $sql .= ' AND p.pnt_active = ' . $player->active; }
            $sql .= ' ORDER BY c.cli_name';
            //print_r($player);
            //echo "<br/><br/>" . $sql; exit;
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os contatos
    *
    * @return array Retorna a lista de contatos relacionados a players.
    */
    public function listContacts($id) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT ctc_id, 
                            ptn_id, 
                            ctc_name, 
                            ctc_landline_phone, 
                            ctc_cell_phone, 
                            ctc_email
                       FROM hlp_ponto_contact WHERE ptn_id = ?';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Função para registrar um novo contato relacionado ao ponto
    * @param ArrayObject $person Dados do Ponto.
    * @return boolean of success.
    */
    public function addContact($person) {
        $sql = 'INSERT INTO hlp_ponto_contact (ptn_id, ctc_name, ctc_landline_phone, ctc_cell_phone, ctc_email)
                VALUES (?,?,?,?,?)';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([ $person->player,
                            $person->name,
                            $person->landPhone,
                            $person->cellPhone,                            
                            $person->email                            
                          ])) 
        { 
            return true;
        } else {            
            return false; 
        }        
    }

    /**
    * Função para alterar dados do contato
    * @param ArrayObject $person Dados do contato.
    * @return boolean of success.
    */
    public function contactUpdate($person){
        $sql = 'UPDATE hlp_ponto_contact
                SET
                ptn_id = ?,
                ctc_name = ?,
                ctc_landline_phone = ?,
                ctc_cell_phone = ?,
                ctc_email = ?
                WHERE ctc_id = ?';
        $pdo = $this->pdo;
        if(isset($person->id)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([
                $person->player,
                $person->name,
                $person->landPhone,
                $person->cellPhone,                
                $person->email,   
                $person->id
            ])){
                return true;
            }else{
                $this->msg = 'Erro na alteração de dados do cliente.';
                return false;
            }
        }else{
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }    

    /**
    * Extrair os dados de um contato baseado em seu id
    *
    * @param int $player id do contato.
    * @return array retorna um array com os dados do contato.
    */
    public function getContact($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT ptn_id, 
                           ctc_name, 
                           ctc_landline_phone, 
                           ctc_cell_phone, 
                           ctc_email
                      FROM hlp_ponto_contact
                    WHERE ctc_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(); 
            return $result;
        }
    }

    /**
    * Função para remover um contato relacionado ao ponto
    * @param int $id id do contato.
    * @return boolean of success.
    */
    public function deleteContact($id) {
        $sql = 'DELETE FROM hlp_ponto_contact WHERE ctc_id=?';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$id])) { 
            return true;
        } else {            
            return false; 
        }        
    }

    public function export() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'select   
                        c.cli_name,
                        p.pnt_name,
                        p.pnt_notes,
                        p.pnt_address,
                        p.pnt_number,
                        p.pnt_neighbor,
                        p.pnt_city,
                        p.pnt_state,
                        p.pnt_zip,
                        p.pnt_active
                    from hlp_ponto p,
                         hlp_client c
                    where p.cli_id = c.cli_id
                    order by p.pnt_name';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar o log de alteração do ponto
    * @param int $id Id do ponto.
    * @return array Returns list of log.
    */
    public function getLog($id) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT l.pnt_log_date, l.pnt_log_field, l.pnt_log_old, l.pnt_log_new, u.usr_name
                       FROM hlp_ponto_log l, hlp_ponto p, hlp_user u 
                    WHERE l.pnt_id = p.pnt_id AND l.usr_id = u.usr_id 
                        AND p.pnt_id = ?';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

}
