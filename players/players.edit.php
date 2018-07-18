<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Alteração de Dados do Ponto');    
?>
<?php include '../header.php'; ?>
<?php
  $id  = $_GET['id'];
  $obj      = $player->getPlayer($id);
  $contacts = $player->listContacts($id);
  $idxContact = 1;
  
  if (isset($_POST['alterar'])) {

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
    $ponto->active   = $_POST['active'];
    $ponto->id       = $_POST['ptn_id'];

    $result = $player->playerUpdate($ponto);

    if($result) {

      if (isset($_POST['ctc_name1'])) {
        $person = new ArrayObject();
        $person->player = $ponto->id;
        $person->name = $_POST['ctc_name1'];
        $person->landPhone = $_POST['ctc_landline_phone1'];
        $person->cellPhone = $_POST['ctc_cell_phone1'];
        $person->email = $_POST['ctc_email1'];
        $person->id = $_POST['ctc_id1'];

        $result = $player->contactUpdate($person);        
      }

      if (isset($_POST['ctc_name2'])) {
        $person = new ArrayObject();
        $person->player = $ponto->id;
        $person->name = $_POST['ctc_name2'];
        $person->landPhone = $_POST['ctc_landline_phone2'];
        $person->cellPhone = $_POST['ctc_cell_phone2'];
        $person->email = $_POST['ctc_email2'];
        $person->id = $_POST['ctc_id2'];

        $result = $result && ($player->contactUpdate($person));        
      }

      if ($result) {
        $_SESSION['success'] = 'Dados alterados com sucesso';        
        header('Location: players.php');
      }

    } else {
      $error[]   = 'Ocorreu um problema na alteração dos dados do ponto';
    }        
  }
  
  if (!empty($_POST['modal-ctc-id'])) {    
    $ctc_id = $_POST['modal-ctc-id'];
    if ($player->deleteContact($ctc_id)){
      $_SESSION['success'] = 'Contato removido com sucesso';
      header('Location: players.edit.php?id=' . $id);
    }
  }

  if (!empty($_POST['ctc_name'])) {
    $person = new ArrayObject();
    $person->player = $_POST['ptn_id'];
    $person->name = $_POST['ctc_name'];
    $person->landPhone = $_POST['ctc_landline_phone'];
    $person->cellPhone = $_POST['ctc_cell_phone'];
    $person->email = $_POST['ctc_email'];    

    if($player->addContact($person)) {
      header('Location: players.edit.php?id=' . $id); 
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
					                <?php 
									if (isset($_SESSION['success'])) {
			          					echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
			          					unset($_SESSION['success']);
			          				} ?>
									<form class="form-bordered" method="post">
										<div class="form-group row">
											<div class="col-lg-6">							
												<label class="control-label text-lg-left pt-2">Cliente</label>
												<select name="client" class="form-control mb-3" required="true">                    
												  <?php foreach ($client->listClients() as $c) { ?>
												    <option value="<?=$c['cli_id']?>" <?php if ($c['cli_id'] == $obj['cli_id']) { echo 'selected'; } ?> ><?=$c['cli_name']?></option>
												  <?php } ?>                    
												</select>
											</div>
											<div class="col-lg-6">
												<label class="control-label text-lg-left pt-2">Status do Ponto</label>
						                        <select name="active" class="form-control">
						                          <option value='1' <?php if ($obj['pnt_active'] == 1) { echo 'selected'; } ?>>Ativo</option>
						                          <option value='0' <?php if ($obj['pnt_active'] == 0) { echo 'selected'; } ?>>Inativo</option>
						                        </select>
											</div>
										</div>	
										<div class="form-group row">
											<div class="col-lg-8">							
												<label>Nome do Ponto</label>
                        						<input type="text" class="form-control" name="name" value="<?=$obj['pnt_name']?>">
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-12">							
												<label>Observações</label>
                        						<textarea name="notes" class="form-control" rows="5" placeholder="Observações..."><?=$obj['pnt_notes']?></textarea>
											</div>
										</div>											
										<div class="form-group row">
											<div class="col-lg-6">							
												<label>Endereço</label>
                      							<input type="text" class="form-control" name="address" required="true" id="rua" value="<?=$obj['pnt_address']?>">
											</div>
											<div class="col-lg-2">
												<label>Número</label>
                      							<input type="text" class="form-control" name="number" required="true" value="<?=$obj['pnt_number']?>">
											</div>
											<div class="col-lg-4">
												<label>Bairro</label>
                      							<input type="text" class="form-control" name="neighbor" required="true" id="bairro" value="<?=$obj['pnt_neighbor']?>">
											</div>
										</div>
										<div class="form-group row">
											<div class="col-lg-6">
												<label>Cidade</label>
                      							<input type="text" class="form-control" name="city" required="true" id="cidade" value="<?=$obj['pnt_city']?>">
											</div>
											<div class="col-lg-3">
												  <label>Estado</label>
							                      <select name="state" class="form-control" id="uf">                       
							                        <option value="AC" <?php if ($obj['pnt_state'] == 'AC') { echo 'selected'; } ?>>Acre</option>
							                        <option value="AL" <?php if ($obj['pnt_state'] == 'AL') { echo 'selected'; } ?>>Alagoas</option>
							                        <option value="AP" <?php if ($obj['pnt_state'] == 'AP') { echo 'selected'; } ?>>Amapá</option>
							                        <option value="AM" <?php if ($obj['pnt_state'] == 'AM') { echo 'selected'; } ?>>Amazonas</option>
							                        <option value="BA" <?php if ($obj['pnt_state'] == 'BA') { echo 'selected'; } ?>>Bahia</option>
							                        <option value="CE" <?php if ($obj['pnt_state'] == 'CE') { echo 'selected'; } ?>>Ceará</option>
							                        <option value="DF" <?php if ($obj['pnt_state'] == 'DF') { echo 'selected'; } ?>>Distrito Federal</option>
							                        <option value="ES" <?php if ($obj['pnt_state'] == 'ES') { echo 'selected'; } ?>>Espírito Santo</option>
							                        <option value="GO" <?php if ($obj['pnt_state'] == 'GO') { echo 'selected'; } ?>>Goiás</option>
							                        <option value="MA" <?php if ($obj['pnt_state'] == 'MA') { echo 'selected'; } ?>>Maranhão</option>
							                        <option value="MT" <?php if ($obj['pnt_state'] == 'MT') { echo 'selected'; } ?>>Mato Grosso</option>
							                        <option value="MS" <?php if ($obj['pnt_state'] == 'MS') { echo 'selected'; } ?>>Mato Grosso do Sul</option>
							                        <option value="MG" <?php if ($obj['pnt_state'] == 'MG') { echo 'selected'; } ?>>Minas Gerais</option>
							                        <option value="PA" <?php if ($obj['pnt_state'] == 'PA') { echo 'selected'; } ?>>Pará</option>
							                        <option value="PB" <?php if ($obj['pnt_state'] == 'PB') { echo 'selected'; } ?>>Paraíba</option>
							                        <option value="PR" <?php if ($obj['pnt_state'] == 'PR') { echo 'selected'; } ?>>Paraná</option>
							                        <option value="PE" <?php if ($obj['pnt_state'] == 'PE') { echo 'selected'; } ?>>Pernambuco</option>
							                        <option value="PI" <?php if ($obj['pnt_state'] == 'PI') { echo 'selected'; } ?>>Piauí</option>
							                        <option value="RJ" <?php if ($obj['pnt_state'] == 'RJ') { echo 'selected'; } ?>>Rio de Janeiro</option>
							                        <option value="RN" <?php if ($obj['pnt_state'] == 'RN') { echo 'selected'; } ?>>Rio Grande do Norte</option>
							                        <option value="RS" <?php if ($obj['pnt_state'] == 'RS') { echo 'selected'; } ?>>Rio Grande do Sul</option>
							                        <option value="RO" <?php if ($obj['pnt_state'] == 'RO') { echo 'selected'; } ?>>Rondônia</option>
							                        <option value="RR" <?php if ($obj['pnt_state'] == 'RR') { echo 'selected'; } ?>>Roraima</option>
							                        <option value="SC" <?php if ($obj['pnt_state'] == 'SC') { echo 'selected'; } ?>>Santa Catarina</option>
							                        <option value="SP" <?php if ($obj['pnt_state'] == 'SP') { echo 'selected'; } ?>>São Paulo</option>
							                        <option value="SE" <?php if ($obj['pnt_state'] == 'SE') { echo 'selected'; } ?>>Sergipe</option>
							                        <option value="TO" <?php if ($obj['pnt_state'] == 'TO') { echo 'selected'; } ?>>Tocantins</option>
							                      </select>
											</div>
											<div class="col-lg-3">
												<label>CEP</label>
                      							<input type="text" class="form-control" name="zipcode" required="true" maxlength="9"  id="cep" value="<?=$obj['pnt_zip']?>">
											</div>
										</div>
										<?php if(count($contacts) > 0) { ?>
											<?php foreach ($contacts as $cto) { ?>
											<div class="form-group row">
												<div class="col-lg-3">							
													<label>Contato</label>
													<input type="text" class="form-control" value="<?=$cto['ctc_name']?>" name="<?php echo 'ctc_name'. $idxContact ?>">
												</div>
												<div class="col-lg-3">							
													<label>Email</label>
	                        						<input type="email" class="form-control" value="<?=$cto['ctc_email']?>" name="<?php echo 'ctc_email'. $idxContact ?>">
												</div>
												<div class="col-lg-2">							
													<label>Telefone Fixo</label>
	                        						<input type="text" class="form-control" value="<?=$cto['ctc_landline_phone']?>" name="<?php echo 'ctc_landline_phone'. $idxContact ?>">
												</div>
												<div class="col-lg-2">							
													<label>Telefone Celular</label>
	                        						<input type="text" class="form-control" value="<?=$cto['ctc_cell_phone']?>" name="<?php echo 'ctc_cell_phone'. $idxContact ?>">
												</div>
												<div class="col-lg-1">
													<label>.</label>							                        
							                        <button id="<?=$cto['ctc_id']?>" type="button" class="mr-1 ws-normal btn btn-default" data-toggle="modal" data-target="#modal-delete-contact">Remover</button>
												</div>
												<input type="hidden" value="<?=$cto['ctc_id']?>" name="<?php echo 'ctc_id'. $idxContact ?>" >
												<?php $idxContact++; ?>
											</div>
											<?php } ?>
										<?php } ?>																				
								</div>	
								<input type="hidden" name="ptn_id" value="<?=$id?>">												
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button type="submit" name="alterar" class="btn btn-primary">Alterar</button>
											<?php if(count($contacts) < 2) { ?>
							                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-contact">
							                  Adicionar Contato
							                </button>                
							                <?php } ?>
											<button type="button" onclick="window.location='players.php'" class="btn btn-primary">Voltar</button>
										</div>
									</div>
								</footer>
								</form>
							</section>
							<!-- Modal Delete Contact -->							
							<!-- Modal -->
							<div class="modal fade" id="modal-delete-contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							  <form method="post" id="md-delete-contact">							  
							  <div class="modal-dialog" role="document">
							    <div class="modal-content">
							      <div class="modal-body">
							        Deseja remover o contato selecionado?
							      </div>
							      <div class="modal-footer">
							      	<button type="submit" class="btn btn-primary" id="confirm-contact-delete">Sim</button>
							        <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>					<input type="hidden" name="modal-ctc-id" id="modal-ctc-id">		        
							      </div>
							    </div>
							  </div>							  
							  </form>
							</div>					
							<!-- / Modal Delete Contact -->
							<!-- ADD CONTACT MODAL -->
				           <div class="modal fade" id="modal-add-contact">
				              <div class="modal-dialog">
				                <div class="modal-content">
				                  <div class="modal-header">
				                    <h4 class="modal-title">Adicionar Contato</h4>
				                  </div>
				                  <form role="form" method="post" id="form-add-contact">
				                    <div class="modal-body">
				                      <div class="form-group row">
				                        <div class="col-lg-6">							
				                          <label>Nome</label>
				                          <input type="text" class="form-control" name="ctc_name" required="true">				
				                        </div>
				                        <div class="col-lg-6">
				                          	<label>Email</label>
				                            <input type="email" class="form-control" name="ctc_email" required="true">				
				                        </div>                        
				                      </div>
				                      <div class="form-group row">
				                        <div class="col-lg-6">
				                          	<label>Telefone Fixo</label>
				                            <input type="text" class="form-control" name="ctc_landline_phone" required="true">		
				                        </div>
				                        <div class="col-lg-6">				                          
				                            <label>Telefone Celular</label>
				                            <input type="text" class="form-control" name="ctc_cell_phone" required="true">
				                        </div>
				                      </div>
				                    </div>
				                    <div class="modal-footer">
				                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
				                      <button type="button" name="add-contact" id="add-contact" class="btn btn-primary">Adicionar</button>
				                    </div>
				                    <input type="hidden" name="ptn_id" value="<?=$id?>">
				                  </form>
				                </div>
				                <!-- /.modal-content -->
				              </div>
				              <!-- /.modal-dialog -->
				            </div>
				            <!-- /.modal -->
				          <!--/ADD CONTACT MODAL  -->
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