<header class="header">
	<div class="logo-container">
		<a href="../main/dashboard.php" class="logo">
			<img src="../img/logo.png" width="75" height="21" alt="Combo Smart Solutions" />
		</a>
		<div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
			<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
		</div>
	</div>

	<!-- start: search & user box -->
	<div class="header-right">

		<span class="separator"></span>

		<div id="userbox" class="userbox">
			<a href="#" data-toggle="dropdown">
				<figure class="profile-picture">
					<img src="../img_profile/<?=$_SESSION['user']['photo']?>" alt="<?=$_SESSION['user']['name']?>" class="rounded-circle" data-lock-picture="../img_profile/<?=$_SESSION['user']['photo']?>" />
				</figure>
				<div class="profile-info" data-lock-name="John Doe" data-lock-email="johndoe@okler.com">
					<span class="name"><?=$_SESSION['user']['name']?></span>
					<span class="role"><?=$_SESSION['user']['group']?></span>
				</div>

				<i class="fa custom-caret"></i>
			</a>

			<div class="dropdown-menu">
				<ul class="list-unstyled mb-2">
					<li class="divider"></li>
					<li>
						<a role="menuitem" tabindex="-1" href="../users/changepass.php"><i class="fa fa-user"></i> Alterar Senha</a>
					</li>
					<li>
						<a role="menuitem" tabindex="-1" href="../logout.php"><i class="fa fa-power-off"></i> Sair</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- end: search & user box -->
</header>