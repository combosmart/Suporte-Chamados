<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Pesquisa de Equipamentos');
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
			 				   $row['pnt_name'],
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

	    $filter->type        = $_POST['typ_id'];
		$filter->player      = $_POST['pnt_id'];
		$filter->sku         = $_POST['mch_sku'];
		$filter->active      = $_POST['mch_active'];	
		$filter->dataInicio	 = formataDataMySQL($_POST['begin']);
		$filter->dataFim	 = formataDataMySQL($_POST['end']);
		$filter->cliente     = $_POST['cli_id'];	
		$filter->state       = $_POST['pnt_state'];

	    if(!isset($error)) {
	    	$machines = [];
	    	$machines = $machine->searchMachines($filter);
	    	//limpar o resultado da pesquisa anterior, caso exista
	    	unset($_SESSION['machines']);
	    	if (count($machines) <= 0) {
	    		$error[]   = 'Não há resultados disponíveis';
	    	} else {	    		
	    		$_SESSION['filter']['machines'] = $filter; 
	    		$_SESSION['machines'] = $machines;
	    	}	    	
		}	
	}
	
	if (isset($_SESSION['filter']['machines'])) {
		$machines = $machine->searchMachines($_SESSION['filter']['machines']);
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
												<label class="col-lg-3 control-label text-lg-left pt-2">Data</label>												
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
												<label class="col-lg-3 control-label text-lg-left pt-2">Cliente</label>
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
												<label class="col-lg-6 control-label text-lg-left pt-2">Local de instalação</label>
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
												<label class="col-lg-4 control-label text-lg-left pt-2">Situação</label>
												<div>
													<select class="form-control mb-3" name="mch_active">
														<option value="">Selecione...</option>
														<option value="1" selected>Ativo</option>
														<option value="0" selected>Inativo</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-4 control-label text-lg-left pt-2">Tipo</label>
												<div>
													<select class="form-control mb-3" name="typ_id">
														<option value="">Selecione...</option>
														<?php foreach ($machine->listTypes() as $tp) { ?>
															<option value="<?=$tp['typ_id']?>"><?=$tp['typ_name']?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-4">							
												<label class="col-lg-5 control-label text-lg-left pt-2">Patrimônio</label>
												<input type="text" name="mch_sku" class="form-control" id="inputDefault">
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-5 control-label text-lg-left pt-2">Estado</label>
												<select name="pnt_state" class="form-control mb-3" id="uf">                       
												  <option value="">Selecione...</option>
												  <option value="AC">Acre</option>
												  <option value="AL">Alagoas</option>
												  <option value="AP">Amapá</option>
												  <option value="AM">Amazonas</option>
												  <option value="BA">Bahia</option>
												  <option value="CE">Ceará</option>
												  <option value="DF">Distrito Federal</option>
												  <option value="ES">Espírito Santo</option>
												  <option value="GO">Goiás</option>
												  <option value="MA">Maranhão</option>
												  <option value="MT">Mato Grosso</option>
												  <option value="MS">Mato Grosso do Sul</option>
												  <option value="MG">Minas Gerais</option>
												  <option value="PA">Pará</option>
												  <option value="PB">Paraíba</option>
												  <option value="PR">Paraná</option>
												  <option value="PE">Pernambuco</option>
												  <option value="PI">Piauí</option>
												  <option value="RJ">Rio de Janeiro</option>
												  <option value="RN">Rio Grande do Norte</option>
												  <option value="RS">Rio Grande do Sul</option>
												  <option value="RO">Rondônia</option>
												  <option value="RR">Roraima</option>
												  <option value="SC">Santa Catarina</option>
												  <option value="SP">São Paulo</option>
												  <option value="SE">Sergipe</option>
												  <option value="TO">Tocantins</option>
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
											<button type="button" onclick="window.location='machines.add.php'" name="add" class="btn btn-primary">Adicionar</button>	
											<button type="submit" name="search" class="btn btn-primary">Pesquisar</button>
										</div>
									</div>
								</footer>
								</form>															
							</section>
						</div>
					</div>
					<?php if (count($machines) > 0) { ?>
					<!-- start: resultados da busca -->
					<div class="row">
						<div class="col">
							<section class="card">
								<header class="card-header">
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										<a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
									</div>
									<h2 class="card-title">Resultado da Pesquisa - <?=count($machines)?></h2>
								</header>
								<div class="card-body">
									<table class="table table-responsive-lg table-bordered table-striped table-sm mb-0">
										<thead>
											<tr>
												<th>Cliente</th>
												<th>Ponto</th>
												<th>Tipo</th>
												<th>Patrimônio</th>
												<th>Status</th>
												<th>Cadastrado por</th>
												<th>Data</th>
												<th>Ações</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($machines as $key) { ?>			
											<tr>
												<td><?=$key['cli_name']?></td>
												<td><?=$key['pnt_name']?>&nbsp;(<?=$key['pnt_state']?>)</td>
												<td><?=$key['typ_name']?></td>
												<td><?=$key['mch_sku']?></td>
												<td><?=label_active_txt($key['mch_active'])?></td>
												<td><?=$key['usr_name']?></td>
												<td><?=$key['dia']?></td>
												<td>
													<a href="machines.edit.php?id=<?=$key['mch_id']?>"><i class="fa fa-edit" aria-hidden="true"></i></a>|
													<a href="#modalAnim" class="mb-1 mt-1 mr-1 modal-with-zoom-anim ws-normal" ><i class="fa fa-trash" aria-hidden="true"></i></a>
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