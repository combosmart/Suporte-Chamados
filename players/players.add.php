<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Inclusão de Ponto');    
?>
<?php include '../header.php'; ?>
<?php
  if (!empty($_POST)) {

    unset($_SESSION['success']);
    $error = [];
    $success = [];
    
    $ponto = new ArrayObject();
    $ponto->client   = $_POST['client'];
    $ponto->name     = $_POST['name'];
    $ponto->notes    = $_POST['notes'];
    $ponto->address  = $_POST['address'];
    $ponto->number   = $_POST['number'];
    $ponto->neighbor = $_POST['neighbor'];
    $ponto->city     = $_POST['city'];
    $ponto->state    = $_POST['state'];
    $ponto->zip      = $_POST['zipcode'];
    $ponto->active   = 1;

    $pontoId = $player->addPlayer($ponto);

    if($pontoId > 0) { 
      if (!empty($_POST['ctc_name1'])) {
        $person = new ArrayObject();
        $person->player = $pontoId;        
        $person->name = $_POST['ctc_name1'];
        $person->landPhone = $_POST['ctc_landline_phone1'];
        $person->cellPhone = $_POST['ctc_cell_phone1'];
        $person->email = $_POST['ctc_email1'];

        $result = $player->addContact($person);        
      }

      if (!empty($_POST['ctc_name2'])) {
        $person = new ArrayObject();
        $person->player = $pontoId;        
        $person->name = $_POST['ctc_name2'];
        $person->landPhone = $_POST['ctc_landline_phone2'];
        $person->cellPhone = $_POST['ctc_cell_phone2'];
        $person->email = $_POST['ctc_email2'];

        $result =  $result && ($player->addContact($person));        
      }

      if ($result) {
        $_SESSION['success'] = 'Ponto adicionado com sucesso';
        header('Location: players.php');
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
												<label class="control-label text-lg-left pt-2">Cliente</label>
												<select data-plugin-selectTwo name="client" class="form-control populate" required="true">
												  <?php foreach ($client->listClients() as $c) { ?>
												    <option value="<?=$c['cli_id']?>"><?=$c['cli_name']?></option>
												  <?php } ?>                    
												</select>
											</div>
										</div>	
										<div class="form-group row">
											<div class="col-lg-8">							
												<label>Nome do Ponto</label>
                        						<input type="text" class="form-control" name="name">
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-12">							
												<label>Observações</label>
                        						<textarea name="notes" class="form-control" rows="5" placeholder="Observações..."></textarea>
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
										<div class="form-group row">
											<div class="col-lg-4">							
												<label>Contato</label>
												<input type="text" class="form-control" name="ctc_name1">
											</div>
											<div class="col-lg-4">							
												<label>Email</label>
                        						<input type="email" class="form-control" name="ctc_email1">
											</div>
											<div class="col-lg-2">							
												<label>Telefone Fixo</label>
                        						<input type="text" class="form-control" name="ctc_landline_phone1">
											</div>
											<div class="col-lg-2">							
												<label>Telefone Celular</label>
                        						<input type="text" class="form-control" name="ctc_cell_phone1">
											</div>
										</div>										
										<div class="form-group row">
											<div class="col-lg-4">							
												<label>Contato</label>
												<input type="text" class="form-control" name="ctc_name2">
											</div>
											<div class="col-lg-4">							
												<label>Email</label>
                        						<input type="email" class="form-control" name="ctc_email2">
											</div>
											<div class="col-lg-2">							
												<label>Telefone Fixo</label>
                        						<input type="text" class="form-control" name="ctc_landline_phone2">
											</div>
											<div class="col-lg-2">							
												<label>Telefone Celular</label>
                        						<input type="text" class="form-control" name="ctc_cell_phone2">
											</div>
										</div>
								</div>																
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="add" class="btn btn-primary">Salvar</button>
											<button type="button" onclick="window.location='players.php'" class="btn btn-primary">Voltar</button>
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