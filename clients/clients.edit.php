<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Alteração de Dados do Cliente');
?>
<?php include '../header.php'; ?>
<?php

  $id     = $_GET['id'];
  $obj    = $client->getClient($id);    	

  if (!empty($_POST)) {

    unset($_SESSION['success']);
    $error = [];
    $success = [];

    $cliente = new ArrayObject();  
    $cliente->cli_id           = $_POST['cli_id'];
    $cliente->cli_active       = $_POST['cli_active'];
	$cliente->cli_name         = $_POST['cli_name'];
	$cliente->cli_cnpj         = limpaCPF_CNPJ($_POST['cli_cnpj']);
	$cliente->usr_id           = $_SESSION['user']['id'];
	$cliente->pri_id           = $_POST['pri_id'];
	$cliente->cli_address      = $_POST['address'];
	$cliente->cli_number       = $_POST['number'];
	$cliente->cli_neighbor     = $_POST['neighbor'];
	$cliente->cli_city         = $_POST['city'];
	$cliente->cli_state        = $_POST['state'];
	$cliente->cli_zip          = $_POST['zipcode'];
	$cliente->cli_nmfnt        = $_POST['cli_nmfnt'];
    $cliente->cli_flg_elemidia = $_POST['cli_flg_elemidia'];
    
    /*
    if ($client->checkCNPJ($cliente,'update')) {
    	$error[] = 'CNPJ já cadastrado no sistema';
    }
    */

    if(empty($error)) {      
	  if($client->clientUpdate($cliente)) {             
	    $_SESSION['success'] = 'Cliente alterado com sucesso';
	    header('Location: clients.php');    
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
								</header>								
								<div class="card-body">
									<?php if(!empty($error)) { 
										echo "<div class='alert alert-danger'>";
										echo "<ul>";
										foreach ($error as $e) {
											echo '<li>'. $e . '</li>';
										}
										echo "</ul></div>";
					                } ?>
					                <form class="form-bordered" method="post">
					                	<div class="form-group row">
											<div class="col-lg-5">							
												<label class="control-label text-lg-left pt-2">Nome Fantasia</label>
												<input type="text" class="form-control" name="cli_nmfnt" value="<?=$obj['cli_nmfnt']?>">				
											</div>											
											<div class="col-lg-4">
												<label class="col-lg-6 control-label pt-2">Cliente Elemídia?</label>
												<select class="form-control mb-3" name="cli_flg_elemidia">
													<option value="1" <?php if ($obj['cli_flg_elemidia'] == 1) { echo 'selected'; } ?>>Sim</option>
													<option value="0" <?php if ($obj['cli_flg_elemidia'] == 0) { echo 'selected'; } ?> >Não</option>												
												</select>		
											</div>
											<div class="col-lg-3">
												<label class="col-lg-6 control-label text-lg-left pt-2">Situação</label>
												<select name="cli_active" class="form-control mb-3" id="cli_active">
													<option value="1" <?php if ($obj['cli_active'] == 1) { echo 'selected'; } ?>>Ativo</option>
													<option value="0" <?php if ($obj['cli_active'] == 0) { echo 'selected'; } ?> >Inativo</option>
												</select>
											</div>											
										</div>
										<div class="form-group row">
											<div class="col-lg-6">							
												<label class="control-label text-lg-left pt-2">Razão Social</label>
												<input type="text" class="form-control" name="cli_name" required="true" value="<?=$obj['cli_name']?>">				
											</div>											
											<div class="col-lg-3">							
												<label class="control-label text-lg-left pt-2">CNPJ</label>
												<input id="cli_cnpj" type="text" name="cli_cnpj" data-plugin-masked-input data-input-mask="99.999.999/9999-99" placeholder="00.000.000/0001-00" class="form-control" required="true" value="<?=$obj['cli_cnpj']?>">
											</div>
											<div class="col-lg-3">
												<label class="col-lg-6 control-label text-lg-left pt-2">Prioridade</label>
												<select name="pri_id" class="form-control mb-3" id="pri_id">
													<?php foreach ($client->listPriorities() as $k) { ?>
														<option value="<?=$k['pri_id']?>" <?php if ($k['pri_id'] == $obj['pri_id']) { echo 'selected'; } ?>><?=$k['pri_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>											
										<div class="form-group row">
											<div class="col-lg-6">							
												<label>Endereço</label>
                      							<input type="text" class="form-control" name="address" required="true" id="rua" value="<?=$obj['cli_address']?>">
											</div>
											<div class="col-lg-2">
												<label>Número</label>
                      							<input type="text" class="form-control" name="number" required="true" value="<?=$obj['cli_number']?>">
											</div>
											<div class="col-lg-4">
												<label>Bairro</label>
                      							<input type="text" class="form-control" name="neighbor" required="true" id="bairro" value="<?=$obj['cli_neighbor']?>">
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-6">
												<label>Cidade</label>
                      							<input type="text" class="form-control" name="city" required="true" id="cidade" value="<?=$obj['cli_city']?>">
											</div>
											<div class="col-lg-3">
												  <label>Estado</label>
							                      <select name="state" class="form-control" id="uf">                       
							                        <option value="AC" <?php if ($obj['cli_state'] == 'AC') { echo 'selected'; } ?>>Acre</option>
													<option value="AL" <?php if ($obj['cli_state'] == 'AL') { echo 'selected'; } ?>>Alagoas</option>
													<option value="AP" <?php if ($obj['cli_state'] == 'AP') { echo 'selected'; } ?>>Amapá</option>
													<option value="AM" <?php if ($obj['cli_state'] == 'AM') { echo 'selected'; } ?>>Amazonas</option>
													<option value="BA" <?php if ($obj['cli_state'] == 'BA') { echo 'selected'; } ?>>Bahia</option>
													<option value="CE" <?php if ($obj['cli_state'] == 'CE') { echo 'selected'; } ?>>Ceará</option>
													<option value="DF" <?php if ($obj['cli_state'] == 'DF') { echo 'selected'; } ?>>Distrito Federal</option>
													<option value="ES" <?php if ($obj['cli_state'] == 'ES') { echo 'selected'; } ?>>Espírito Santo</option>
													<option value="GO" <?php if ($obj['cli_state'] == 'GO') { echo 'selected'; } ?>>Goiás</option>
													<option value="MA" <?php if ($obj['cli_state'] == 'MA') { echo 'selected'; } ?>>Maranhão</option>
													<option value="MT" <?php if ($obj['cli_state'] == 'MT') { echo 'selected'; } ?>>Mato Grosso</option>
													<option value="MS" <?php if ($obj['cli_state'] == 'MS') { echo 'selected'; } ?>>Mato Grosso do Sul</option>
													<option value="MG" <?php if ($obj['cli_state'] == 'MG') { echo 'selected'; } ?>>Minas Gerais</option>
													<option value="PA" <?php if ($obj['cli_state'] == 'PA') { echo 'selected'; } ?>>Pará</option>
													<option value="PB" <?php if ($obj['cli_state'] == 'PB') { echo 'selected'; } ?>>Paraíba</option>
													<option value="PR" <?php if ($obj['cli_state'] == 'PR') { echo 'selected'; } ?>>Paraná</option>
													<option value="PE" <?php if ($obj['cli_state'] == 'PE') { echo 'selected'; } ?>>Pernambuco</option>
													<option value="PI" <?php if ($obj['cli_state'] == 'PI') { echo 'selected'; } ?>>Piauí</option>
													<option value="RJ" <?php if ($obj['cli_state'] == 'RJ') { echo 'selected'; } ?>>Rio de Janeiro</option>
													<option value="RN" <?php if ($obj['cli_state'] == 'RN') { echo 'selected'; } ?>>Rio Grande do Norte</option>
													<option value="RS" <?php if ($obj['cli_state'] == 'RS') { echo 'selected'; } ?>>Rio Grande do Sul</option>
													<option value="RO" <?php if ($obj['cli_state'] == 'RO') { echo 'selected'; } ?>>Rondônia</option>
													<option value="RR" <?php if ($obj['cli_state'] == 'RR') { echo 'selected'; } ?>>Roraima</option>
													<option value="SC" <?php if ($obj['cli_state'] == 'SC') { echo 'selected'; } ?>>Santa Catarina</option>
													<option value="SP" <?php if ($obj['cli_state'] == 'SP') { echo 'selected'; } ?>>São Paulo</option>
													<option value="SE" <?php if ($obj['cli_state'] == 'SE') { echo 'selected'; } ?>>Sergipe</option>
													<option value="TO" <?php if ($obj['cli_state'] == 'TO') { echo 'selected'; } ?>>Tocantins</option>
							                      </select>
											</div>
											<div class="col-lg-3">
												<label>CEP</label>
                      							<input type="text" class="form-control" name="zipcode" required="true" maxlength="9"  id="cep" value="<?=$obj['cli_zip']?>">
                      							<input type="hidden" name="cli_id" value="<?=$obj['cli_id']?>?>">
											</div>
										</div>										
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="add" class="btn btn-primary">Salvar</button>
											<button type="button" onclick="window.location='clients.php'" class="btn btn-primary">Voltar</button>
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