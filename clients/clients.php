<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Administração de Clientes');
?>
<?php 
	
	if (isset($_POST['excel'])) {
		$filename = "registro_" . time() . ".csv";
		$delimiter = ';';
		$fields = array('Cliente', 
						'UF', 
						'CNPJ', 
						'Cliente Elemidia', 
						'Status', 
						'Endereço', 
						'Numero', 
						'Bairro', 
						'Cidade', 
						'Estado', 
						'CEP', 
						'Cadastrado por', 
						'Data');
		
		$f = fopen('php://memory', 'w');
		fputcsv($f, $fields, $delimiter);
		
		foreach ($_SESSION['clients']  as $row) {
			 $lineData = array(
			 	$row['cli_name'],
			 	$row['cli_state'],
			 	mask($row['cli_cnpj'],'##.###.###/####-##'),
			 	label_yesno_txt($row['cli_flg_elemidia']),
			 	label_active_txt($row['cli_active']),
			 	$row['cli_address'],
			 	$row['cli_number'],
			 	$row['cli_neighbor'],
			 	$row['cli_city'],
			 	$row['cli_state'],
			 	$row['cli_zip'],
			 	$row['usr_name'],
			 	$row['dia']
			 );
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
	    $filter->active       = $_POST['cli_active'];	
		$filter->dataInicio	  = formataDataMySQL($_POST['begin']);
		$filter->dataFim	  = formataDataMySQL($_POST['end']);
		$filter->cli_name     = $_POST['name'];			

	  	$clientes = $client->searchClients($filter);

	  	unset($_SESSION['filter']['clients']);
		if (count($clientes) <= 0) {
			$error[]   = 'Não há resultados disponíveis';
		} else {	    		
			$_SESSION['filter']['clients'] = $filter; 
			$_SESSION['clients'] = $clientes;
		}	    	

	}
	
	if (isset($_SESSION['filter']['clients'])) {		
		$clientes = $client->searchClients($_SESSION['filter']['clients']);
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
									<form class="form-bordered" method="post" role="form">
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
											<div class="col-lg-3">
												<label class="col-lg-5 control-label text-lg-left pt-2">Status</label>
												<select class="form-control mb-3" name="cli_active">
													<option value="">Todos</option>
													<option value="1">Ativo</option>
													<option value="0">Inativo</option>												
												</select>		
											</div>
											<div class="col-lg-3">
												<label class="col-lg-4 control-label text-lg-left pt-2">Nome</label>	
												<input type="text" class="form-control" name="name">		
											</div>
										</div>
										<footer class="card-footer">
											<div class="row">
												<div class="col-sm-9">
													<button type="button" onclick="window.location='clients.add.php'" name="add" class="btn btn-primary">Adicionar</button>	<button type="submit" name="search" class="btn btn-primary">Pesquisar</button>
												</div>
											</div>
										</footer>
									</form>									
								</div>								
							</section>
						</div>
					</div>
					<?php if (count($clientes) > 0) { ?>
					<!-- start: resultados da busca -->
					<div class="row">
						<div class="col">
							<section class="card">
								<header class="card-header">
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										<a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
									</div>
									<h2 class="card-title">Resultado da Pesquisa  - <?=count($clientes)?></h2>
								</header>
								<div class="card-body">
									<table class="table table-responsive-lg table-bordered table-striped table-sm mb-0">
										<thead>
											<tr>
												<th>Cliente</th>
												<th>CNPJ</th>
												<th>Status</th>
												<th>Cadastrado por</th>
												<th>Data</th>
												<th>Ações</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($clientes as $key) { ?>			
											<tr>
												<td><?=$key['cli_name']?>&nbsp;(<?=$key['cli_state']?>)</td>
												<td><?=mask($key['cli_cnpj'],'##.###.###/####-##')?></td>
												<td><?=label_active_txt($key['cli_active'])?></td>									
												<td><?=$key['usr_name']?></td>
												<td><?=$key['dia']?></td>
												<td>
													<a href="clients.edit.php?id=<?=$key['cli_id']?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
													<!--
													<a href="#modalAnim" class="mb-1 mt-1 mr-1 modal-with-zoom-anim ws-normal" ><i class="fa fa-trash" aria-hidden="true"></i></a>
													-->
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