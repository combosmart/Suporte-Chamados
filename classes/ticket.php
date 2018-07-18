<?php
/**
* Classe com os métodos de manipulação do objeto Cliente.
*/

class Ticket {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;

    function __construct($db) {
       $this->pdo = $db;
    }
    
    
    /**
    * Listar os clientes com equipamentos
    *
    * @return array Returns list of clients.
    */
    public function listClientsWithMachine() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT DISTINCT  c.cli_id,
                                      c.cli_name
                     FROM hlp_client c, hlp_ponto p
                    WHERE p.cli_id = c.cli_id
                    AND EXISTS (SELECT 1 FROM hlp_mchn m WHERE m.pnt_id = p.pnt_id)';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os pontos de cliente com equipamentos
    * @param int $cli_id Id do cliente .
    * @return array Returns list of players.
    */
    public function listPlayersWithMachineByClient($cli_id) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT DISTINCT p.pnt_id, 
                                    p.pnt_name 
                    FROM hlp_ponto p 
                    WHERE p.cli_id = ?
                    AND EXISTS (SELECT 1 FROM hlp_mchn m WHERE m.pnt_id = p.pnt_id)';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cli_id]);
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**    
    * Função para registrar um novo ticket
    * @param string $ticket Dados do Ticket.
    * @return boolean of success.
    */
    public function addTicket($ticket) {
        $sql = 'INSERT INTO hlp_tickets
                (sta_id,
                mch_id,
                usr_id,
                ntr_id,
                prb_id,
                tkt_dt_open,                
                tkt_notes,
                usr_id_created,
                tkt_dt_creation)
                VALUES
                (?,?,?,?,?,?,?,?,NOW())';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([
                $ticket->status,
                $ticket->equipamento,
                $ticket->usuario,
                $ticket->natureza,
                $ticket->problema,
                $ticket->dataAbertura,
                $ticket->obs,
                $ticket->usrCreated
        ])) { 
            $id = $pdo->lastInsertId();            
            $tkt_sku = $id + 10000;            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare('UPDATE hlp_tickets set tkt_sku = ? WHERE tkt_id = ?');
            $stmt->execute([ $tkt_sku, $id ]);
            return $tkt_sku;
        } else {            
            return -1; 
        }        
    }


    /**    
    * Função para registrar upload de imagens para um ticket
    * @param string $ticket Dados da imagem.
    * @return boolean of success.
    */
    public function uploadImage($imagem) {
        $sql = 'INSERT INTO hlp_tkt_uploads
                (tkt_id, upl_path,upl_obs)
                VALUES(?,?,?)';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([
                $imagem->ticket,
                $imagem->path,
                $imagem->descricao
        ])) { 
            return true;
        } else {            
            return false; 
        }        
    }

    /**    
    * Função para remover uma imagem do ticket
    * @param int $upl_id Id da imagem.
    * @return boolean of success.
    */
    public function deleteImage($upl_id) {
        $sql = 'DELETE FFROM hlp_tkt_uploads WHERE upl_id = ?';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$upl_id])) { 
            return true;
        } else {            
            return false; 
        }        
    }



    /**
    * Função para alterar dados do ticket
    * @param int $id Id do Ticket.
    * @return boolean of success.
    */
    public function ticketUpdate($ticket){
        $sql = 'UPDATE hlp_tickets
                SET
                sta_id = ?,
                mch_id = ?,
                ntr_id = ?,
                prb_id = ?,
                tkt_dt_creation = ?,
                tkt_dt_close = ?,
                tkt_notes = ?,
                tkt_notes_close = ?,
                prb_id_close = ?
                WHERE tkt_id = ?';
        $pdo = $this->pdo;
        if(isset($ticket)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([
                $ticket->status,
                $ticket->equipamento,
                $ticket->natureza,
                $ticket->problema,
                $ticket->dataAbertura,
                $ticket->dataFechamento,
                $ticket->obs,
                $ticket->obsFechamento,
                $ticket->problemaFechamtento,
                $ticket->id
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
    * Listar os tickets
    *
    * @return array Returns list of tickets
    */
    public function searchTickets($filter) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'select t.tkt_id,
                           t.tkt_sku,
                           date(t.tkt_dt_open) as tkt_dt_open,
                           date(t.tkt_dt_close) as tkt_dt_close,
                           n.ntr_name,       
                           s.sta_name,
                           k.pri_name,
                           p.pnt_name,
                           p.pnt_address,
                           p.pnt_number,
                           p.pnt_neighbor,
                           p.pnt_city,
                           p.pnt_state,
                           p.pnt_zip,
                           p.pnt_notes,
                           w.prb_name,
                           u.usr_name as usuario_atribuido,
                           x.usr_name as usuario_criador,
                           IFNULL(c.cli_nmfnt,c.cli_name) AS cli_name,
                           m.mch_sku,
                           t.tkt_notes,
                           tkt_notes_close,
                           t.sta_id
                      from hlp_tickets t    
                    inner join hlp_tkt_ntrza n on t.ntr_id = n.ntr_id 
                    inner join hlp_tkt_status s on t.sta_id = s.sta_id 
                    inner join hlp_mchn m on t.mch_id = m.mch_id 
                    inner join hlp_ponto p on m.pnt_id = p.pnt_id
                    inner join hlp_client c on p.cli_id = c.cli_id
                    inner join hlp_tkt_priority k on c.pri_id = k.pri_id 
                    inner join hlp_tkt_problem w on t.prb_id = w.prb_id 
                     left join hlp_user u on t.usr_id = u.usr_id
                    inner join hlp_user x on t.usr_id_created = x.usr_id 
                    where 1=1 ';
            if (($filter->dataInicio != '--') && ($filter->dataFim != '--')) { 
                $sql .= 'AND  (t.tkt_dt_open BETWEEN "'.  $filter->dataInicio . '" AND "'. $filter->dataFim. '") '; 
            }

            if (($filter->dataInicio != '--') && ($filter->dataFim == '--')) {
                $sql .= 't.tkt_dt_open >= "' . $filter->dataInicio . '" ';
            }

            if (($filter->dataInicio == '--') && ($filter->dataFim != '--')) {
                $sql .= 't.tkt_dt_open <= "' . $filter->dataFim . '" ';
            }      

            if (!empty($filter->natureza)) {
                $sql .= ' AND t.ntr_id = "' . $filter->natureza . '"' ;
            }

            if (!empty($filter->status)) {
                $sql .= ' AND t.sta_id = ' . $filter->status ;
            }

            if (!empty($filter->cliente)) {
                $sql .= ' AND c.cli_id = ' . $filter->cliente ;
            }

            if (!empty($filter->usuario)) {
                $sql .= ' AND t.usr_id = ' . $filter->usuario ;
            }

            if (!empty($filter->id)) {
                $sql .= ' AND t.tkt_id = ' . $filter->id ;
            }

            if (!empty($filter->prioridade)) {
                $sql .= ' AND c.pri_id = ' . $filter->prioridade ;
            }

            if (!empty($filter->player)) {
                $sql .= ' AND p.pnt_id = ' . $filter->player;
            }

            $sql .= ' order by t.tkt_id';
            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Extrair os dados de um ponto baseado em seu id
    *
    * @param int $player id do Ponto.
    * @return array retorna um array com os dados do cliente.
    */
    public function getTicket($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT t.tkt_id,
                           t.sta_id,
                           t.mch_id,
                           t.usr_id,
                           t.ntr_id,
                           t.prb_id,
                           DATE(t.tkt_dt_open) as dt_open,
                           DATE(t.tkt_dt_creation) as dt_creation,
                           DATE(t.tkt_dt_close) as dt_close,
                           t.tkt_notes,
                           t.tkt_notes_close,
                           t.usr_id_created,
                           p.pnt_id,
                           p.pnt_name,
                           k.pri_name,
                           m.mch_sku,
                           t.tkt_sku
                      FROM hlp_tickets t,
                           hlp_mchn m,
                           hlp_ponto p,
                           hlp_client c,
                           hlp_tkt_priority k
                     WHERE tkt_id = ? 
                     AND t.mch_id = m.mch_id
                     AND m.pnt_id = p.pnt_id 
                     AND p.cli_id = c.cli_id
                     AND c.pri_id = k.pri_id';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(); 
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
    * Listar o status
    *
    * @return array Returns list of statuses.
    */
    public function listStatus() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT sta_id,
                            sta_name
                       FROM hlp_tkt_status';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os problemas
    *
    * @return array Returns list of known problems.
    */
    public function listProblems() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT prb_id,
                            prb_name
                       FROM hlp_tkt_problem';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os tipos de chamado
    *
    * @return array Returns list of type of ticket.
    */
    public function listTicketType() {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT ntr_id,
                            ntr_name
                       FROM hlp_tkt_ntrza';            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

}
