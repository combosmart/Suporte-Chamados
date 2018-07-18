<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Pesquisa de Chamados');
?>
<?php 

	if (isset($_POST['excel'])) {
		$filename = "registro_" . time() . ".csv";
		$delimiter = ';';
		$fields = array('Cliente','Ponto','Tipo','Patrimônio','Status','Cadastrado','Data');
		$f = fopen('php://memory', 'w');
		fputcsv($f, $fields, $delimiter);
		foreach ($_SESSION['machines'] as $row) {
			 $lineData = array($row['cli_name'],
			 				   $row['pnt_name'].' - '.$row['pnt_state'],
			 				   $row['typ_name'],
			 				   $row['mch_sku'],
			 				   label_active_txt($row['mch_active']),
			 				   $row['usr_name'],
			 				   $row['dia']);
			 fputcsv($f, $lineData, $delimiter);			 
		}

		//move back to beginning of file
    	fseek($f, 0);
    
	    //set headers to download file rather than displayed
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename="' . $filename . '";');
	    
	    //output all remaining data on a file pointer
	    fpassthru($f);	    
	    exit;
	}

	if (isset($_POST['search'])) {
		
		if (!empty($_POST['begin'])) {
			if (!validaData($_POST['begin'])) {
				$error[]   = 'Data inicial inválida';
			}
		}

		if (!empty($_POST['end'])) {
			if (!validaData($_POST['end'])) {
				$error[]   = 'Data final inválida';
			}
		}
	  	
	  	$filter = new ArrayObject();  

	    $filter->dataInicio	     = formataDataMySQL($_POST['begin']);
		$filter->dataFim	     = formataDataMySQL($_POST['end']);
		$filter->cliente         = $_POST['cli_id'];
		$filter->player          = $_POST['pnt_id'];	
		$filter->prioridade      = $_POST['pri_id'];	
		$filter->status          = $_POST['sta_id'];
		$filter->usuario         = $_POST['usr_id'];
	    $filter->natureza        = $_POST['ntr_id'];

	    if(!isset($error)) {
	    	$tickets = [];
	    	$tickets = $ticket->searchTickets($filter);
	    	//limpar o resultado da pesquisa anterior, caso exista
	    	unset($_SESSION['tickets']);
	    	if (count($tickets) <= 0) {
	    		$error[]   = 'Não há resultados disponíveis';
	    		unset($_SESSION['filter']['tickets']); 
	    		$_SESSION['tickets'] = $tickets;
	    	} else {	    		
	    		$_SESSION['filter']['tickets'] = $filter; 
	    		$_SESSION['tickets'] = $tickets;
	    	}	    	
		}	
	}
	
	if (isset($_SESSION['filter']['tickets'])) {
		$tickets = $ticket->searchTickets($_SESSION['filter']['tickets']);
	}
 ?>
	<!-- start: header -->
	<?php include '../header.php'; ?>
	<!-- end: header -->
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
									<?php 
									if (isset($_SESSION['success'])) {
			          					echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
			          					unset($_SESSION['success']);
			          				} ?>	
			          				<?php 
									if (isset($error)) {
			          					echo "<div class='alert alert-danger'>";
			          					echo "<ul>";
			          					foreach ($error as $e) {
			          						echo "<li>".$e."</li>";
			          					}
			          					echo "</ul>";
			          					echo "</div>";	
			          					$error = [];		          					
			          				} ?>	
									<form class="form-bordered" method="post">
										<div class="form-group row">
											<div class="col-lg-6">
												<label class="control-label text-lg-left pt-2">Data</label>												
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</span>
													<input id="begin" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="begin">
													<span class="input-group-addon">até</span>
													<input id="end" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="end">
												</div>												
											</div>
											<div class="col-lg-6">
												<label class="control-label text-lg-left pt-2">Cliente</label>
												<div>
													<select data-plugin-selectTwo class="form-control populate mb-3" name="cli_id">												
														<option value="">Selecione...</option>
														<?php foreach ($client->listClients() as $cli) { ?>
															<option value="<?=$cli['cli_id']?>"><?=$cli['cli_name']?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Local de instalação</label>
												<div>
													<select data-plugin-selectTwo class="form-control populate mb-3" name="pnt_id">	
														<option value="">Selecione...</option>
														<?php foreach ($player->listPlayers() as $pl) { ?>
															<option value="<?=$pl['pnt_id']?>"><?=$pl['pnt_name']?></option>
														<?php } ?>
													</select>													
												</div>
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Prioridade</label>
												<div>
													<select class="form-control mb-3" name="pri_id">	
														<option value="">Selecione...</option>
														<?php foreach ($client->listPriorities() as $r) { ?>
															<option value="<?=$r['pri_id']?>"><?=$r['pri_name']?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Status</label>
												<div>
													<select class="form-control mb-3" name="sta_id">
														<option value="">Selecione...</option>
														<?php foreach ($ticket->listStatus() as $r) { ?>
															<option value="<?=$r['sta_id']?>"><?=$r['sta_name']?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Atribuído para</label>
												<select name="usr_id" class="form-control mb-3">                       
												  <option value="">Selecione...</option>
												  <?php foreach ($user->listUsers(TECNICO) as $u) { ?>
													<option value="<?=$u['usr_id']?>"><?=$u['usr_name']?></option>
												  <?php } ?>
												</select>
											</div>
											<div class="col-lg-4">							
												<label class="control-label text-lg-left pt-2">Natureza do Chamado</label>
												<select name="ntr_id" class="form-control mb-3">                       
												  <option value="">Selecione...</option>
												  <?php foreach ($ticket->listTicketType() as $r) { ?>
													<option value="<?=$r['ntr_id']?>"><?=$r['ntr_name']?></option>
												  <?php } ?>
												</select>
											</div>
											<div class="col-lg-4">							
												&nbsp;
											</div>
										</div>											
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="button" onclick="window.location='tickets.add.php'" name="add" class="btn btn-primary">Adicionar</button>	
											<button type="submit" name="search" class="btn btn-primary">Pesquisar</button>
										</div>
									</div>
								</footer>
								</form>															
							</section>
						</div>
					</div>
					<?php if (count($tickets) > 0) { ?>
					<!-- start: resultados da busca -->
					<div class="row">
						<div class="col">
							<section class="card">
								<header class="card-header">
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										<a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
									</div>
									<h2 class="card-title">Resultado da Pesquisa</h2>
								</header>
								<div class="card-body">
									<table class="table table-responsive-lg table-bordered table-striped table-sm mb-0">
										<thead>
											<tr>
												<th>Num. Chamado</th>
												<th>Cliente</th>
												<th>Ponto</th>
												<th>Data de abertura</th>
												<th>Status</th>
												<th>Aberto por</th>
												<th>Atribuído para</th>
												<th>Ações</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($tickets as $key) { ?>			
											<tr>
												<td><a href="tickets.edit.php?id=<?=$key['tkt_id']?>"><?=$key['tkt_sku']?></a></td>
												<td><?=$key['cli_name']?></td>
												<td><?=$key['pnt_name']?>&nbsp;(<?=$key['pnt_state']?>)</td>
												<td><?=formataData($key['tkt_dt_open'])?></td>
												<td><?=$key['sta_name']?></td>
												<td><?=$key['usuario_criador']?></td>
												<td><?=$key['usuario_atribuido']?></td>
												<td>
													<a href="tickets.edit.php?id=<?=$key['tkt_id']?>"><i class="fa fa-edit" aria-hidden="true"></i></a>								
													|<a href="tickets.view.php?id=<?=$key['tkt_id']?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
												</td>													
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<form method="POST">
									<footer class="card-footer">
										<div class="row">
											<div class="col-sm-9">
												<button type="submit" name="excel" class="btn btn-primary">Exportar para Excel</button>
											</div>
										</div>
									</footer>
								</form>
							</section>
						</div>
					</div>
					<!-- end: resultados da busca -->					
					<?php } ?>
					<!-- end: page -->
				</section>
			</div>			
		</section>
		<!-- Modal Animation -->
		<div id="modalAnim" class="zoom-anim-dialog modal-block modal-block-primary mfp-hide">
			<section class="card">
				<header class="card-header">
					<h2 class="card-title">Remover Registro</h2>
				</header>
				<div class="card-body">
					<div class="modal-wrapper">
						<div class="modal-icon">
							<i class="fa fa-question-circle"></i>
						</div>
						<div class="modal-text">
							<p class="mb-0">Confirma remoção do equipamento cadastrado?</p>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-default modal-dismiss">Fechar</button>
						</div>
					</div>
				</footer>
			</section>
		</div>
		<!-- start: footer -->
		<?php include '../footer.php'; ?>
		<!-- end: footer -->		
	</body>	
</html>