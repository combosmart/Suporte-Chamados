<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','HelpDesk - Abertura de Chamado');    
?>
<?php
  
  $id  = $_GET['id'];
  $listClientsWithMachine = $ticket->listClientsWithMachine();  
  $obj = $ticket->getTicket($id);  

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

	if ($_POST['sta_id'] == HELPDESK_FECHADO) {		
		if ($_POST['tkt_dt_close'] == '--') {
			$error[] = 'Informe a data de fechamento';		
		}
	}

	$chamado = new ArrayObject();  

	$chamado->id                 = $_POST['tkt_id'];  
	$chamado->usuario            = $_POST['usr_id'];
	$chamado->status             = $_POST['sta_id'];
	$chamado->equipamento        = $_POST['mch_id'];
	$chamado->natureza           = $_POST['ntr_id'];
	$chamado->problema           = $_POST['prb_id'];
	$chamado->dataAbertura       = formataDataMySQL($_POST['tkt_dt_open']);;
	$chamado->obs                = $_POST['tkt_notes'];	
	$chamado->obsFechamento      = $_POST['tkt_notes_close'];	
	$chamado->dataFechamento     = formataDataMySQL($_POST['tkt_dt_close']);
	$chamado->problemaFechamento = $_POST['prb_id_close'];

	if ($ticket->ticketUpdate($chamado)) {

    	if (empty($chamado->usuario)) {
    		$email = new ArrayObject();  
    		$email->address   = $_SESSION['user']['email'];
    		$email->subject   = 'Alteração de Status do Chamado - ' . date('d/m/Y');
    		$email->message   = 'O chamado XXXXX teve seu status alterado para XXXXX.<br>'; 
    		$email->message  .= 'Confira os detalhes acessando o endereço abaixo:<br>'; 
    		$email->message  .= DIR . '/tickets/ticket.edit.php?id=' . $chamado->id;
            sendConfirmationEmail($email);
    	}

    	$_SESSION['success'] = 'Chamnado alterado com sucesso';
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
														<input id="date" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="tkt_dt_open" value="<?=formataData($obj['dt_open'])?>">
												</div>
											</div>
											<div class="col-lg-4">
												<label class="control-label text-lg-left pt-2">Status</label>
												<select class="form-control mb-3" name="sta_id" onchange="showDivFechamento(this.value)">
													<?php foreach ($ticket->listStatus() as $row) { ?>
														<option value="<?=$row['sta_id']?>" <?php if ($obj['sta_id'] == $row['sta_id']) echo "selected" ?> ><?=$row['sta_name']?></option>
													<?php } ?>
												</select>		
											</div>
											<div class="col-lg-4">
												<span id="prioridade">
													<label class="control-label text-lg-left pt-2">Prioridade</label>
													<div class="alert alert-default">
														<?=$obj['pri_name']?>
													</div>
												</span>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-4">
												<label class="control-label text-lg-left pt-2">Usuário</label>
												<select class="form-control mb-3" name="usr_id">
													<option value="">Por enquanto, nenhum</option>
													<?php foreach ($user->listUsers(TECNICO) as $u) { ?>
														<option value="<?=$u['usr_id']?>" <?php if ($obj['usr_id'] == $u['usr_id']) echo "selected" ?> ><?=$u['usr_name']?></option>
													<?php } ?>
												</select>		
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Ponto</label>
												<div>
													<select data-plugin-selectTwo class="form-control populate" name="mch_id" onchange="showEquip(this.value)">
														<?php foreach ($listClientsWithMachine as $cli) { ?>
														<option value="">Selecione um ponto</option>
														<optgroup label="<?=$cli['cli_name']?>">
															<?php foreach ($ticket->listPlayersWithMachineByClient($cli['cli_id']) as $pl) { ?>
																<option value="<?=$pl['pnt_id']?>" <?php if ($obj['pnt_id'] == $pl['pnt_id']) echo "selected" ?> ><?=$pl['pnt_name']?></option>
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

													function showDivFechamento(status) {
														if (status == <?=HELPDESK_FECHADO?>) {
															document.getElementById('fechamento').style.display= 'block' ;
														} else {
															document.getElementById('fechamento').style.display= 'none' ;
															document.getElementById('tkt_dt_close').value = "";
															document.getElementById('tkt_notes_close').value = "";
														}
													}
												</script>
											</div>				
											<div class="col-lg-4">
												<span id="equipamentos">
													<label class="control-label text-lg-left pt-2">Equipamento</label>
													<select class="form-control mb-3" name="mch_id">
														<?php foreach ($machine->selectMachinesByPlayer($obj['pnt_id']) as $k) { ?>
															<option value="<?=$k['mch_id']?>"><?=$k['mch_sku']?></option>
														<?php } ?>
													</select>
												</span>
											</div>							
										</div>
										<div class="form-group row">
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Tipo de Chamado</label>
												<select class="form-control mb-3" name="ntr_id">
													<?php foreach ($ticket->listTicketType() as $k) { ?>
														<option value="<?=$k['ntr_id']?>" <?php if ($obj['ntr_id'] == $k['ntr_id']) echo "selected" ?> ><?=$k['ntr_name']?></option>
													<?php } ?>
												</select>
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Tipo de Problema</label>
												<select class="form-control mb-3" name="prb_id">
													<?php foreach ($ticket->listProblems() as $k) { ?>
														<option value="<?=$k['prb_id']?>" <?php if ($obj['prb_id'] == $k['prb_id']) echo "selected" ?> ><?=$k['prb_name']?></option>
													<?php } ?>
												</select>
											</div>											
										</div>	
										<div class="form-group">
											<label class="control-label text-lg-left pt-2" for="textareaDefault">Observações</label>
											<div>
												<textarea class="form-control" rows="3" id="textareaDefault" name="tkt_notes" required="required"><?=$obj['tkt_notes']?></textarea>
											</div>
										</div>
										<span id="fechamento" style="display: none;">
											<div class="form-group row">
												<div class="col-lg-4">
													<label class="control-label text-lg-left pt-2">Data de Fechamento</label>
													<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</span>
															<input id="tkt_dt_close" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="tkt_dt_close" value="<?=formataData($obj['tkt_dt_close'])?>">
													</div>
												</div>												
												<div class="col-lg-4">							
													<label class="control-label text-lg-left pt-2">Tipo de Problema (Fechamento)</label>
													<select class="form-control mb-3" name="prb_id">
														<?php foreach ($ticket->listProblems() as $k) { ?>
															<option value="<?=$k['prb_id']?>" <?php if ($obj['prb_id_close'] == $k['prb_id']) echo "selected" ?> ><?=$k['prb_name']?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label text-lg-left pt-2" for="textareaDefault">Observações</label>
												<div>
													<textarea class="form-control" rows="3" id="tkt_notes_close" name="tkt_notes_close" required="required"><?=$obj['tkt_notes_close']?></textarea>
												</div>
											</div>
										</span>																		
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="add" class="btn btn-primary">Salvar</button>
											<button type="button" onclick="window.location='tickets.php'" class="btn btn-primary">Voltar</button>
										</div>
									</div>
								</footer>
								<input type="hidden" name="tkt_id" value="<?=$id?>">
								<input type="hidden" name="tkt_sku" value="<?=$obj['tkt_sku']?>">
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