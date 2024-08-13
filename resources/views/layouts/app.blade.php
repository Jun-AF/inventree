<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<!-- Document Title -->
		<title>{{ config('app.name', 'Inventree') }}</title>
		<link rel="icon" type="image/x-icon" href="{{ asset('img/SIS-removebg-preview.png') }}">
		
		<!-- Custom fonts for this template-->
		<link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
		<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
		
		<!-- Custom styles for this template-->
		<link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
		<link href="{{ asset('app/app.css') }}" rel="stylesheet">
		<link rel="stylesheet" href="{{ asset('welcome/dist/css/custom.css') }}">
		
		<!-- Custom styles for this page -->
		<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet">
		<link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap4.css') }}">
		
		<!-- Page level plugins -->
		<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
	</head>
	<body id="page-top">
		<!-- Page Wrapper -->
		<div id="wrapper">
			<!-- Sidebar -->
			<ul class="navbar-nav bg-dark sidebar sidebar-dark accordion" id="accordionSidebar">
				<!-- Sidebar - Brand -->
				<a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
					<div class="sidebar-brand-icon">
						<img class="header-logo-image-sm" src="{{ asset('img/SIS-removebg-preview.png') }}" alt="SIS">
					</div>
					<div class="sidebar-brand-text mx-3">SIS</div>
				</a>
				<!-- Divider -->
				<hr class="sidebar-divider my-0">
				<!-- Nav Item - Dashboard -->
				<li class="nav-item">
					<a class="nav-link" href="{{ route('home') }}">
						<i class="fas fa-fw fa-tachometer-alt"></i>
						<span>Dashboard</span>
					</a>
				</li>
				<!-- Divider -->
				<hr class="sidebar-divider">
				<!-- Heading -->
				<div class="sidebar-heading"> Pages </div>
                @if (Auth::user()->role == "Super Admin" || Auth::user()->role == "Planner")
				<li class="nav-item">
					<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#assetCollection" aria-expanded="true" aria-controls="assetCollection">
						<i class="fas fa-fw fa-list"></i>
						<span>Master Table</span>
					</a>
					<div id="assetCollection" class="collapse" aria-labelledby="assetCollection" data-parent="#accordionSidebar">
						<div class="bg-white py-2 collapse-inner rounded">
							<h6 class="collapse-header">Menus:</h6>
							<a class="collapse-item" href="{{ route('supervisor') }}">Supervisor</a>
							<a class="collapse-item" href="{{ route('scaler') }}">Scaler</a>
							<a class="collapse-item" href="{{ route('operator') }}">Operator</a>
                            <a class="collapse-item" href="{{ route('user') }}">User</a>
						</div>
					</div>
				</li>
                <li class="nav-item">
					<a class="nav-link" href="{{ route('rkt') }}">
						<i class="fas fa-fw fa-maps"></i>
						<span>RKT</span>
					</a>
				</li>
                @endif
				@if (Auth::user()->role == 'Super admin' || Auth::user()->role == 'Harvesting' || Auth::user()->role == 'TUK')
				<li class="nav-item">
					<a class="nav-link" href="{{ route('harvesting') }}">
						<i class="fas fa-fw fa-tree"></i>
						<span>Harvesting</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#assetCollection" aria-expanded="true" aria-controls="assetCollection">
						<i class="fas fa-fw fa-list"></i>
						<span>TUK</span>
					</a>
					<div id="assetCollection" class="collapse" aria-labelledby="assetCollection" data-parent="#accordionSidebar">
						<div class="bg-white py-2 collapse-inner rounded">
							<h6 class="collapse-header">Menus:</h6>
							<a class="collapse-item" href="{{ route('measurement_28') }}">Measurement 28</a>
							<a class="collapse-item" href="{{ route('measurement_42') }}">Measurement 42</a>
							<a class="collapse-item" href="{{ route('hauling_28') }}">Hauling 28</a>
                            <a class="collapse-item" href="{{ route('hauling_42') }}">Hauling 42</a>
						</div>
					</div>
				</li>
				@endif
                @if(Auth::user()->role == 'Super Admin')
                <li class="nav-item">
					<a class="nav-link" href="{{ route('settings') }}">
						<i class="fas fa-fw fa-cog"></i>
						<span>Settings</span>
					</a>
				</li>
                @endif 
			</ul>
			<!-- End of Sidebar -->
			<!-- Content Wrapper -->
			<div id="content-wrapper" class="d-flex flex-column">
				<!-- Main Content -->
				<div id="content">
					<!-- Topbar -->
					<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
						<!-- Sidebar Toggle (Topbar) -->
						<button id="sidebarToggleTop" class="btn btn-link mr-3">
							<i class="fa fa-bars"></i>
						</button>
						<!-- Topbar Navbar -->
						<ul class="navbar-nav ml-auto">
							<!-- Nav Item - Search Dropdown (Visible Only XS) -->
							<li class="nav-item dropdown no-arrow d-sm-none">
								<a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fas fa-search fa-fw"></i>
								</a>
								<!-- Dropdown - Messages -->
								<div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
									<form class="form-inline mr-auto w-100 navbar-search">
										<div class="input-group">
											<input type="text" class="form-control bg-light border-0 small" placeholder="Search for asset" aria-label="Search" aria-describedby="basic-addon2">
											<div class="input-group-append">
												<button class="btn btn-primary" type="button">
													<i class="fas fa-search fa-sm"></i>
												</button>
											</div>
										</div>
									</form>
								</div>
							</li>
							<!-- Nav Item - Alerts -->
							<li class="nav-item dropdown no-arrow mx-1">
								<a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fas fa-bell fa-fw"></i>
									<!-- Counter - Alerts --> @if ($activities[1] > 0) <span class="badge badge-danger badge-counter">{{ $activities[1] }}</span> @endif </a>
								<!-- Dropdown - Alerts -->
								<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
									<h6 class="dropdown-header"> Alerts Center </h6> 
									@foreach ($activities[0] as $act)
									<a class="dropdown-item d-flex align-items-center" href="{{ route('activity.detail',$act->id) }}">
										<div class="mr-3">
											<div class="icon-circle bg-primary">
												<i class="fas fa-file-alt text-white"></i>
											</div>
										</div>
										<div>
											<div class="small text-gray-500">{{ $act->created_at }}</div>
											<span class="font-weight-bold">{{ $act->type.' - '.$act->message }}</span>
										</div>
									</a> 
									@endforeach
									@if ($activities[0]->count() < 1)
									<div class="dropdown-item text-center small text-gray-500 bg-gray-100">
										No activities right now
									</div> 
									@endif
									<a class="dropdown-item text-center small text-gray-500" href="{{ route('activity') }}">Show All Alerts</a>
								</div>
							</li>
							<div class="topbar-divider d-none d-sm-block"></div>
							<!-- Nav Item - User Information -->
							<li class="nav-item dropdown no-arrow">
								<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
									<img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}">
								</a>
								<!-- Dropdown - User Information -->
								<div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
									<a class="dropdown-item" href="{{ route('profile') }}">
										<i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile </a>
									<a class="dropdown-item" href="{{ route('activity') }}">
										<i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log </a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('signOutForm').submit();">
										<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Sign out </a>
									<form id="signOutForm" action="{{ route('logout') }}" method="POST"> @csrf </form>
									</a>
								</div>
							</li>
						</ul>
					</nav>
					<!-- End of Topbar -->
					<!-- Begin Page Content --> 
					@yield('content')
				</div>
				<!-- End of Main Content --> 
				@if (session()->get('condition') == 'Success') 
				<div class="position-fixed p-3" style="z-index: 11">
					<div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
						<div class="toast-header d-flex justify-content-center">
							<i class="rounded me-2 fas fa-check-circle"></i>&nbsp;&nbsp; <strong class="me-auto">{{ session()->get('condition') }}</strong>
							<button id="toastClose" type="button" class="border-0 bg-transparent ml-auto" aria-label="Close">x</button>
						</div>
						<div class="toast-body">
							{{ session()->get('notif') }}
						</div>
					</div>
				</div> 
				@elseif (session()->get('condition') == 'Fails') 
				<div class="position-fixed p-3" style="z-index: 11">
					<div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
						<div class="toast-header d-flex justify-content-center">
							<i class="rounded me-2 fas fa-times-circle"></i>&nbsp;&nbsp; <strong class="me-auto">{{ session()->get('condition') }}</strong>
							<button id="toastClose" type="button" class="border-0 bg-transparent ml-auto" aria-label="Close">x</button>
						</div>
						<div class="toast-body">
							{{ session()->get('notif') }}
						</div>
					</div>
				</div> 
				@else
				@endif
				<!-- Footer -->
				<footer class="sticky-footer bg-transparent">
					<div class="container my-auto">
						<div class="copyright text-center my-auto">
							<span>&copy; Sumicon 2024</span>
						</div>
					</div>
				</footer>
				<!-- End of Footer -->
			</div>
			<!-- End of Content Wrapper -->
		</div>
		<!-- End of Page Wrapper -->
		<!-- Scroll to Top Button-->
		<a id="scrollTop" class="scroll-to-top rounded" href="#page-top">
			<i class="fas fa-angle-up"></i>
		</a>
		<!-- Bootstrap core JavaScript-->
		<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
		<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
		<!-- Core plugin JavaScript-->
		<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
		<!-- Custom scripts for all pages-->
		<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
		<script src="{{ asset('app/app.js') }}"></script>
		<!-- Page level plugins -->
		<script src="{{ asset('js/demo/datatables-demo.js') }}"></script>
		<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
		<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
		<script>
			window.onload = (event) => {
				var toastStart = $('#liveToast');
				toastStart.removeClass('hide');
			}
			$('#toastClose').on('click', () => {
				var toastStart = $('#liveToast');
				toastStart.addClass('hide');
			});
		</script>
	</body>
</html>