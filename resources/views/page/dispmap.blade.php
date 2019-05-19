@extends('layouts.app')

@section('stylesheet')
<link href="/assets/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/assets/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
@endsection

@section('javascript')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key=2342342423424324"></script>
<script src="/assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="/assets/js/plugins/footable/footable.all.min.js"></script>
<script src="/assets/js/plugins/jasny/jasny-bootstrap.min.js"></script>

<!-- M -->
<script src="{{ asset('js/models/map-tooltip.js') }}"></script>
<script src="{{ asset('js/models/map-style.js') }}"></script>
<script src="{{ asset('js/models/map-functions.js') }}"></script>
<script src="{{ asset('js/models/disp-map.js') }}"></script>
@endsection

@section('javascript_content')
	// Стандартный датапикчер
	$('#datadiv').datepicker({
					language: 'ru',
					format: 'yyyy-mm-dd',
					keyboardNavigation: false,
					forceParse: false,
					calendarWeeks: true,
					autoclose: true,
					showToday: true,
					
			});
	// init table module
	$('.footable').footable();
	// Open all table string
	function showAllTabs(){ $('.footable').trigger('footable_expand_all'); }
	// update footeble
	function upFootable(){ $('#cruiseTableOrders').trigger('footable_initialize'); $('#allTableOrders').trigger('footable_initialize'); }
	// google api
	google.maps.event.addDomListener(window, 'load', __construct);
@endsection


@section('content')
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-md-8">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><i class="fa fa-globe"></i> Карта доставки <select id="teritorial">
								<option value="0"{{($territory==0)?' selected' : ''}}> Все </option>
								<option value="1"{{($territory==1)?' selected' : ''}}>Филиал №1 Котовск</option>
								<option value="2"{{($territory==2)?' selected' : ''}}>Филиал №2 Одесса</option>
								<option value="3"{{($territory==3)?' selected' : ''}}>Филиал №3 ***</option>
							</select> на <input type="text" id="datadiv" value="{{ date('Y-m-d') }}">
						</h5> 	
						&nbsp;&nbsp;&nbsp;<a class="btn btn-success btn-facebook btn-xs" onClick="loadAjaxComponents();"><i class="fa fa-map-marker"> </i> Загрузить</a>
					</div>
					<div class="ibox-content">
						<div class="google-map" id="map_canvas"></div>
					</div>
				</div>
			</div>
			
			<div class="col-md-4">
				<div class="tabs-container">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#tab-cruis" aria-expanded="false"><i class="fa fa-road"></i> </a></li>
						<!--<li class=""><a data-toggle="tab" href="#tab-main" aria-expanded="false"><i class="fa fa-area-chart"></i> </a></li>
						<li class=""><a data-toggle="tab" href="#tab-orders" aria-expanded="false"><i class="fa fa-money"></i> </a></li>-->
					</ul>
					<div class="tab-content">
						
						<div id="tab-cruis" class="tab-pane active">
							<div class="panel-body">
								<a class="btn btn-success btn-facebook btn-xs" onClick="changedAllTT();"><i class="fa fa-map-marker"> </i> Показать/скрыть</a>
								<ul class="todo-list m-t small-list" id="cruiseList"></ul>
							</div>
						</div>
						<!--
						<div id="tab-main" class="tab-pane">
							<div class="panel-body">
								Сводка по отгрузке
							</div>
						</div>
						
						<div id="tab-orders" class="tab-pane active">
							<div class="panel-body">
								<input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="Искать в таблице">
								<table class="footable table table-stripped toggle-arrow-tiny" id="cruiseTableOrders" data-filter="#filter">
									<thead><tr>
													<th data-toggle="true">ТТ</th>
													<th>Вес</th>
													<th>ВР</th>
													<th>Адрес</th>
													<th data-hide="all">ТП</th>
													<th data-hide="all">Сумма</th>
													<th>#</th>
									</tr></thead>
									<tbody class="tooltip-demo">
									</tbody>
									<tfoot><tr><td colspan="5"> <ul class="pagination pull-right"></ul> </td> </tr></tfoot>
								</table>
							</div>
						</div>
						-->
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
@endsection
