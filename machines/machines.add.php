<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Cadastro de Equipamentos');    
?>
<?php
  if (!empty($_POST)) {

  	unset($_SESSION['success']);
    $error = [];
    $success = [];

    $equipamento = new ArrayObject();  

    $equipamento->type           = $_POST['typ_id'];
	$equipamento->player         = $_POST['pnt_id'];
	$equipamento->sku            = $_POST['mch_sku'];
	$equipamento->active         = $_POST['mch_active'];
	$equipamento->contentManager = $_POST['mch_cm_name'];
	$equipamento->teamViewer     = $_POST['mch_tv_name'];
	$equipamento->config         = $_POST['mch_config'];

	if($machine->addMachine($equipamento)) {             
	    $_SESSION['success'] = 'Equipamento adicionado com sucesso';
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
									<p class="card-subtitle">
										Cadastro dos equipamentos associados aos pontos.
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
										<div class="form-group row">
											<div class="col-lg-4">							
												<label class="col-lg-6 control-label text-lg-left pt-2">Local de instalação</label>
												<div>
													<select data-plugin-selectTwo class="form-control populate" name="pnt_id">
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
											<div class="col-lg-4">							
												<label class="col-lg-4 control-label text-lg-left pt-2">Situação</label>
												<div>
													<select class="form-control mb-3" name="mch_active">
														<option value="1" selected>Ativo</option>
														<option value="0" selected>Inativo</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-4 control-label text-lg-left pt-2">Tipo</label>
												<div>
													<select class="form-control mb-3" name="typ_id">
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
												<input type="text" name="mch_sku" class="form-control" id="inputDefault" required="true">
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-7 control-label text-lg-left pt-2">Content Manager</label>
												<input type="text" name="mch_cm_name" class="form-control" id="inputDefault">
											</div>
											<div class="col-lg-4">							
												<label class="col-lg-5 control-label text-lg-left pt-2">Team Viewer</label>
												<input type="text" name="mch_tv_name" class="form-control" id="inputDefault">
											</div>
										</div>	
										<div class="form-group">
											<label class="col-lg-3 control-label text-lg-left pt-2" for="textareaDefault">Configuração</label>
											<div class="col-lg-12">
												<textarea class="form-control" rows="3" id="textareaDefault" name="mch_config"></textarea>
											</div>
										</div>																		
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="add" class="btn btn-primary">Salvar</button>
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