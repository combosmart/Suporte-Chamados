<?php 
  require('../config.php'); 
  if(!$user->is_logged_in()){ header('Location: ../index.php'); } 
  define('sitetile','Alteração de Senha de Usuário');    
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
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
										<strong>Well done!</strong> You are using an awesome template! <a href="" class="alert-link">Say Hi to Porto Admin</a>.
									</div>
									<div class="alert alert-danger">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
										<strong>Oh snap!</strong> Are you using other template? <a href="" class="alert-link">Buy Porto Admin now</a> and make your customers a lot happier.
									</div>
									<form class="form-bordered" method="post">
										<div class="form-group">									
											<label class="col-lg-3 control-label text-lg-left pt-2" for="inputDefault">Nova senha</label>
											<div class="col-lg-6">
												<input type="password" name="password" class="form-control" id="inputDefault">
											</div>
										</div>
										<div class="form-group">									
											<label class="col-lg-3 control-label text-lg-left pt-2" for="inputDefault">Confirme nova senha</label>
											<div class="col-lg-6">
												<input type="password" name="confirm_password" class="form-control" id="inputDefault">
											</div>
										</div>
									</form>
								</div>
								<footer class="card-footer">
									<div class="row">
										<div class="col-sm-9">
											<button class="btn btn-primary">Submit</button>
											<button type="reset" class="btn btn-default">Reset</button>
										</div>
									</div>
								</footer>
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