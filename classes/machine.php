<?php
/**
* Classe com os métodos de manipulação do objeto Machine (Equipamentos).
*/

class Machine {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;

    function __construct($db) {
       $this->pdo = $db;
    }
    
    /**
    * Função para registrar um novo equipamento
    * @param ArrayObject $machine Dados do Equipamento.
    * @return boolean of success.
    */
    public function addMachine($machine) {
        $sql = 'INSERT INTO hlp_mchn
                (typ_id,
                 pnt_id,
                 mch_active,
                 mch_sku,
                 mch_cm_name,
                 mch_tv_name,
                 mch_config,
                 usr_id,
                 mch_creation_date)
                VALUES
                (?,
                 ?,
                 ?,
                 ?,
                 ?,
                 ?,
                 ?,
                 ?,
                 NOW())';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([ $machine->type,
                            $machine->player,
                            $machine->active,
                            $machine->sku,                            
                            $machine->contentManager,
                            $machine->teamViewer,
                            $machine->config,
                            $_SESSION['user']['id']                            
                          ])) { 
            return true;
        } else {            
            return false; 
        }        
    }

    /**
    * Função para alterar dados do equipamento
    * @param ArrayObject $machine Dados do Equipamento.
    * @return boolean of success.
    */
    public function machineUpdate($machine){
        $sql = 'UPDATE hlp_mchn
                SET
                    typ_id = ?,
                    pnt_id = ?,
                    mch_active = ?,
                    mch_sku = ?,
                    mch_cm_name = ?,
                    mch_tv_name = ?,
                    mch_config = ?
                WHERE mch_id = ?';
        $pdo = $this->pdo;
        if(isset($machine)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([
                $machine->type,
                $machine->player,
                $machine->active,
                $machine->sku,                
                $machine->contentManager,
                $machine->teamViewer,
                $machine->config,
                $machine->id
            ])){
                return true;
            }else{
                $this->msg = 'Erro na alteração de dados do equipamento.';
                return false;
            }
        }else{
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

    /**
    * Extrair os dados de um equipamento baseado em seu id
    *
    * @param int $mch_id id do equipameno.
    * @return array retorna a prioridadade.
    */
    public function getPrioridade($mch_id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT k.pri_name
                    FROM hlp_mchn m,
                         hlp_ponto p,
                         hlp_client c,
                         hlp_tkt_priority k
                    where m.pnt_id = p.pnt_id    
                    and p.cli_id = c.cli_id
                    and c.pri_id = k.pri_id
                    and p.pnt_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$mch_id]);
            $result = $stmt->fetch(); 
            return $result;
        }
    }

    /**
    * Extrair os dados de um equipamento baseado em seu id
    *
    * @param int $id id do equipameno.
    * @return array retorna um array com os dados do equipamento.
    */
    public function getMachine($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT mch_id,
                           typ_id,
                           pnt_id,
                           mch_active,
                           mch_sku,
                           mch_cm_name,
                           mch_tv_name,
                           mch_config
                    FROM hlp_mchn 
                    WHERE mch_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(); 
            return $result;
        }
    }

    /**
    * Listar os equipamentos
    *
    * @return array Retorna a lista de equipmanentos.
    */
    public function listMachines() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT m.mch_id,
                            m.typ_id,
                            m.pnt_id,
                            m.mch_active,
                            m.mch_sku,
                            m.mch_cm_name,
                            m.mch_tv_name,
                            m.mch_config,
                            p.pnt_name,
                            c.cli_name,
                            t.typ_name    
                       FROM hlp_mchn m, 
                            hlp_ponto p, 
                            hlp_user u,
                            hlp_client c,
                            hlp_mchn_typeof t
                      WHERE m.typ_id = t.typ_id 
                        AND m.pnt_id = p.pnt_id
                        AND p.cli_id = c.cli_id                     
                        AND m.mch_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os equipamentos com base em pesquisas
    *
    * @return array Retorna a lista de equipmanentos.
    */
    public function searchMachines($filter) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT m.mch_id,
                            m.mch_sku,
                            DATE(m.mch_creation_date) as dia,
                            m.mch_active,
                            t.typ_name,
                            c.cli_name, 
                            p.pnt_name,
                            p.pnt_state,
                            u.usr_name,
                            m.mch_creation_date
                       FROM hlp_mchn m,
                            hlp_mchn_typeof t,
                            hlp_ponto p,
                            hlp_client c,
                            hlp_user u
                      WHERE m.typ_id = t.typ_id
                        AND m.pnt_id = p.pnt_id
                        AND p.cli_id = c.cli_id     
                        AND u.usr_id = m.usr_id ';
            if (($filter->dataInicio != '--') && ($filter->dataFim != '--')) { 
                $sql .= 'AND  (m.mch_creation_date BETWEEN "'.  $filter->dataInicio . '" AND "'. $filter->dataFim. '") '; 
            }

            if (($filter->dataInicio != '--') && ($filter->dataFim == '--')) {
                $sql .= 'AND m.mch_creation_date >= "' . $filter->dataInicio . '" ';
            }

            if (($filter->dataInicio == '--') && ($filter->dataFim != '--')) {
                $sql .= 'AND m.mch_creation_date <= "' . $filter->dataFim . '" ';
            }

            if (!empty($filter->cliente)) {
                $sql .= ' AND c.cli_id = ' . $filter->cliente ;
            }

            if (!empty($filter->player)) {
                $sql .= ' AND m.pnt_id = ' . $filter->player;
            }

            if (!empty($filter->type)) {
                $sql .= ' AND m.typ_id = ' . $filter->type;
            }

            if (!empty($filter->active)) {
                $sql .= ' AND m.mch_active = ' . $filter->active;
            }

            if (!empty($filter->state)) {
                $sql .= ' AND p.pnt_state = "' . $filter->state . '" ';
            }

            if (!empty($filter->sku)) {
                $sql .= ' AND UPPER(m.mch_sku) LIKE "%' . strtoupper($filter->sku) . '%" ';
            }

            $sql .= ' ORDER BY c.cli_name, p.pnt_name';

            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os tipos de equipamentos
    *
    * @return array Retorna a lista de tipos.
    */
    public function listTypes() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT typ_id,
                            typ_name
                       FROM hlp_mchn_typeof 
                       ORDER BY typ_name';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os equipamentos
    *
    * @return array Retorna a lista de equipmanentos por player.
    */
    public function selectMachinesByPlayer($pnt_id) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT m.mch_id,
                            m.pnt_id,
                            m.mch_sku,
                            m.mch_cm_name,
                            m.mch_tv_name,
                            m.mch_config  
                       FROM hlp_mchn m
                      WHERE m.pnt_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$pnt_id]);
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    public function export() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'select 
                          m.mch_active,
                          m.mch_sku,
                          m.mch_cm_name,
                          m.mch_config,
                          m.mch_tv_name,
                          p.pnt_name,
                          c.cli_name,
                          t.typ_name
                    from hlp_mchn m,
                         hlp_mchn_typeof t,
                         hlp_ponto p,
                         hlp_client c
                    where t.typ_id = m.typ_id
                    and m.pnt_id = p.pnt_id
                    and c.cli_id = p.pnt_id';            
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
