<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Administração de Pontos');
?>
<?php 

	if (isset($_POST['excel'])) {
		$filename = "registro_" . time() . ".csv";
		$delimiter = ';';
		$fields = array('Cliente', 'Nome', 'Cidade', 'Status', 'Cadastrado por', 'Data');
		$f = fopen('php://memory', 'w');
		fputcsv($f, $fields, $delimiter);
		foreach ($_SESSION['players']  as $row) {
			 $lineData = array(
			 	$row['cli_name'],
			 	$row['pnt_name'],
			 	$row['pnt_city'] . "(" . $row['pnt_state'] . ")",			 	
			 	label_active_txt($row['cli_active']),
			 	$row['usr_name'],
			 	$row['pnt_creation_date']
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
		
		$filter = new ArrayObject();  	
	    
	    $filter->client = $_POST['client'];
		$filter->name   = $_POST['name'];
		$filter->city   = $_POST['city'];
		$filter->state  = $_POST['state'];
		$filter->active = $_POST['pnt_active'];

	  	$players = [];
    	$players = $player->searchPlayers($filter);

		//limpar o resultado da pesquisa anterior, caso exista
		unset($_SESSION['players']);
		if (count($players) <= 0) {
			$error[]   = 'Não há resultados disponíveis';
		} else {	    		
			$_SESSION['filter']['players'] = $filter; 
			$_SESSION['players'] = $players;
		}	    	

	}
	
	if (isset($_SESSION['filter']['players'])) {
		$players = $player->searchPlayers($_SESSION['filter']['players']);
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
											<div class="col-lg-5">
												<label class="control-label text-lg-left pt-2">Cliente</label>
												<select data-plugin-selectTwo class="form-control populate mb-3" name="client">												
													<option value="">Todos</option>
												  <?php foreach ($client->listClients() as $c) { ?>
												    <option value="<?=$c['cli_id']?>"><?=$c['cli_name']?></option>
												  <?php } ?>                    
												</select>
											</div>
											<div class="col-lg-7">
												<label class="control-label text-lg-left pt-2">Nome</label>
                        						<input type="text" class="form-control" name="name">
											</div>
										</div>										
										<div class="form-group row">
											<div class="col-lg-6">
												<label class="control-label text-lg-left pt-2">Cidade</label>
                        						<input type="text" class="form-control" name="city">
											</div>
											<div class="col-lg-3">
												<label class="control-label text-lg-left pt-2">Estado</label>
                        						<select name="state" class="form-control mb-3" id="uf">
                        						  <option value=''>Todos</option>
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
											<div class="col-lg-3">
												<label class="control-label text-lg-left pt-2">Status</label>
												<select class="form-control mb-3" name="pnt_active">
													<option value="">Todos</option>
													<option value="1">Ativo</option>
													<option value="0">Inativo</option>												
												</select>		
											</div>							
										</div>
										<footer class="card-footer">
											<div class="row">
												<div>
													<button type="button" onclick="window.location='players.add.php'" name="add" class="btn btn-primary">Adicionar</button>	<button type="submit" name="search" class="btn btn-primary">Pesquisar</button>
												</div>
											</div>
										</footer>
									</form>									
								</div>								
							</section>
						</div>
					</div>
					<?php if (count($players) > 0) { ?>
					<!-- start: resultados da busca -->
					<div class="row">
						<div class="col">
							<section class="card">
								<header class="card-header">
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										<a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
									</div>
									<h2 class="card-title">Resultado da Pesquisa - <?=count($players)?></h2>
								</header>
								<div class="card-body">
									<table class="table table-responsive-lg table-bordered table-striped table-sm mb-0">
										<thead>
											<tr>
												<th>Nome</th>
								                <th>Cliente</th>
								                <th>Cidade</th>                
								                <th>Criado por</th>
								                <th>Criado em</th>
								                <th>Status</th>
								                <th>Ações</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($players as $row) { ?>			
											<tr>
											  	<td><?=$row['pnt_name']?></td>
							                  	<td><?=$row['cli_name']?></td>
							                  	<td><?=$row['pnt_city']?> (<?=$row['pnt_state']?>)</td>
							                  	<td><?=$row['usr_name']?></td>
							                  	<td><?=$row['pnt_creation_date']?></td>
							                  	<td><?=label_active($row['pnt_active'])?></td>                                    
							                  	<td><a href="players.edit.php?id=<?=$row['pnt_id']?>"><i class="fa fa-fw fa-edit"></i></a>|<a href="players.view.php?id=<?=$row['pnt_id']?>"><i class="fa fa-fw fa-eye"></i></a></td>
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