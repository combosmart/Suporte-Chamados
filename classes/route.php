<?php
/**
* Classe com os métodos de manipulação do objeto Rota.
*/

class Route {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;

    function __construct($db) {
       $this->pdo = $db;
    }
    
    /**
    * Função para registrar um novo cliente
    * @param ArrayObject $route Objeto Route.
    * @return boolean of success.
    */
    public function addRoute($route) {
        $sql = 'INSERT INTO hlp_reg_route
                (
                usr_id,
                reg_data,
                pnt_id_de,
                pnt_id_para,
                reg_order,
                reg_km
                )
                VALUES
                (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?)';
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([
                            $route->user,
                            $route->data,               
                            $route->origem,
                            $route->destino,
                            $route->ordem,
                            $route->km
                          ])) { 
            return true;
        } else {            
            return false; 
        }        
    }

    /**
    * Função para alterar dados do percurso
    * @param ArrayObject $route Objeto Route.
    * @return boolean of success.
    */
    public function routeUpdate($route){
        $sql = 'UPDATE hlp_reg_route
                SET
                reg_data = ?,
                pnt_id_de = ?,
                pnt_id_para = ?,
                reg_order = ?,
                reg_km = ?,
                reg_obs = ?
                WHERE reg_id = ?';
        $pdo = $this->pdo;
        if(isset($route->id)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([
                                $route->data,               
                                $route->origem,
                                $route->destino,
                                $route->ordem,
                                $route->km,
                                $route->obs,
                                $route->id
                              ])){
                return true;
            }else{
                $this->msg = 'Erro na alteração de dados do percurso.';
                return false;
            }
        }else{
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

    /**
    * Extrair os dados de um cliente baseado em seu id
    *
    * @return array returna um array com os dados do cliente.
    */
    public function getRoute($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'SELECT
                        reg_id, 
                        usr_id,
                        DATE(reg_data) as dia,
                        pnt_id_de,
                        pnt_id_para,
                        reg_order,
                        reg_km, 
                        reg_obs,
                        sta_id
                    FROM hlp_reg_route 
                    WHERE reg_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(); 
            return $result;
        }
    }

    /**
    * Listar os percursos
    *
    * @return array Returns list of routes.
    */
    public function listRoutesByUser($id) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT 
                        reg_id,
                        reg_data,
                        pnt_id_de,
                        pnt_id_para,
                        reg_order,
                        reg_km
                    FROM hlp_reg_route 
                    WHERE usr_id = ? ORDER BY reg_order';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Listar os percursos filtrados por data
    *
    * @return array Retorna a lista de percursos.
    */
    public function searchRoutes($usr_id=null,$beginDate=null,$endDate=null,$status=null) {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql  = 'SELECT 
                          r.usr_id,
                          r.reg_id,
                          DATE(r.reg_data) as dia,
                          r.pnt_id_de,
                          r.pnt_id_para,
                          r.reg_order,
                          r.reg_km,
                          r.sta_id,
                          u.usr_name,
                          p1.pnt_name as origem,
                          p2.pnt_name as destino,    
                          s.sta_name,
                          r.reg_justif
                     FROM hlp_reg_route r, 
                          hlp_user u, 
                          hlp_ponto p1,
                          hlp_ponto p2,
                          hlp_route_status s
                    WHERE r.usr_id = u.usr_id 
                      AND r.sta_id = s.sta_id ';                    
            
            if (($beginDate != '--') && ($endDate != '--')) { 
                $sql .= 'AND  (r.reg_data BETWEEN "'.  $beginDate . '" AND "'. $endDate. '") '; 
            }

            if (($beginDate != '--') && ($endDate == '--')) {
                $sql .= 'AND r.reg_data >= "' . $beginDate . '" ';
            }

            if (($beginDate == '--') && ($endDate != '--')) {
                $sql .= 'AND r.reg_data <= "' . $endDate . '" ';
            }

            if (!empty($usr_id)) { 
                $sql .= 'AND r.usr_id = '.$usr_id; 
            }                                

            if (!empty($status)) { 
                $sql .= 'AND r.sta_id = '.$status; 
            }                                
            
            $sql .= ' AND u.grp_id =' . TECNICO;
            $sql .= ' AND r.pnt_id_de = p1.pnt_id 
                      AND r.pnt_id_para = p2.pnt_id 
                      ORDER BY u.usr_name, r.reg_data, r.reg_order';
            
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }

    /**
    * Função que verifica a a ordem de um percurso para o dia
    * @param string $percurso Percurso informado.
    * @return boolean of success.
    */
    public function checkOrder($percurso,$mode=null){
        $sql = 'SELECT reg_id 
                  FROM hlp_reg_route 
                 WHERE reg_order = ? 
                   AND DATE(reg_data) =  ?
                   AND usr_id = ? ';
        if (!empty($mode)) {
        	$sql .= 'AND reg_id NOT IN (?)';        	
            $arrExecute = array($percurso->ordem, $percurso->data, $percurso->user, $percurso->id);
        } else {
        	$arrExecute = array($percurso->ordem, $percurso->data, $percurso->user);
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
    * Remover um percurso
    * @param $id Id do percurso.
    * @return boolean of success.
    */
    public function deleteRoute($id){
        if(is_null($this->pdo)){
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $sql = 'DELETE FROM hlp_reg_route WHERE reg_id = ?';
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$id]);
        }
    }

    /**
    * Função para aprovar percurso
    * @param Int $reg_id Id do Percurso.
    * @return boolean of success.
    */
    public function routeApproval($reg_id){
        $sql = 'UPDATE hlp_reg_route
                SET
                sta_id = 2 
                WHERE reg_id = ?';
        $pdo = $this->pdo;
        if(isset($reg_id)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([$reg_id])){
                return true;
            }else{
                $this->msg = 'Erro na aprovação do percurso.';
                return false;
            }
        }else{
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

    /**
    * Função para reprovar percurso
    * @param Int $reg_id Id do Percurso.
    * @return boolean of success.
    */
    public function routeRejection($reg_id,$reg_justif){
        $sql = 'UPDATE hlp_reg_route
                SET
                sta_id = 3, reg_justif = ? 
                WHERE reg_id = ?';
        $pdo = $this->pdo;
        if(isset($reg_id)){
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([$reg_justif, $reg_id])){
                return true;
            }else{
                $this->msg = 'Erro na rejeição do percurso.';
                return false;
            }
        }else{
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

}
