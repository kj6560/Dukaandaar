<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>SwiftSell</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
		name='viewport' />
	<link rel="stylesheet" href="{{ asset('theme/backend') }}/assets/css/bootstrap.min.css">
	<link rel="stylesheet"
		href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
	<link rel="stylesheet" href="{{ asset('theme/backend') }}/assets/css/ready.css">
	<link rel="stylesheet" href="{{ asset('theme/backend') }}/assets/css/demo.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.css" />
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
	<div class="wrapper">
		<div class="main-header">
			<div class="logo-header">
				<a href="index.html" class="logo">
					SwiftSell Dashboard
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
					data-target="collapse" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<button class="topbar-toggler more"><i class="la la-ellipsis-v"></i></button>
			</div>
			<nav class="navbar navbar-header navbar-expand-lg">
				<div class="container-fluid">
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<li class="nav-item dropdown">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"
								aria-expanded="false"> <img src="{{ url('/storage') }}/{{ Auth::user()->profile_pic }}"
									alt="user-img" width="36" class="img-circle"><span>Hizrian</span></span> </a>
							<ul class="dropdown-menu dropdown-user">
								<li>
									<div class="user-box">
										<div class="u-img"><img
												src="{{ url('/storage') }}/{{ Auth::user()->profile_pic }}" alt="user">
										</div>
										<div class="u-text">
											<h4><?php
												echo Auth::user()->name;
											?></h4>
											<p class="text-muted"><?php
												echo Auth::user()->email;
											?></p><a href="profile.html"
												class="btn btn-rounded btn-danger btn-sm">View Profile</a>
										</div>
									</div>
								</li>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#"><i class="ti-user"></i> My Profile</a>
								<a class="dropdown-item" href="#"></i> My Balance</a>
								<a class="dropdown-item" href="#"><i class="ti-email"></i> Inbox</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#"><i class="ti-settings"></i> Account Setting</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#"><i class="fa fa-power-off"></i> Logout</a>
							</ul>
							<!-- /.dropdown-user -->
						</li>
					</ul>
				</div>
			</nav>
		</div>
		<div class="sidebar">
			<div class="scrollbar-inner sidebar-wrapper">
				<div class="user">
					<div class="photo">
						<img src="{{ url('/storage') }}/{{ Auth::user()->profile_pic }}">
					</div>
					<div class="info">
						<a class="" data-toggle="collapse" href="#collapseExample" aria-expanded="true">
							<span>
								<?php
												echo Auth::user()->name;
											?>
								<span class="user-level"><?php
												echo Auth::user()->role ==1 ? "Super User":"User";
											?></span>
								<span class="caret"></span>
							</span>
						</a>
						<div class="clearfix"></div>

						<div class="collapse in" id="collapseExample" aria-expanded="true" style="">
							<ul class="nav">
								<li>
									<a href="#profile">
										<span class="link-collapse">My Profile</span>
									</a>
								</li>
								<li>
									<a href="#edit">
										<span class="link-collapse">Edit Profile</span>
									</a>
								</li>
								<li>
									<a href="#settings">
										<span class="link-collapse">Settings</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<ul class="nav">
					<li class="nav-item active">
						<a href="index.html">
							<i class="la la-dashboard"></i>
							<p>Dashboard</p>
							<span class="badge badge-count">5</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="components.html">
							<i class="la la-table"></i>
							<p>Components</p>
							<span class="badge badge-count">14</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="forms.html">
							<i class="la la-keyboard-o"></i>
							<p>Forms</p>
							<span class="badge badge-count">50</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="tables.html">
							<i class="la la-th"></i>
							<p>Tables</p>
							<span class="badge badge-count">6</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="notifications.html">
							<i class="la la-bell"></i>
							<p>Notifications</p>
							<span class="badge badge-success">3</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="typography.html">
							<i class="la la-font"></i>
							<p>Typography</p>
							<span class="badge badge-danger">25</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="icons.html">
							<i class="la la-fonticons"></i>
							<p>Icons</p>
						</a>
					</li>
					<li class="nav-item update-pro">
						<button data-toggle="modal" data-target="#modalUpdate">
							<i class="la la-hand-pointer-o"></i>
							<p>Update To Pro</p>
						</button>
					</li>
				</ul>
			</div>
		</div>
		<div class="main-panel">
			@yield('content')
			<footer class="footer">
				<div class="container-fluid">
					<nav class="pull-left">
						<ul class="nav">
							<li class="nav-item">
								<a class="nav-link" href="http://www.themekita.com">
									Shiwkesh Schematics
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">
									Contact Us
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">
									About Us
								</a>
							</li>
						</ul>
					</nav>
					<div class="copyright ml-auto">
						<?php echo date("Y");?>, made with <i class="la la-heart heart text-danger"></i> by <a
							href="https://shiwkesh.in" target="_blank">Shiwkesh Schematics Private Limited</a>
					</div>
				</div>
			</footer>
		</div>
	</div>
	</div>

</body>
<script src="{{ asset('theme/backend') }}/assets/js/core/jquery.3.2.1.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/core/popper.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/core/bootstrap.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/chartist/chartist.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/chartist/plugin/chartist-plugin-tooltip.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/jquery-mapael/jquery.mapael.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/jquery-mapael/maps/world_countries.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/chart-circle/circles.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/ready.min.js"></script>
<script src="{{ asset('theme/backend') }}/assets/js/demo.js"></script>
<script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>
@yield('custom_javascript')
</html>