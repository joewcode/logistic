@extends('layouts.app')

@section('stylesheet')
<link href="/assets/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/assets/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/plugins/select2/select2.min.css" rel="stylesheet">
<link href="/assets/css/plugins/touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet">

@endsection

@section('javascript')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key=234234234"></script>
<script src="/assets/js/plugins/footable/footable.all.min.js"></script>
<script src="/assets/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="/assets/js/plugins/select2/select2.full.min.js"></script>
<script src="/assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js"></script>

<!-- M -->
<script src="{{ asset('js/models/map-tooltip.js') }}"></script>
<script src="{{ asset('js/models/map-style.js') }}"></script>
<script src="{{ asset('js/models/map-functions.js') }}"></script>
<script src="{{ asset('js/models/class/log-orders.js') }}"></script>
<script src="{{ asset('js/models/class/log-autopark.js') }}"></script>
<script src="{{ asset('js/models/log-constructor.js') }}"></script>
@endsection

@section('javascript_content')
	// init table module
	$('.footable').footable();
	$('#savestatusHTM').hide();
	$('#markerCords').hide();
	
	// Open all table string
	function showAllTabs(){ $('.footable').trigger('footable_expand_all'); }
	
	// update footeble
	function upFootable(all = 1) {
		$("#upFooBut").prop('disabled', true);
		$('#cruiseTableOrders').trigger('footable_initialize'); 
		$('#cruiseTableList').trigger('footable_initialize'); 
		if(all) $('#allTableOrders').trigger('footable_initialize'); 
		$("#upFooBut").prop('disabled', false);
	}
	
	// google api
	google.maps.event.addDomListener(window, 'load', __construct);
	
	// load ajax
	$('#loadConstrSess').click(function(){ 
		var sID = parseInt( $('#usesList').val() );
		if ( sID > 0 ) return loadingSession( sID ); 
		else errHelper(13);
	});
	
	// module select2
	$("#autoparkList").select2({containerCssClass: "error", dropdownCssClass: "test"});
	
	// Touch Spin
	$("#optemizeInput").TouchSpin({
			min: 100, max: 5000, step: 20,
			buttondown_class: 'btn btn-white',
			buttonup_class: 'btn btn-white'
	});
	
@endsection

@section('content')
	<div class="row-fluid">
		<div class="col-md-8">
				<div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5><i class="fa fa-globe"></i> Карта </h5>
							<h5>&nbsp;&nbsp;На дату: <val id="dataDeli">не загружено</val></h5>
							
							<h5 class="">&nbsp;&nbsp;&nbsp;<i class="fa fa-beer"></i>&nbsp;Остаток, накл: <val id="statCouOrd">0</val> шт, вес: <val id="statTonn">0</val> кг.</h5>
							
							<div class="ibox-tools">
								<a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-wrench"></i> </a>
                                <ul class="dropdown-menu dropdown-user">
								<!--
									<li><a href="#" onClick="alert(1);"><i class="fa fa-cloud-upload"></i> Запомнить модель маршрута</a></li>
									<li><a href="#" onClick="alert(1);"><i class="fa fa-cloud-download"></i> Восстановить модель маршрута</a></li>
									
									<li role="separator" class="divider"></li> -->
									<li><a href="#" data-toggle="modal" data-target="#sessionConnect"><i class="fa fa-download"></i> Подключить сессию доставки</a></li>
                                </ul>
                            </div>
						</div>
                        <div class="ibox-content">
                            <div class="google-map" id="map_canvas"></div>
                        </div>
				</div>
		</div>
		
		<div class="col-md-4">
				<div class="ibox float-e-margins">
					<div class="ibox-title row">
						<strong><i class="fa fa-truck"></i> Авто </strong>
						<select class="select2-selection__rendered" id="autoparkList" onChange="autoparkOnChange(this);">
							<option value="0">Нет</option>
							<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
						</select>
						<button class="btn btn-info btn-xs" type="button" onClick="autoparkInfo();" id="InFooBut" title="Информация о автопарке"> <i class="fa fa-info"></i> </button>
					</div>
					<div class="ibox-content"> <i class="btn btn-primary" onClick="copyTextInDiv('markerCords');" id="markerCords"></i>
                            <p>Груз-сть: <b id="carM">0</b> кг. <i>Гос. номер: <b id="carN"> не известно</b></i></p>
							<p>Загружено: <b id="carT">0</b> кг. Накл.: <b id="carTT">0</b> шт., <i>на <b id="carGRN">0</b> грн.</i></p>
							<input type="text" class="form-control" id="cruiseComment" value="" placeholder="Комментарий маршрута, вводить при записи рейса." />
							<div class="row">
								<div class="col-md-4">
									<button class="form-control btn btn-success btn-xs" onClick="cruiseCreatedCurrentBut();"><i class="fa fa-save"></i> Записать </button>
								</div>
								<div class="col-md-4">
									<button class="form-control btn btn-info btn-xs" onClick="createCruise();"><i class="fa fa-soundcloud"></i> Сохранить </button>
								</div>
								<div class="col-md-4">
									<button class="form-control btn btn-danger btn-xs" onClick="clearDriverOrders(0, 1);"><i class="fa fa-trash"></i> Очистить </button>
								</div>
							</div>
							<h3 id="savestatusHTM" class="text-danger">Маршрут не сохранен!</h3>
					</div>
				</div>
				
				<div class="tabs-container">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#tab-map" aria-expanded="false" title="Карта маршрута"> <i class="fa fa-map-marker"></i> Маршрут </a></li>
						<li class=""><a data-toggle="tab" href="#tab-orders" aria-expanded="false" title="Заказы в маршруте"> <i class="fa fa-money"> ТТ</i> </a></li>
						<li class=""><a data-toggle="tab" href="#tab-cruis" aria-expanded="false" title="Все рейсы сессии"> <i class="fa fa-road"></i> Все рейсы</a></li>
						<li class=""><a class="btn-xs" onClick="upFootable();" id="upFooBut" title="Обновить табличные части"> <i class="fa fa-recycle"></i> </a></li>
					</ul>
					<div class="tab-content">
						
						<div id="tab-map" class="tab-pane active">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-5"><input class="form-control" type="text" value="200" id="optemizeInput" style="display: block;" title="Радиус оптимизации в метрах"></div>
									<div class="col-md-3"><button type="button" class="btn btn-outline btn-success" onClick="tocreateDirection();" title="Рассчитать маршрут с помощью сервисов Google"><i class="fa fa-globe"></i> км?</button></div>
									<div class="col-md-4"><div class="form-control" id="car_kilometr">0 км</div></div>
								</div>
								<div class="google-map" id="DrvMAPdiv"></div>
							</div>
						</div>
						
						<div id="tab-orders" class="tab-pane active">
							<div class="panel-body">
								<input type="text" class="form-control input-sm m-b-xs" id="filter2" placeholder="Искать в таблице">
								<table class="footable table table-stripped toggle-arrow-tiny" id="cruiseTableOrders" data-filter="#filter2">
									<thead><tr>
										<th data-toggle="true">ТТ</th>
										<th>Вес</th>
										<th>#</th>
										<th data-hide="all">ВР</th>
										<th data-hide="all">Адрес</th>
										<th data-hide="all">ТП</th>
										<th data-hide="all">Сумма</th>
									</tr></thead>
									<tbody class="tooltip-demo"></tbody>
									<tfoot><tr><td colspan="5"> <ul class="pagination pull-right"></ul> </td> </tr></tfoot>
								</table>
							</div>
						</div>
						
						
						
						<div id="tab-cruis" class="tab-pane active">
							<div class="panel-body">
								<ul class="todo-list m-t small-list" id="5cruiseList">
									
									<table class="footable table table-stripped toggle-arrow-tiny" id="cruiseTableList">
										<thead><tr>
											<th data-toggle="true">Авто</th>
											<th>Вес</th>
											<th data-hide="all">Сумма</th>
											<th>Расстояние</th>
											<th data-hide="all">Карта</th>
											<th>Состояние</th>
											<th data-hide="all">Коммент.</th>
										</tr></thead>
										<tbody>
										</tbody>
										<tfoot><tr><td colspan="5"> <ul class="pagination pull-right"></ul> </td> </tr></tfoot>
									</table>
									
								</ul>
							</div>
						</div>
						
						<!--<div id="tab-stats" class="tab-pane">
							<div class="panel-body" id="all_statistic">
								<div class="col-md-6"> 
									<p> Доступно авто: <b id="stt1">0</b> шт. </p>
									<p> Вес отгр. (х1 рейс): <b id="stt2">0</b> кг. </p>
									<p> Всего заказов: <b id="stt3">0</b> шт. </p>
									<p> Общий тоннаж: <b id="stt4">0</b> кг. </p>
									
								</div>
								<div class="col-md-6"> 
									<p> Задействовано авто: <b id="sttr1">0</b> шт. </p>
									<p> Кол-во отгр. ТМЦ: <b id="sttr2">0</b> шт. </p>
									<p> Вес отгр. ТМЦ: <b id="sttr3">0</b> кг. </p>
									<p> Общее расстояние: <b id="sttr4">0</b> км. </p>
									
								</div>
								
								<div class="m-t-md pull-right"> <small> <strong>*</strong> Тест. </small> </div>
							</div>
						</div>-->
						
					</div>
				</div>
		</div>
	</div>
		<div class="col-md-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Заказы в сессии (без маршрута)</h5>
						<div class="ibox-tools">
							<a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
							<a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-wrench"></i> </a>
							<ul class="dropdown-menu dropdown-user">
									<li><a href="#" onClick="showAllTabs();">Отобразить все элементы таблицы</a></li>
							</ul>
						</div>
					</div>
					<div class="ibox-content">
						<input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="Искать в таблице">
						<table class="footable table table-stripped toggle-arrow-tiny" id="allTableOrders" data-filter="#filter">
							<thead><tr>
									<th data-toggle="true">ЧП</th>
									<th>Вес</th>
									<th>Часы работы</th>
									<th>Адрес</th>
									<th data-hide="all">Коммен. ТП</th>
									<th data-hide="all">Сумма накладной</th>
									<th>#</th>
							</tr></thead>
							<tbody></tbody>
							<tfoot><tr><td colspan="5"> <ul class="pagination pull-right"></ul> </td> </tr></tfoot>
						</table>
					</div>
				</div>
		</div>
			
			
			<div class="modal inmodal" id="sessionConnect" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
				<div class="modal-dialog">
					<div class="modal-content animated fadeIn">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
							<i class="fa fa-beer modal-icon"></i>
						</div>
						<div class="modal-body">
							<p>Выбрать сессию доставки:</p>
							<select class="form-control" id="usesList">
								<option value="0"></option>
								@foreach($user_sessions as $r)
									<option value="{{$r->id}}">{{$r->session_todate}} ( {{$r->author_comment}} )</option>
								@endforeach
							</select>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
							<button type="button" class="btn btn-primary" data-dismiss="modal" id="loadConstrSess">Загрузить</button>
						</div>
					</div>
				</div>
			</div>
			
			{{-- <div class="modal inmodal" id="sessionSave" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
				<div class="modal-dialog">
                                    <div class="modal-content animated fadeIn">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                            <i class="fa fa-beer modal-icon"></i>
                                           
                                        </div>
                                        <div class="modal-body">
											<p>Чуть позже</p>
										
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
                                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="loadCo">Сохранить</button>
                                        </div>
                                    </div>
				</div>
			</div> --}}

			<div class="modal fade" id="autoparkDiv" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog"> <div class="modal-content animated fadeIn"> <div class="modal-body">
					<p>Автопарк</p>

                </div> </div> </div>
			</div>
	
@endsection
