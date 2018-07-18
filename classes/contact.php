<?php
/**
* Classe com os métodos de manipulação do objeto Player (Pontos).
*/

class Contact {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;

    function __construct($db) {
       $this->pdo = $db;
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
        $sql = 'INSERT INTO hlp_contact (ptn_id, cli_id, ctc_name, ctc_job, ctc_landline_phone, ctc_cell_phone, ctc_email, ctc_notes)
                VALUES (?,?,?,?,?,?,?,?)';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([ $person->ptn_id,
                            $person->cli_id,
                            $person->ctc_name,
                            $person->ctc_job,
                            $person->ctc_landline_phone,
                            $person->ctc_cell_phone,                            
                            $person->ctc_email,
                            $person->ctc_notes                            
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
