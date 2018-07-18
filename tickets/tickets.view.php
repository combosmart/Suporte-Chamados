<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','HelpDesk - Abertura de Chamado');    
?>
<?php
  
  	$filter = new ArrayObject();
	
	$filter->id  = $_GET['id'];
	$filter->dataInicio = '--';
	$filter->dataFim = '--';
  	
  	$obj = $ticket->searchTickets($filter)[0];  

  	/*
	if (!empty($_POST)) {

	  	unset($_SESSION['success']);
	    $error = [];
	    $success = [];

	    if (empty($_POST['tkt_dt_open'])) {
	    	$error[] = 'Preencha a data';
	    }

	    if (!validaData($_POST['tkt_dt_open'])) {
			$error[] = 'Data inválida';
		}

		$chamado = new ArrayObject();  

		$chamado->usuario      = $_POST['usr_id'];
		$chamado->status       = empty($chamado->usuario) ? HELPDESK_ABERTO : HELPDESK_ACIONADO;
		$chamado->equipamento  = $_POST['mch_id'];
		$chamado->natureza     = $_POST['ntr_id'];
		$chamado->problema     = $_POST['prb_id'];
		$chamado->dataAbertura = formataDataMySQL($_POST['tkt_dt_open']);;
		$chamado->obs          = $_POST['tkt_notes'];
		$chamado->usrCreated   = $_SESSION['user']['id'];

		$chamado->id = $ticket->addTicket($chamado);
	    if ($chamado->id > 0) {

	    	// envio de email após abertura de chamado
	    	// o texto de email muda caso haja ou não redirecionamento para técnico
	    	if (empty($chamado->usuario)) {
	    		$email = new ArrayObject();  
	    		$email->address   = 'felipe@combovideos.com.br';
	    		$email->subject   = 'Abertura de Chamado - ' . date('d/m/Y');
	    		$email->message   = 'Um novo chamado foi aberto no sistema.<br>'; 
	    		$email->message  .= 'Confira os detalhes e redirecione-o para um técnico para sua execução, acessando o endereço abaixo:<br>'; 
	    		$email->message  .= DIR . '/tickets/ticket.edit.php?id=' . $chamado->id;
	            sendConfirmationEmail($email);
	    	} else {
	    		$email = new ArrayObject();  
	    		$email->address  = 'felipe@combovideos.com.br';
	    		$email->subject  = 'Abertura de Chamado - ' . date('d/m/Y');
	    		$email->message   = 'Um novo chamado foi aberto no sistema e redirecionado a você.<br>'; 
	    		$email->message  .= 'Confira os detalhes, acessando o endereço abaixo:<br>'; 
	    		$email->message  .= DIR . '/tickets/ticket.edit.php?id=' . $chamado->id;
	            sendConfirmationEmail($email);
	    	}

	    	$_SESSION['success'] = 'Chamnado adicionado com sucesso';
		    header('Location: tickets.php');	    
	    }
        
	}
	*/
?>
	<?php include '../header.php'; ?>
	<body>
		<section class="body">

			<!-- start: header -->
			<?php include '../top.php'; ?>
			<!-- end: header -->

			<div class="inner-wrapper">
				<!-- start: sidebar -->
				<?php include '../sidebar.php'; ?>
				<!-- end: sidebar -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2><?=sitetile?></h2>											
					</header>
					<!-- start: page -->
					<div class="row">
						<div class="col">
							<section class="card">
								<header class="card-header">
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										<a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
									</div>
									<h2 class="card-title"><?=sitetile?></h2>									
								</header>
								<div class="card-body">
									<?php if(isset($error)) { 
										echo "<div class='alert alert-danger'>";
										echo "<ul>";
										foreach ($error as $e) {
											echo '<li>'. $e . '</li>';
										}
										echo "</ul></div>";
					                } ?>
									<form class="form-bordered" method="post">
										<div class="form-group row">
											<div class="col-lg-4">
												<label class="control-label text-lg-left pt-2">Número</label>
												<div class="input-group">
													<h4><?=$obj['tkt_sku']?></h4>	
												</div>
											</div>
											<div class="col-lg-4">
												<label class="control-label text-lg-left pt-2">Data</label>
												<div class="input-group">
													<h4><?=formataData($obj['tkt_dt_open'])?></h4>	
												</div>
											</div>
											<div class="col-lg-4">
												<label class="control-label text-lg-left pt-2">Prioridade</label>
												<div class="input-group">
													<h4><?=$obj['pri_name']?></h4>	
												</div>												
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-4">
												<label class="control-label text-lg-left pt-2">Usuário</label>
												<h4><?php echo empty($obj['usuario_atribuido'])?'Não atribuído':$obj['usuario_atribuido'] ?></h4>
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Ponto</label>
												<h4>
													<a data-toggle="modal" data-target="#modalBootstrap" href="#">
														<?=$obj['pnt_name']?>
													</a>
												</h4>												
											</div>				
											<div class="col-lg-4">
												<span id="equipamentos">
													<label class="control-label text-lg-left pt-2">Equipamento</label>
													<h4><?=$obj['mch_sku']?></h4>
												</span>
											</div>							
										</div>
										<div class="form-group row">
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Tipo de Chamado</label>
												<h4><?=$obj['ntr_name']?></h4>
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Tipo de Problema</label>
												<h4><?=$obj['prb_name']?></h4>
											</div>											
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Staus</label>
												<h4><?=$obj['sta_name']?></h4>
											</div>
										</div>	
										<div class="form-group">
											<label class="control-label text-lg-left pt-2" for="textareaDefault">Observações</label>
											<h4><?=$obj['tkt_notes']?></h4>											
										</div>																		
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="button" onclick="window.location='tickets.php'" class="btn btn-primary">Voltar</button>
										</div>
									</div>
								</footer>
								</form>
							</section>
							<!-- Modal Endereço -->
							<div class="modal" id="modalBootstrap" tabindex="-1" role="dialog">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title"><?=$obj['pnt_name']?> - Endereço</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body">
											<p><?=$obj['pnt_address']?> - <?=$obj['pnt_number']?> - <?=$obj['pnt_neighbor']?></p>
											<p><?=$obj['pnt_city']?>/<?=$obj['pnt_state']?> - <?=$obj['pnt_zip']?></p>
											<?php if (!empty($obj['pnt_notes'])) { ?>
												<p>Observação<br/>
												<?=$obj['pnt_notes']?>
											<?php } ?>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
										</div>
									</div>
								</div>
							</div>
							<!-- /Modal Endereço -->
						</div>
					</div>
					<!-- end: page -->
				</section>
			</div>			
		</section>
		<!-- start: footer -->
		<?php include '../footer.php'; ?>
		<!-- end: footer -->
	</body>
</html>