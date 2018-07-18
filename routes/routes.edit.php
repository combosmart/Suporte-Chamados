<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  if (in_array($_SESSION['user']['grp_id'], array(GERENTE))) {
  	define('sitetile','Quilometragem - Aprovação de Percurso');      	
  } else {
  	define('sitetile','Quilometragem - Edição de Percurso');    	
  } 
?>
<?php
  
	$id = $_GET['id'];
	$obj = $route->getRoute($id);

	if (empty($obj)) { header('Location: routes.php');}

  	if (isset($_POST['edit'])) {
	    
	    unset($_SESSION['success']);
	    $error = [];
	    $success = [];

	    if (empty($_POST['reg_data'])) {
	    	$error[] = 'Preencha a data';
	    }

	    if (!validaData($_POST['reg_data'])) {
			$error[] = 'Data inválida';
		}


	    $percurso = new ArrayObject();  
	    $percurso->data    = formataDataMySQL($_POST['reg_data']);
	    $percurso->origem  = $_POST['pnt_id_de'];
	    $percurso->destino = $_POST['pnt_id_para'];
	    $percurso->ordem   = $_POST['reg_order'];
	    $percurso->km      = formataMoedaMysql($_POST['reg_km']);
	    $percurso->obs     = $_POST['reg_obs'];
	    $percurso->user    = $_POST['usr_id'];
	    $percurso->id      = $_POST['reg_id'];

	    if($route->checkOrder($percurso,"edit")) {
	      $error[]   = 'Já existe um percurso para esta data nesta posição';
	    } else {
	    	if(empty($error)) {
		      if($route->routeUpdate($percurso)) {             
		        $_SESSION['success'] = 'Percurso alterado com sucesso';
		        header('Location: routes.php');
		      } 
			}  
	    }         
	}

	if (isset($_POST['aprovar'])) {		
		$reg_id = $_POST['reg_id'];
		if ($route->routeApproval($reg_id)) {
			$_SESSION['success'] = 'Percurso aprovado com sucesso';
			header('Location: routes.php');
		}
	}

	if (isset($_POST['reprovar'])) {			
		$reg_id     = $_POST['reg_id'];
		$reg_justif = $_POST['reg_justif'];
		if ($route->routeRejection($reg_id,$reg_justif)) {
			$_SESSION['success'] = 'Percurso reprovado com sucesso';
			header('Location: routes.php');
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
									<p class="card-subtitle">
										Preenchimento da quilometragem por percurso. Atenção para o campo "ordem", que deve ser respeitado segundo a ordenação do percurso diário.
									</p>
								</header>								
								<div class="card-body">
									<?php if(isset($error)) { 
										echo "<div class='alert alert-danger'>";
										echo "<strong>Erro: </strong>";
										foreach ($error as $e) {
											echo $e;
										}
										echo "</div>";
					                } ?>
									<form class="form-bordered" method="post">
										<?php if (in_array($_SESSION['user']['grp_id'], array(GERENTE))) { ?>
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2">Usuário</label>
											<div class="col-lg-6">
												<select class="form-control mb-3" name="usr_id">
													<?php foreach ($user->listUsers(TECNICO) as $u) { ?>
														<option value="<?=$u['usr_id']?>" <?php if ($obj['usr_id'] == $u['usr_id']) { echo 'selected'; } ?> ><?=$u['usr_name']?></option>
													<?php } ?>
												</select>		
											</div>
										</div>										
										<?php } ?>
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2">Data</label>
											<div class="col-lg-6">
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</span>
													<input id="date" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="reg_data" value="<?=formataData($obj['dia'])?>">
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-6">							
												<label class="col-lg-3 control-label text-lg-left pt-2">Origem</label>
												<div>
													<select data-plugin-selectTwo class="form-control populate" name="pnt_id_de">
														<?php foreach ($client->listClients() as $cli) { ?>
														<optgroup label="<?=$cli['cli_name']?>">
															<?php foreach ($player->listPlayers($cli['cli_id']) as $pl) { ?>
																<option value="<?=$pl['pnt_id']?>" <?php if ($pl['pnt_id']==$obj['pnt_id_de']) {echo 'selected';} ?> ><?=$pl['pnt_name']?></option>
															<?php } ?>
														</optgroup>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-lg-6">							
												<label class="col-lg-3 control-label text-lg-left pt-2">Destino</label>
												<div>
													<select data-plugin-selectTwo class="form-control populate" name="pnt_id_para">
														<?php foreach ($client->listClients() as $cli) { ?>
														<optgroup label="<?=$cli['cli_name']?>">
															<?php foreach ($player->listPlayers($cli['cli_id']) as $pl) { ?>
																<option value="<?=$pl['pnt_id']?>" <?php if ($pl['pnt_id']==$obj['pnt_id_para']) {echo 'selected';} ?> ><?=$pl['pnt_name']?></option>
															<?php } ?>
														</optgroup>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-5">							
												<label class="col-lg-6 control-label text-lg-left pt-2">Ordem do Percurso</label>
												<div class="col-lg-3">
													<select class="form-control mb-3" name="reg_order">
														<?php for ($i=1; $i < 11 ; $i++) { ?>
															<option value="<?=$i?>" <?php if ($i==$obj['reg_order']) {echo 'selected';} ?> ><?=$i?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-lg-7">							
												<label class="col-lg-4 control-label text-lg-left pt-2">Distância (Km)</label>
												<div class="col-lg-3">
													<input type="number" name="reg_km" class="form-control" id="inputDefault" required="true" value="<?=$obj['reg_km']?>">													
												</div>												
											</div>
										</div>	
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2" for="textareaDefault">Observações</label>
											<div class="col-lg-12">
												<textarea class="form-control" rows="3" id="textareaDefault" name="reg_obs"><?=$obj['reg_obs']?></textarea>
											</div>
										</div>																		
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="edit" class="btn btn-primary">Salvar</button>
											<?php if (in_array($_SESSION['user']['grp_id'], array(GERENTE))) { ?>
											<button type="submit" name="aprovar" class="btn btn-primary">Aprovar</button>
											<a class="mb-1 mt-1 mr-1 modal-with-zoom-anim ws-normal btn btn-primary" onclick="myFunction()" href="#modalAnim">Reprovar</a>
											<script>
												function myFunction(){
													var str = "Meu teste";
													document.getElementById("teste").innerHTML = str;
												}
											</script>
											<?php } ?>
											<button type="button" onclick="window.location='routes.php'" class="btn btn-primary">Voltar</button>
										</div>
									</div>
								</footer>
								<input type="hidden" name="reg_id" value="<?=$obj['reg_id']?>">
								<input type="hidden" name="usr_id" value="<?=$obj['usr_id']?>">
								</form>
							</section>
						</div>
					</div>
					<!-- end: page -->
				</section>
			</div>			
		</section>
		<!-- Modal Animation -->
		<div id="modalAnim" class="modal-block modal-block-primary mfp-hide">
			<section class="card">
				<header class="card-header">
					<h2 class="card-title">Jusiticativa da Reprovação</h2>
				</header>
				<form method="post">
				<div class="card-body">			
						<div class="form-group">
							<div class="col-lg-12">
								<textarea class="form-control" rows="3" id="textareaDefault" name="reg_justif"></textarea>
							</div>
						</div>		
						<input type="hidden" name="reg_id" value="<?=$obj['reg_id']?>">						
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button name="reprovar" type="submit" class="btn btn-primary">Reprovar</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
				</form>
			</section>
		</div>
		<!-- start: footer -->
		<?php include '../footer.php'; ?>
		<!-- end: footer -->
	</body>
</html>