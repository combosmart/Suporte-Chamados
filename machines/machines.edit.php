<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Cadastro de Equipamentos - Alteração de dados');    
?>
<?php

	$id = $_GET['id'];
	$obj = $machine->getMachine($id);

	if (empty($obj)) { header('Location: machines.php');}

	if (isset($_POST['edit'])) {

	  	unset($_SESSION['success']);

		$equipamento = new ArrayObject();  

	    $equipamento->type           = $_POST['typ_id'];
		$equipamento->player         = $_POST['pnt_id'];
		$equipamento->sku            = $_POST['mch_sku'];
		$equipamento->active         = $_POST['mch_active'];
		$equipamento->contentManager = $_POST['mch_cm_name'];
		$equipamento->teamViewer     = $_POST['mch_tv_name'];
		$equipamento->config         = $_POST['mch_config'];
		$equipamento->id             = $_POST['mch_id'];

		//var_dump($equipamento); die;

		if($machine->machineUpdate($equipamento)) {             
		    $_SESSION['success'] = 'Dados do equipamento alterados com sucesso';	
		    header('Location: machines.php');
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
								<div class="card-body">
									<?php 
									if (isset($_SESSION['success'])) {
			          					echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
			          					unset($_SESSION['success']);
			          				} ?>
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
												<label class="col-lg-6 control-label text-lg-left pt-2">Local de instalação</label>
												<div>
													<select data-plugin-selectTwo class="form-control populate" name="pnt_id">
														<?php foreach ($client->listClients() as $cli) { ?>
														<optgroup label="<?=$cli['cli_name']?>">
															<?php foreach ($player->listPlayers($cli['cli_id']) as $pl) { ?>
																<option value="<?=$pl['pnt_id']?>" <?php if ($pl['pnt_id'] == $obj['pnt_id']) { echo 'selected'; } ?> ><?=$pl['pnt_name']?></option>
															<?php } ?>
														</optgroup>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-4 control-label text-lg-left pt-2">Situação</label>
												<div>
													<select class="form-control mb-3" name="mch_active">
														<option value="1" <?php if ($obj['mch_active'] == 1) { echo 'selected'; } ?>>Ativo</option>
														<option value="0" <?php if ($obj['mch_active'] == 0) { echo 'selected'; } ?>>Inativo</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-4 control-label text-lg-left pt-2">Tipo</label>
												<div>
													<select class="form-control mb-3" name="typ_id">
														<?php foreach ($machine->listTypes() as $tp) { ?>
															<option value="<?=$tp['typ_id']?>" <?php if ($tp['typ_id'] == $obj['typ_id']) { echo 'selected'; } ?> ><?=$tp['typ_name']?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-4">							
												<label class="col-lg-5 control-label text-lg-left pt-2">Patrimônio</label>
												<input type="text" name="mch_sku" class="form-control" id="inputDefault" required="true" value="<?=$obj['mch_sku']?>">
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-7 control-label text-lg-left pt-2">Content Manager</label>
												<input type="text" name="mch_cm_name" class="form-control" id="inputDefault" value="<?=$obj['mch_cm_name']?>">
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-5 control-label text-lg-left pt-2">Team Viewer</label>
												<input type="text" name="mch_tv_name" class="form-control" id="inputDefault" value="<?=$obj['mch_tv_name']?>">
											</div>
										</div>	
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2" for="textareaDefault">Configuração</label>
											<div class="col-lg-12">
												<textarea class="form-control" rows="3" id="textareaDefault" name="mch_config"><?=$obj['mch_config']?></textarea>
											</div>
											<input type="hidden" name="mch_id" value="<?=$obj['mch_id']?>">
										</div>																		
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="edit" class="btn btn-primary">Salvar</button>
											<button type="button" onclick="window.location='machines.php'" class="btn btn-primary">Voltar</button>
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