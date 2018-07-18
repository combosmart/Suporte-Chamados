<?php
require_once('config.php');
if($user->is_logged_in()) { header('Location: main/dashboard.php'); } 

if (!empty($_POST)) {
    
    $email    = $_POST['email'];
    $password = $_POST['password'];
  
  if (empty($email) || empty($password)) 
  {
      $error[] = 'Preencha os campos corretamente';
  } else {      
      if($user->login($email,$password)) { 
        header('Location: main/dashboard.php');        
      } else {        
        $error[] = 'Login/senha incorretos ou usuário inativo';
      }        
  }  
}

?>
<!doctype html>
<html class="fixed">
	<head>				
		<!-- Basic -->
		<meta charset="UTF-8">
		<title>HelpDesk Combo Smart Solutions | Log in</title>
		<link rel="shortcut icon" href="favicon.ico"/>
		<meta name="keywords" content="HelpDesk Combo Smart Solutions" />
		<meta name="description" content="HelpDesk Combo Smart Solutions">
		<meta name="author" content="combovideos.com.br">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="vendor/animate/animate.css">

		<link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="css/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="css/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="css/custom.css">

		<!-- Head Libs -->
		<script src="vendor/modernizr/modernizr.js"></script>


	</head>
	<body>
		<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
				<a href="/" class="logo float-left">
					<img src="img/logo.png" height="54" alt="Porto Admin" />
				</a>
				<br/><br/><br/>
				<div class="panel card-sign">
					<div class="card-body">
						<?php 
					        if(isset($error)){
					            foreach($error as $error){
					            	$loginBoxMsg = $error;
					            }
					            echo '<p class="login-box-msg">' . $loginBoxMsg . '</p>';
					        }
					    ?>						
						<form method="post">
							<div class="form-group mb-3">
								<label>Username</label>
								<div class="input-group input-group-icon">
									<input name="email" type="text" class="form-control form-control-lg" required="true" />
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-user"></i>
										</span>
									</span>
								</div>
							</div>

							<div class="form-group mb-3">
								<div class="clearfix">
									<label class="float-left">Password</label>
									<!--<a href="pages-recover-password.html" class="float-right">Lost Password?</a>-->
								</div>
								<div class="input-group input-group-icon">
									<input name="password" type="password" class="form-control form-control-lg" required="true" />
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-lock"></i>
										</span>
									</span>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12 text-center">
									<button type="submit" class="btn btn-primary mt-2">Sign In</button>
								</div>
							</div>							

						</form>
					</div>
				</div>

				<p class="text-center text-muted mt-3 mb-3">&copy; <?php echo date('Y') ?> Combo Smart Solutions</p>
			</div>
		</section>
		<!-- end: page -->

		<!-- Vendor -->
		<script src="vendor/jquery/jquery.js"></script>		<script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>		<script src="vendor/popper/umd/popper.min.js"></script>		<script src="vendor/bootstrap/js/bootstrap.js"></script>		<script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>		<script src="vendor/common/common.js"></script>		<script src="vendor/nanoscroller/nanoscroller.js"></script>		<script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>		<script src="vendor/jquery-placeholder/jquery-placeholder.js"></script>
		
		<!-- Theme Base, Components and Settings -->
		<script src="js/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="js/custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="js/theme.init.js"></script>

	</body>
</html>