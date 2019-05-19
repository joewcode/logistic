@extends('layouts.app')

@section('stylesheet')

@endsection

@section('javascript')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=visualization&key=234234234243" async defer></script>
<script src="/assets/js/plugins/chartJs/Chart.min.js"></script>
<script src="/assets/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<!-- M -->
<script src="{{ asset('js/models/map-style.js') }}"></script>
<script src="{{ asset('js/models/map-functions.js') }}"></script>
<script src="{{ asset('js/models/home.js') }}"></script>
@endsection

@section('javascript_content')

        $(document).ready(function() {
			//
			var dlOptions = {!! $stats !!};
			
			//
            var ctx = document.getElementById("lineChart").getContext("2d");
            new Chart(ctx, {type: 'line', data: getDataLine(dlOptions), options: {responsive: true}});
			
			// google api
			google.maps.event.addDomListener(window, 'load', __construct);
        });
		
@endsection

@section('content')
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-8">
				<div class="ibox float-e-margins">
					<div class="ibox-content">
						<div> <h1 class="m-b-xs">Статистика логистики {{ $territory($UTerritory) }}</h1> </div>
						<div> <canvas id="lineChart" height="100"></canvas> </div>
						<div class="m-t-md">
							<small class="pull-right"><i class="fa fa-clock-o"> </i> Обновлено {{date('d.m.Y')}}</small>
							<small> <strong>*</strong> Данные статистики формируются на основе активности вашего региона. </small>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-lg-4">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Мои задачи</h5>
						<div class="ibox-tools"> <a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a> </div>
					</div>
					<div class="ibox-content">
						<form class="form-horizontal" method="POST" action="{{ route('createBP') }}" role="form">
							{{ csrf_field() }}
							<div class="input-group">
								<input type="text" class="form-control" id="newtask" name="newtask">
								<span class="input-group-btn"> <button type="submit" class="btn btn-primary">Добавить</button> </span>
							</div>
						</form>
						<ul class="todo-list m-t small-list">
						@foreach ($busblan as $res)
							<li> <a href="#" onClick="chengedStatus({{ $res->id }});" class="check-link"><i class="fa fa-{{ !$res->status ? 'square-o' : 'check-square' }}"></i> </a>
								<span class="m-l-xs {{ $res->status ? ' todo-completed' : null }}">{{ $res->text }}</span>
								@if ( $res->status == 1 ) 
									<small class="label label-primary"><i class="fa fa-clock-o"></i> зав-но {{ $res->updated_at }}</small> 
								@else
									<small class="label label-primary"><i class="fa fa-clock-o"></i> доб-но {{ $res->created_at }}</small> 
								@endif
							</li>
						@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-lg-12"><h1>Интенсивность заказов по регионам</h1>
				<div class="google-map" id="map"></div>
			</div>
		</div>
		
	</div>
@endsection
