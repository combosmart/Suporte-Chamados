<?php
/**
* Classe com os métodos de manipulação do objeto Cliente.
*/

class Client {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;

    function __construct($db) {
       $this->pdo = $db;
    }
    
    /**
    * Função para registrar um novo cliente
    * @param obj $client Objeto cliente.
    * @return boolean of success.
    */
    public function addClient($cliente) {
        $sql = 'INSERT INTO hlp_client
                    (cli_name,
                    usr_id,
                    cli_creation_date,
                    pri_id,
                    cli_address,
                    cli_number,
                    cli_neighbor,
                    cli_city,
                    cli_state,
                    cli_zip,
                    cli_cnpj,
                    cli_nmfnt,
                    cli_flg_elemidia)
                    VALUES
                    (?,
                    ?,
                    NOW(),
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?)';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([
            $cliente->cli_name,
            $cliente->usr_id,
            $cliente->pri_id,
            $cliente->cli_address,
            $cliente->cli_number,
            $cliente->cli_neighbor,
            $cliente->cli_city,
            $cliente->cli_state,
            $cliente->cli_zip,
            $cliente->cli_cnpj,
            $cliente->cli_nmfnt,
            $cliente->cli_flg_elemidia
        ])) { 
            return $pdo->lastInsertId();
        } else {            
            return -1; 
        }        
    }

    /**
    * Função para alterar dados do cliente
    * @param obj $client Objeto cliente.
    * @return boolean of success.
    */
    public function clientUpdate($cliente){
        $sql = 'UPDATE hlp_client
                    SET
                    cli_name = ?,
                    pri_id = ?,
                    cli_address = ?,
                    cli_number = ?,
                    cli_neighbor = ?,
                    cli_city = ?,
                    cli_state = ?,
                    cli_zip = ?,
                    cli_cnpj = ?,
                    cli_nmfnt = ?,
                    cli_flg_elemidia = ?,
                    cli_active = ?
                    WHERE cli_id = ?';
        $pdo = $this->pdo;
        if(isset($cliente)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([
                $cliente->cli_name,
                $cliente->pri_id,
                $cliente->cli_address,
                $cliente->cli_number,
                $cliente->cli_neighbor,
                $cliente->cli_city,
                $cliente->cli_state,
                $cliente->cli_zip,
                $cliente->cli_cnpj,
                $cliente->cli_nmfnt,
                $cliente->cli_flg_elemidia,
                $cliente->cli_active,
                $cliente->cli_id
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
    * Função que verifica a existência do nome
    * @param string $name Nome do cliente.
    * @return boolean of success.
    */
    public function checkName($name){
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT cli_name FROM hlp_client WHERE UPPER(cli_name) = UPPER(?) limit 1');
        $stmt->execute([$name]);
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
    * Função que verifica se já existe um CNPJ cadastrado
    * @param obj $cliente Objeto cliente.
    * @param string $mode Modo (se é insert ou update).
    * @return boolean of success.
    */
    public function checkCNPJ($cliente,$mode=null){
        $sql = 'SELECT  cli_id,    
                        cli_cnpj
                FROM hlp_client 
               WHERE cli_cnpj = ?';
        if (!empty($mode)) {
            $sql .= 'AND cli_id NOT IN (?)';            
            $arrExecute = array($cliente->cli_cnpj, $cliente->cli_id);
        } else {
            $arrExecute = array($cliente->cli_cnpj);
        }
        
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);        
        $stmt->execute($arrExecute);
        
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }


    /**
    * Extrair os dados de um cliente baseado em seu id
    * @param int $id Id do cliente.
    * @return array returna um array com os dados do cliente.
    */
    public function getClient($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT  cli_id,
                            cli_active,
                            cli_name,
                            usr_id,
                            cli_creation_date,
                            pri_id,
                            cli_address,
                            cli_number,
                            cli_neighbor,
                            cli_city,
                            cli_state,
                            cli_zip,
                            cli_cnpj,
                            cli_nmfnt,
                            cli_flg_elemidia
                       FROM hlp_client
                      WHERE cli_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(); 
            return $result;
        }
    }

    /**
    * Listar os clientes
    *
    * @return array Returns list of clients.
    */
    public function listClients() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT c.cli_id,
                            c.cli_active,
                            IFNULL(c.cli_nmfnt,c.cli_name) AS cli_name, 
                            u.usr_name,
                            c.cli_creation_date 
                       FROM hlp_client c, 
                            hlp_user u 
                      WHERE u.usr_id = c.usr_id 
                      ORDER BY cli_name';
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

    /**
    * Listar as prioridades
    *
    * @return array Returns list of priorities.
    */
    public function listPriorities() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT pri_id,
                            pri_name
                       FROM hlp_tkt_priority 
                   ORDER BY pri_id';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar o log de alteração do cliente
    * @param int $id Id do cliente.
    * @return array Returns list of log.
    */
    public function getLog($id) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT l.cli_log_date, l.cli_log_field, l.cli_log_old, l.cli_log_new, u.usr_name
                       FROM hlp_client_log l, hlp_client c, hlp_user u 
                    WHERE l.cli_id = c.cli_id AND l.usr_id = u.usr_id 
                        AND c.cli_id = ?';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os equipamentos com base em pesquisas
    *
    * @return array Retorna a lista de equipmanentos.
    */
    public function searchClients($filter) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT  c.cli_id,
                             c.cli_active,
                             c.cli_name,
                             c.usr_id,
                             DATE(c.cli_creation_date) as dia,
                             c.pri_id,
                             c.cli_address,
                             c.cli_number,
                             c.cli_neighbor,
                             c.cli_city,
                             c.cli_state,
                             c.cli_zip,
                             c.cli_cnpj,
                             c.cli_nmfnt,
                             c.cli_flg_elemidia,
                             u.usr_name,
                             p.pri_name
                        FROM hlp_client c, 
                             hlp_user u, 
                             hlp_tkt_priority p
                        WHERE c.usr_id = u.usr_id 
                          AND p.pri_id = c.pri_id ';
            if (($filter->dataInicio != '--') && ($filter->dataFim != '--')) { 
                $sql .= 'AND  (c.cli_creation_date BETWEEN "'.  $filter->dataInicio . '" AND "'. $filter->dataFim. '") '; 
            }

            if (($filter->dataInicio != '--') && ($filter->dataFim == '--')) {
                $sql .= 'AND c.cli_creation_date >= "' . $filter->dataInicio . '" ';
            }

            if (($filter->dataInicio == '--') && ($filter->dataFim != '--')) {
                $sql .= 'AND c.cli_creation_date <= "' . $filter->dataFim . '" ';
            }

            if (!empty($filter->cli_name)) {
                $sql .= ' AND UPPER(c.cli_name) LIKE "%' . strtoupper($filter->cli_name) . '%" ';
            }

            if (!empty($filter->active)) {
                $sql .= ' AND c.cli_active = ' . $filter->active;
            }

            $sql .= ' ORDER BY cli_name';

            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

}
