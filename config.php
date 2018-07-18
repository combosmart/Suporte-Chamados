<?php
ob_start();
session_start();

//set timezone
setlocale(LC_ALL, 'pt_BR', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_MONETARY,"pt_BR", "ptb");

//database credentials
define('DBHOST','combo_helpdesk.mysql.dbaas.com.br');
define('DBUSER','combo_helpdesk');
define('DBPASS','HelpDesk123@#');
define('DBNAME','combo_helpdesk');

//application address
define('DIR','http://suporte.combovideos.com.br');
define('SITEEMAIL','suporte@combovideos.com.br');

//perfis de acesso
define('ADMINISTRADOR','1');
define('TECNICO','2');
define('USUARIO','3');
define('GERENTE','4');

//status de aprovação
define('CHAMADO_PENDENTE','1');
define('CHAMADO_APROVADO','2');
define('CHAMADO_REPROVADO','3');

//status de chamado
define('HELPDESK_ABERTO','1');
define('HELPDESK_ACIONADO','2');
define('HELPDESK_CANCELADO','3');
define('HELPDESK_FECHADO','4');
define('HELPDESK_PENDENTE','5');


//status das ocorrencias
define('OCORRENCIA_ABERTA','A');
define('OCORRENCIA_FECHADA','F');

try {

	//create PDO connection
	$db = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
	//show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}

include('classes/user.php');
include('classes/client.php');
include('classes/player.php');
include('classes/machine.php');
include('classes/route.php');
include('classes/ticket.php');
include('classes/atdmt.php');
include('classes/contact.php');

include('classes/utils.php');
include('classes/phpmailer/mail.php');

$user    = new User($db);
$client  = new Client($db);
$player  = new Player($db);
$machine = new Machine($db);
$route   = new Route($db);
$ticket  = new Ticket($db);
$atdmt   = new Atendimento($db);
$contact = new Contact($db);