<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Quilometragem - Adição de Percurso');    
?>
<?php
  if (!empty($_POST)) {

  	unset($_SESSION['success']);
    $error = [];
    $success = [];

    if (empty($_POST['reg_data'])) {
    	$error[] = 'Preencha a data';
    }

    if (!validaData($_POST['reg_data'])) {
		$error[] = 'Data inválida';
	}

	$reg_km = str_replace(',','.',$_POST['reg_km']);
	if (!is_numeric($reg_km)) {
		$error[] = 'Km informada inválida';	
	}

	$percurso = new ArrayObject();  
    $percurso->data    = formataDataMySQL($_POST['reg_data']);
    $percurso->origem  = $_POST['pnt_id_de'];
    $percurso->destino = $_POST['pnt_id_para'];
    $percurso->ordem   = $_POST['reg_order'];
    $percurso->km      = $reg_km;
    $percurso->obs     = $_POST['reg_obs'];   

    if (in_array($_SESSION['user']['grp_id'], array(GERENTE))) {
		$percurso->user = $_POST['usr_id'];
    } else {
    	$percurso->user = $_SESSION['user']['id'];
    }

    if($route->checkOrder($percurso)) {
      $error[]   = 'Já existe um percurso para esta data nesta posição';
    } else {
    	if(empty($error)) {
	      if($route->addRoute($percurso)) {             
	        $_SESSION['success'] = 'Percurso adicionado com sucesso';
	        header('Location: routes.php');
	      } 
		}  
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
										echo "<ul>";
										foreach ($error as $e) {
											echo '<li>'. $e . '</li>';
										}
										echo "</ul></div>";
					                } ?>
									<form class="form-bordered" method="post">
										<?php if (in_array($_SESSION['user']['grp_id'], array(GERENTE))) { ?>
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2">Usuário</label>
											<div class="col-lg-6">
												<select class="form-control mb-3" name="usr_id">
													<?php foreach ($user->listUsers(TECNICO) as $u) { ?>
														<option value="<?=$u['usr_id']?>"><?=$u['usr_name']?></option>
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
													<input id="date" data-plugin-masked-input data-input-mask="99/99/9999" placeholder="__/__/____" class="form-control" name="reg_data">
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
																<option value="<?=$pl['pnt_id']?>"><?=$pl['pnt_name']?></option>
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
																<option value="<?=$pl['pnt_id']?>"><?=$pl['pnt_name']?></option>
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
															<option value="<?=$i?>"><?=$i?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-lg-7">							
												<label class="col-lg-4 control-label text-lg-left pt-2">Distância (Km)</label>
												<div class="col-lg-3">
													<input type="text" name="reg_km" id="reg_km" class="form-control" id="inputDefault" required="true" onblur="decimalBr()">													
												</div>												
											</div>
											<script>
											function decimalBr() {
											    var str = document.getElementById("reg_km").value; 											    
    											var res = str.replace(".", ",");
    											document.getElementById("reg_km").value = res;
											}
											</script>
										</div>	
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2" for="textareaDefault">Observações</label>
											<div class="col-lg-12">
												<textarea class="form-control" rows="3" id="textareaDefault" name="reg_obs"></textarea>
											</div>
										</div>																		
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="add" class="btn btn-primary">Salvar</button>
											<button type="button" onclick="window.location='routes.php'" class="btn btn-primary">Voltar</button>
										</div>
									</div>
								</footer>
								</form>
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