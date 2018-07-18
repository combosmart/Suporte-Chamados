<?php
/**
* Classe com os métodos de manipulação do objeto Atendimento.
*/

class Atendimento {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;

    function __construct($db) {
       $this->pdo = $db;
    }
    
    /**
    * Função para registrar um novo atendimento
    * @param obj $atendimento Objeto atendimento.
    * @return boolean of success.
    */
    public function addAtdmt($atendimento) {
        $sql = 'INSERT INTO hlp_client
                    (mchn_id,
                    atdmt_data,
                    orig_id,
                    atdmt_file,
                    atdmt_obs,
                    usr_id)
                    VALUES
                    (?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?)';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([
            $atendimento->mchn_id,
            $atendimento->atdmt_data,
            $atendimento->orig_id,
            $atendimento->atdmt_file,
            $atendimento->atdmt_obs,
            $atendimento->usr_id
        ])) { 
            return true;
        } else {            
            return false; 
        }        
    }

    /**
    * Função para alterar dados do atendimento
    * @param obj $atendimento Objeto atendimento.
    * @return boolean of success.
    */
    public function atdmtUpdate($atendimento){
        $sql = 'UPDATE  hlp_atdmt SET
                        mchn_id = ? 
                        atdmt_data = ? 
                        orig_id = ? 
                        atdmt_file = ? 
                        atdmt_obs = ? 
                WHERE atdmt_id = ?';
        $pdo = $this->pdo;
        if(isset($atendimento)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([
                $atendimento->mchn_id,
                $atendimento->atdmt_data,
                $atendimento->orig_id,
                $atendimento->atdmt_file,
                $atendimento->atdmt_obs,
                $atendimento->atdmt_id
            ])){
                return true;
            }else{
                $this->msg = 'Erro na alteração de dados do atendimento.';
                return false;
            }
        }else{
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

    /**
    * Extrair os dados de um atendimento baseado em seu id
    * @param int $id Id do atendimento.
    * @return array returna um array com os dados do atendimento.
    */
    public function getAtdmt($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT  hlp_atdmt,
                            mchn_id,
                            atdmt_data,
                            orig_id,
                            atdmt_file,
                            atdmt_obs
                    FROM hlp_atdmt      
                    WHERE atdmt_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(); 
            return $result;
        }
    }

    /**
    * Listar os atendimentos
    *
    * @return array Returns list of atendimentos.
    */
    public function listAtdmt($filter = null) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT DATE(a.atdmt_data) as atdmt_data,
                           a.mch_id,
                           a.orig_id
                           a.atdmt_file,
                           a.atdmt_obs,
                           m.mch_cm_name,
                           o.orig_name,
                           u.usr_name
                      FROM hlp_atdmt a,
                           hlp_mchn m,
                           hlp_atdmt_orig o,
                           hlp_client c,
                           hlp_ponto p,
                           hlp_user u
                     WHERE a.orig_id = o.orig_id
                       AND a.mch_id = m.mch_id
                       AND m.pnt_id = p.pnt_id
                       AND p.pnt_id = c.cli_id 
                       AND a.usr_id = u.usr_id ';
            if (($filter->dataInicio != '--') && ($filter->dataFim != '--')) { 
                $sql .= 'AND  (a.atdmt_data BETWEEN "'.  $filter->dataInicio . '" AND "'. $filter->dataFim. '") '; 
            }

            if (($filter->dataInicio != '--') && ($filter->dataFim == '--')) {
                $sql .= 'AND a.atdmt_data >= "' . $filter->dataInicio . '" ';
            }

            if (($filter->dataInicio == '--') && ($filter->dataFim != '--')) {
                $sql .= 'AND a.atdmt_data <= "' . $filter->dataFim . '" ';
            }

            if (!empty($filter->cli_id)) {
                $sql .= ' AND c.cli_id = ' . $filter->cli_id;
            }

            if (!empty($filter->orig_id)) {
                $sql .= ' AND o.orig_id = ' . $filter->orig_id;
            }

            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar as origens dos atendimentos
    *
    * @return array Returns list of origens de atendimentos.
    */
    public function listAtdmtOrig() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT orig_id,
                            orig_name
                       FROM hlp_atdmt_orig';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os clientes
    *
    * @return array Returns list of clients.
    */
    public function export() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'select c.cli_name,
                           c.cli_active
                    from hlp_client c order by c.cli_name';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }
    

}
