<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','HelpDesk - Abertura de Chamado');    
?>
<?php
  $listClientsWithMachine = $ticket->listClientsWithMachine();
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
    		$email->message  .= DIR . '/tickets/tickets.edit.php?id=' . $chamado->id;
            sendConfirmationEmail($email);
    	} else {
    		$email = new ArrayObject();  
    		$email->address  = 'felipe@combovideos.com.br';
    		$email->subject  = 'Abertura de Chamado - ' . date('d/m/Y');
    		$email->message   = 'Um novo chamado foi aberto no sistema e redirecionado a você.<br>'; 
    		$email->message  .= 'Confira os detalhes, acessando o endereço abaixo:<br>'; 
    		$email->message  .= DIR . '/tickets/tickets.edit.php?id=' . $chamado->id;
            sendConfirmationEmail($email);
    	}

    	$_SESSION['success'] = 'Chamado '. $chamado->id . ' adicionado com sucesso';
	    header('Location: tickets.php');	    
    }
        
}
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
								<?php if (empty($listClientsWithMachine)) { ?>								
								<div class="card-body">
									<div class="alert alert-info nomargin">
										<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
										<h4>Não há equipamentos cadastrados!</h4>
										<p>Para abrir um chamado, é preciso que haja pelo menos um equipamento cadastrado no sistema. Vá para a tela de administração de equipamentos e adicione um para prosseguir</p>
										<p>
											<button class="btn btn-default mt-1 mb-1" type="button"><a href="../machines/machines.add.php">Navegar para o cadastro de equipamentos</a></button>
										</p>
									</div>
								</div>
								<?php } else { ?>
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
												<label class="control-label text-lg-left pt-2">Data</label>
												<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</span>
														<input id="date" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="tkt_dt_open" required="required">
												</div>
											</div>
											<div class="col-lg-4">
												<span id="prioridade"></span>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-4">
												<label class="control-label text-lg-left pt-2">Usuário</label>
												<select class="form-control mb-3" name="usr_id">
													<option value="">Por enquanto, nenhum</option>
													<?php foreach ($user->listUsers(TECNICO) as $u) { ?>
														<option value="<?=$u['usr_id']?>"><?=$u['usr_name']?></option>
													<?php } ?>
												</select>		
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Ponto</label>
												<div>
													<select data-plugin-selectTwo class="form-control populate" name="pnt_id" onchange="showEquip(this.value)">
														<?php foreach ($listClientsWithMachine as $cli) { ?>
														<option value="">Selecione um ponto</option>
														<optgroup label="<?=$cli['cli_name']?>">
															<?php foreach ($ticket->listPlayersWithMachineByClient($cli['cli_id']) as $pl) { ?>
																<option value="<?=$pl['pnt_id']?>"><?=$pl['pnt_name']?></option>
															<?php } ?>
														</optgroup>
														<?php } ?>
													</select>
												</div>
												<script>
													function showEquip(str) {
														if (str.length == 0) { 
													        document.getElementById("equipamentos").innerHTML = "";
													        return;
													    } else {
													        var xmlhttp = new XMLHttpRequest();
													        xmlhttp.onreadystatechange = function() {
													            if (this.readyState == 4 && this.status == 200) {
													                document.getElementById("equipamentos").innerHTML = this.responseText;
													            }
													        };
													        xmlhttp.open("GET", "ajaxSelectEquipamentos.php?mode=equip&q=" + str, true);
													        xmlhttp.send();
													        showPriority(str);
													    }													    
													}

													function showPriority(str) {
														if (str.length == 0) { 
													        document.getElementById("prioridade").innerHTML = "";
													        return;
													    } else {
													        var xmlhttp = new XMLHttpRequest();
													        xmlhttp.onreadystatechange = function() {
													            if (this.readyState == 4 && this.status == 200) {
													                document.getElementById("prioridade").innerHTML = this.responseText;
													            }
													        };
													        xmlhttp.open("GET", "ajaxSelectEquipamentos.php?mode=prioridade&q=" + str, true);
													        xmlhttp.send();													     
													    }
													}
												</script>
											</div>				
											<div class="col-lg-4">
												<span id="equipamentos">&nbsp;</span>
											</div>							
										</div>
										<div class="form-group row">
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Tipo de Chamado</label>
												<select class="form-control mb-3" name="ntr_id">
													<?php foreach ($ticket->listTicketType() as $k) { ?>
														<option value="<?=$k['ntr_id']?>"><?=$k['ntr_name']?></option>
													<?php } ?>
												</select>
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Tipo de Problema</label>
												<select class="form-control mb-3" name="prb_id">
													<?php foreach ($ticket->listProblems() as $k) { ?>
														<option value="<?=$k['prb_id']?>"><?=$k['prb_name']?></option>
													<?php } ?>
												</select>
											</div>											
										</div>	
										<div class="form-group">
											<label class="control-label text-lg-left pt-2" for="textareaDefault">Observações</label>
											<div>
												<textarea class="form-control" rows="3" id="textareaDefault" name="tkt_notes" required="required"></textarea>
											</div>
										</div>																		
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="add" class="btn btn-primary">Salvar</button>
											<button type="button" onclick="window.location='tickets.php'" class="btn btn-primary">Voltar</button>
										</div>
									</div>
								</footer>
								</form>
								<?php } // listClientsWithMachine?>
							</section>
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