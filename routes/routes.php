<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Registro de Quilometragem');
?>
<?php 
	
	if (isset($_POST['excel'])) {
		$filename = "registro_" . time() . ".csv";
		$delimiter = ';';
		$fields = array('Funcionário', 'Data', 'Origem', 'Destino', 'Km');
		$f = fopen('php://memory', 'w');
		fputcsv($f, $fields, $delimiter);
		foreach ($_SESSION['routes'] as $row) {
			 $lineData = array($row['usr_name'],formataData($row['dia']),$row['origem'],$row['destino'],formataMoedaView($row['reg_km']));
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
	  	
	  	$begin  = formataDataMySQL($_POST['begin']);
	  	$end    = formataDataMySQL($_POST['end']);
	  	$status = $_POST['sta_id'];
	  	
	  	if (in_array($_SESSION['user']['grp_id'], array(GERENTE))) {
			$user_id = $_POST['usr_id'];
	    } else {
	    	$user_id = $_SESSION['user']['id'];
	    }

	    if(!isset($error)) {
	    	$routes = [];
	    	$routes = $route->searchRoutes($user_id,$begin,$end,$status);
	    	//limpar o resultado da pesquisa anterior, caso exista
	    	unset($_SESSION['search']);
	    	if (count($routes) <= 0) {
	    		$error[]   = 'Não há resultados disponíveis';
	    	} else {	    		
	    		$_SESSION['routes'] = $routes; 
	    		$_SESSION['search']['user_id'] = $user_id;
	    		$_SESSION['search']['begin']   = $begin;
	    		$_SESSION['search']['end']     = $end;
	    	}	    	
		}	
	}
	
	if (isset($_SESSION['search'])) {
		$routes = $route->searchRoutes($_SESSION['search']['user_id'],
							 $_SESSION['search']['begin'],
							 $_SESSION['search']['end']);
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
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2">Date</label>
											<div class="col-lg-6">
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</span>
													<input id="begin" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="begin">
													<span class="input-group-addon">até</span>
													<input id="end" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="end">
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2">Status</label>
											<div class="col-lg-6">
												<select class="form-control mb-3" name="sta_id">
													<option value="">Todos</option>
													<option value="1">Pendente</option>
													<option value="2">Aprovado</option>
													<option value="3">Rejeitado</option>				
												</select>		
											</div>
										</div>
										<?php if (in_array($_SESSION['user']['grp_id'], array(GERENTE))) { ?>
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2">Usuário</label>
											<div class="col-lg-6">
												<select class="form-control mb-3" name="usr_id">
													<option value="">Todos</option>
													<?php foreach ($user->listUsers(TECNICO) as $u) { ?>
														<option value="<?=$u['usr_id']?>"><?=$u['usr_name']?></option>
													<?php } ?>
												</select>		
											</div>
										</div>										
										<?php } else { echo "<br><br>"; } ?>
										<footer class="card-footer">
											<div class="row">
												<div class="col-sm-9">
													<button type="button" onclick="window.location='routes.add.php'" name="add" class="btn btn-primary">Adicionar</button>	<button type="submit" name="search" class="btn btn-primary">Pesquisar</button>
												</div>
											</div>
										</footer>
									</form>									
								</div>								
							</section>
						</div>
					</div>
					<?php if (count($routes) > 0) { ?>
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
												<th>Funcionário</th>
												<th>Data</th>
												<th>Origem</th>
												<th>Destino</th>
												<th>Km</th>
												<th>Status</th>
												<th>Ações</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($routes as $key) { ?>			
											<tr>
												<td><?=$key['usr_name']?></td>
												<td><?=formataData($key['dia'])?></td>
												<td><?=$key['origem']?></td>
												<td><?=$key['destino']?></td>
												<td><?=formataMoedaView($key['reg_km'])?></td>
												<td><?=$key['sta_name']?></td>
												<?php if($key['sta_id'] == CHAMADO_REPROVADO) { ?>
												<td>
													<button type="button" class="mb-1 mt-1 mr-1 btn btn-xs btn-primary modal-with-zoom-anim ws-normal" href="#modalAnim" onclick="copyValue_<?=$key['reg_id']?>()">Ver motivo</button>
												</td>
												<script type="text/javascript">
													function copyValue_<?=$key['reg_id']?>() {
														document.getElementById("reg_justif").innerHTML = '<?=$key['reg_justif']?>';
													}
												</script>
												<?php } else if ($key['sta_id'] == CHAMADO_APROVADO) {  ?>
												<td>&nbsp;</td>
												<?php } else { ?>					
												<td>
													<a href="routes.edit.php?id=<?=$key['reg_id']?>"><i class="fa fa-edit" aria-hidden="true"></i></a>|
													<a href="routes.delete.php?id=<?=$key['reg_id']?>" onclick="return confirm('Confirma remoção do registro?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
												</td>		
												<?php } ?>	
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
					<h2 class="card-title">Motivo da Reprovação</h2>
				</header>
				<div class="card-body">
					<div class="modal-wrapper">
						<div class="modal-icon">
							<i class="fa fa-exclamation-circle"></i>
						</div>
						<div class="modal-text">
							<p class="mb-0"><span id="reg_justif"></span></p>
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