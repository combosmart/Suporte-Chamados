<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Inclusão de Cliente');    
?>
<?php include '../header.php'; ?>
<?php
  	if (!empty($_POST)) {

    	unset($_SESSION['success']);
	    $error = [];
	    $success = [];

	    $cliente = new ArrayObject();  
	    $contato = new ArrayObject();  
	    
	    $cliente->cli_active       = 1;
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
		$cliente->cli_nmfnt        = valOrNull($_POST['cli_nmfnt']);
	    $cliente->cli_flg_elemidia = $_POST['cli_flg_elemidia'];

	    $contato->ctc_name           = $_POST['ctc_name'];
		$contato->ctc_job            = $_POST['ctc_job'];
		$contato->ctc_email          = $_POST['ctc_email'];
		$contato->ctc_landline_phone = $_POST['ctc_landline_phone'];
		$contato->ctc_cell_phone     = $_POST['ctc_cell_phone'];
		$contato->ctc_notes          = $_POST['ctc_notes'];

	    /*
	    if ($client->checkCNPJ($cliente)) {
	    	$error[] = 'CNPJ já cadastrado no sistema';
	    }
	    */

	    if(empty($error)) {
	      	$cli_id = $client->addClient($cliente);	
		  	if($cli_id > 0) {
				$contato->cli_id = $cli_id;
				if ($contact->addContact($contato)) {
					$_SESSION['success'] = 'Cliente adicionado com sucesso';
		    		header('Location: clients.php');
				} else {
					header('Location: clients.php');
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
											<div class="col-lg-6">							
												<label class="control-label text-lg-left pt-2">Nome Fantasia</label>
												<input type="text" class="form-control" name="cli_nmfnt">				
											</div>											
											<div class="col-lg-4">
												<label class="col-lg-6 control-label pt-2">Cliente Elemídia?</label>
												<select class="form-control mb-3" name="cli_flg_elemidia">
													<option value="1">Sim</option>
													<option value="0" selected>Não</option>												
												</select>		
											</div>											
										</div>											
										<div class="form-group row">
											<div class="col-lg-6">							
												<label class="control-label text-lg-left pt-2">Razão Social</label>
												<input type="text" class="form-control" name="cli_name" required="true">				
											</div>											
											<div class="col-lg-3">							
												<label class="control-label text-lg-left pt-2">CNPJ</label>
												<input id="cli_cnpj" type="text" name="cli_cnpj" data-plugin-masked-input data-input-mask="99.999.999/9999-99" placeholder="00.000.000/0001-00" class="form-control" required="true">
											</div>
											<div class="col-lg-3">
												<label class="col-lg-6 control-label text-lg-left pt-2">Prioridade</label>
												<select name="pri_id" class="form-control mb-3" id="pri_id">
													<?php foreach ($client->listPriorities() as $k) { ?>
														<option value="<?=$k['pri_id']?>"><?=$k['pri_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>											
										<div class="form-group row">
											<div class="col-lg-6">							
												<label>Endereço</label>
                      							<input type="text" class="form-control" name="address" required="true" id="rua">
											</div>
											<div class="col-lg-2">
												<label>Número</label>
                      							<input type="text" class="form-control" name="number" required="true">
											</div>
											<div class="col-lg-4">
												<label>Bairro</label>
                      							<input type="text" class="form-control" name="neighbor" required="true" id="bairro">
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-6">
												<label>Cidade</label>
                      							<input type="text" class="form-control" name="city" required="true" id="cidade">
											</div>
											<div class="col-lg-3">
												  <label>Estado</label>
							                      <select name="state" class="form-control" id="uf">                       
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
												<label>CEP</label>
                      							<input type="text" class="form-control" name="zipcode" required="true" maxlength="9"  id="cep">
											</div>
										</div>
										<header class="card-header">
											<h2 class="card-title">Informações de Contato</h2>				
										</header><br/>								
										<div class="form-group row">										
											<div class="col-lg-6">							
												<label>Nome</label>
												<input type="text" class="form-control" name="ctc_name">
											</div>
											<div class="col-lg-6">							
												<label>Cargo</label>
												<input type="text" class="form-control" name="ctc_job">
											</div>											
										</div>
										<div class="form-group row">										
											<div class="col-lg-4">							
												<label>Email</label>
                        						<input type="email" class="form-control" name="ctc_email">
											</div>
											<div class="col-lg-4">							
												<label>Telefone Fixo</label>
                        						<input type="text" class="form-control" name="ctc_landline_phone">
											</div>
											<div class="col-lg-4">							
												<label>Telefone Celular</label>
                        						<input type="text" class="form-control" name="ctc_cell_phone">
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-12">							
												<label>Observações</label>
                        						<textarea name="ctc_notes" class="form-control" rows="5" placeholder="Observações..."></textarea>
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