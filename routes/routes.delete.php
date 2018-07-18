<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  $origem = '/(www.)?(suporte.combovideos.com.br/routes/routes.php)/g';
  if (preg_match($origem, $_SERVER["HTTP_REFERER"])) {
  	header('Location: routes.php');
  } else {
  	$id = $_GET['id'];
  	if ($route->deleteRoute($id)) {
  		$_SESSION['success'] = 'Percurso removido com sucesso';
  		header('Location: routes.php');
  	}
  }

?>
