@extends('layouts.mapdr')

@section('stylesheet')

@endsection

@section('javascript')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key=234234243"></script>
<script src="/assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="/assets/js/plugins/pace/pace.min.js"></script>

<!-- M -->
<script src="{{ asset('js/models/map-tooltip.js') }}"></script>
<script src="{{ asset('js/models/map-style.js') }}"></script>
<script src="{{ asset('js/models/map-functions.js') }}"></script>
<script src="{{ asset('js/models/drivermap.js') }}"></script>
@endsection

@section('javascript_content')
	$('body').addClass('canvas-menu');
	$('body.canvas-menu .sidebar-collapse').slimScroll({height: '100%', railOpacity: 0.9 });
	
	let OrdersList = {!! $CruiseOrderList !!};
	let UsrTerritory = {{ $OTerritory }};
	
	//
	google.maps.event.addDomListener(window, 'load', __construct);
@endsection

@section('content')
	<div id="wrapper">
	
		<nav class="navbar-default navbar-static-side" role="navigation">
			<div class="sidebar-collapse">
				<a class="close-canvas-menu"><i class="fa fa-times"></i></a>
				<ul class="nav metismenu" id="side-menu">
				
					<li class="nav-header"> <div class="dropdown profile-element">
							<a class="dropdown-toggle" href="#"> 
								<span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">Маршрут № {{ $CruiseInfo['id'] }}</strong> </span> </span> 
								<span class="text-muted text-xs block">Список заказов</span> 
							</a>
					</div> <div class="logo-element"><i class="fa fa-sign-out"></i> L</div> </li>
					<!-- Content menu -->
					@if( $CruiseOrderList )
						@foreach ($CruiseOrderList as $order)
							@if ( $order['contragent'] )
								<li> <a onClick="toMarkCord({{ $order['id'] }});"><i class="fa "></i><span class="nav-label" title="Показать на карте"> {{ $order['contragent']['name'] }} </span></a> </li>
							@endif
						@endforeach
					@else
						<li> <a href="#"><i class="fa "></i><span class="nav-label">Нет заказов</span> </a> </li>
					@endif
					
				</ul>
			</div>
		</nav>
		
		<div id="page-wrapper" class="gray-bg">
			<div class="row border-bottom">
				<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
					<div class="navbar-header">
						<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"> <i class="fa fa-bars"></i> </a>
						<form role="search" method="POST" class="navbar-form-custom" action="{{ route('searchForm') }}" onsubmit="return true;">
							{{ csrf_field() }}
							<div class="form-group">
								<input type="text" placeholder="Поиск..." class="form-control" name="top-search" id="top-search">
							</div>
						</form> 
					</div>
					
					<ul class="nav navbar-top-links navbar-left">
						<li> Заказов: <b> {{$CruiseInfo['all_count']}} шт. </b> </li>
						<li> ТТ: <b> {{$CruiseInfo['tt_count']}} шт. </b> </li>
						<li> Вес: <b> {{$CruiseInfo['weith_sum']}} кг. </b> </li>
						<li class="tooltip"> <i class="fa fa-twitch" data-toggle="tooltip" data-placement="bottom" data-original-title="Описание: {{$CruiseInfo['comment']}}"> </i> </li>
						<li class="tooltip"> <i class="fa fa-clock-o" data-toggle="tooltip" data-placement="bottom" data-original-title="Cоставлен: {{$CruiseInfo['created_at']}}"> </i> </li>
						
						<li> <a href="#" onClick="createdDistantionFunc();"><i class="fa fa-globe"></i> Показать маршрут</a> </li>
						<li> Расстояние: <b id="cou_rkm"> {{$CruiseInfo['kmdirect']}} км. </b> </li>
					</ul>
					
					<ul class="nav navbar-top-links navbar-right">
						<li><a href="#" onclick="getUserLocation();"><i class="fa fa-child"></i> Где я?</a> </li>
						<li><input type="checkbox" id="autoUpGPS" onClick="forLocation();"> <label for="autoUpGPS"> Автообновление </label></li>
					</ul>
				</nav>
			</div>
			<div class="row wrapper border-bottom white-bg page-heading">
				
				<!-- Content -->
				<div class="google-map" id="map_canvas"></div>
				
			</div>
			<div class="footer">
				<div class="pull-right"> Серверное время <i id="serverClock">{{ date('d.m.Y H:i:s') }}</i></div>
				<div> <strong>Copyright by <a onClick="window.open('https://joewcode.ru/');" style="cursor:pointer;">Joe</a></strong> Форт-Бир-Плюс &copy; {{ date('Y') }} </div>
			</div>
		</div>
	</div>
@endsection
