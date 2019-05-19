<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- CSRF Token and etc Keys-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="territory-id" content="{{ Auth::user()->territory }}">
	
	<title>Логистика</title>
	
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
	
	@yield('stylesheet')
    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>
<body class="canvas-menu">
	<div id="page-preloader"><span class="spinner"> <i class="fa fa-angellist"></i> Загрузка...</span></div>
	
    <div id="wrapper">
	
		<nav class="navbar-default navbar-static-side" role="navigation">
			<div class="sidebar-collapse">
				<a class="close-canvas-menu"><i class="fa fa-times"></i></a>
				<ul class="nav metismenu" id="side-menu">
					<li class="nav-header">
						<div class="dropdown profile-element">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#"> <span class="clear"> 
								<span class="block m-t-xs"> <strong class="font-bold">{{ Auth::user()->name }}</strong> </span> 
								<span class="text-muted text-xs block">{{ Auth::user()->position }} <b class="caret"></b></span> 
							</span> </a>
							<ul class="dropdown-menu animated fadeInRight m-t-xs">
								<li><a href="/profile">Мой аккаунт</a>
								<li class="divider"></li>
								<li><a href="{{ url('/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Выход</a></li>
							</ul>
						</div>
						<div class="logo-element"> <i class="fa fa-sign-out"></i> JL </div>
					</li>
					<!-- Menu -->
					{!! Menu::get('JNavigate')->asUl( ['class' => 'nav'], ['class'=>'nav nav-second-level'] ) !!}
					<!-- /Menu -->
				</ul>
			</div>
		</nav>

		<div id="page-wrapper" class="gray-bg">
			<div class="row border-bottom">
				<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
					<div class="navbar-header">
						<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
						<form role="search" method="POST" class="navbar-form-custom" action="{{ route('searchForm') }}" onsubmit="return true;">
							{{ csrf_field() }}
							<div class="form-group">
								<input type="text" placeholder="Поиск..." class="form-control" name="top-search" id="top-search">
							</div>
						</form>
					</div>
					<ul class="nav navbar-top-links navbar-right">
						{{-- <!--
						<li class="dropdown">
							<a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
								<i class="fa fa-bell"></i>  <span class="label label-primary">8</span>
							</a>
							<ul class="dropdown-menu dropdown-alerts">
								<li>
									<a href="/profile/alerts/2233">
										<div>
											<i class="fa fa-envelope fa-fw"></i> от --
											<span class="pull-right text-muted small">4 мин. назад</span>
										</div>
									</a>
								</li>
								
								<li>
									<a href="/profile/alerts/2233">
										<div>
											<i class="fa fa-envelope fa-fw"></i> от --
											<span class="pull-right text-muted small">4 мин. назад</span>
										</div>
									</a>
								</li>
								
								<li class="divider"></li>
								<li>
									<div class="text-center link-block">
										<a href="/profile/alerts">
											<strong>Открыть все уведомления</strong>
											<i class="fa fa-angle-right"></i>
										</a>
									</div>
								</li>
							</ul>
						</li>
						--> --}}
						<li> <a href="/faq"> <i class="fa fa-graduation-cap"></i> </a> </li>
						
						<li>
							<a href="{{ url('/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> Выход</a>
						</li>
						
					</ul>
				</nav>
			</div>
			
			<div class="row wrapper border-bottom white-bg page-heading">
				@yield('content')
			</div>
			
			<div class="footer">
				<div class="pull-right"> Серверное время <i id="serverClock">{{ date('d.m.Y H:i:s') }}</i> </div>
				<div> <strong>Copyright by <a onClick="window.open('https://joewcode.ru/');" style="cursor:pointer;">Joe</a></strong> 000 &copy; {{ date('Y') }} </div>
			</div>
		</div>
	</div>
	
	<!-- FORM -->
	<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
	
    <!-- Mainly scripts -->
    <script src="{{ asset('assets/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
	<script src="{{ asset('assets/js/plugins/notify/bootstrap-notify.min.js') }}"></script>
	<script src="{{ asset('assets/js/plugins/sweetalert/sweetalert.min.js') }}"></script>
	@yield('javascript')
    <!-- Custom and plugin javascript -->
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/pace/pace.min.js') }}"></script>
	
    <script>
		// app
		@yield('javascript_content')
    </script>
</body>
</html>
