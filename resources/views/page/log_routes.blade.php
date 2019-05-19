@extends('layouts.app')

@section('stylesheet')
<link href="/assets/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/assets/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
@endsection

@section('javascript')
<script src="/assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="/assets/js/plugins/footable/footable.all.min.js"></script>

<!-- M -->
<script src="{{ asset('js/models/log-routes.js') }}"></script>

@endsection

@section('javascript_content')

	$(document).ready(function() {
		// init
		__construct();
		
		$('.footable').footable();
		
		$('#date_added').datepicker({
			language: 'ru',
			format: 'yyyy-mm-dd',
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true
		});
		
		// Применить фильтры
		$('#optionFilter').click(function(){
			var stt = {'0':'Запланирован', '1':'Отгружен в УДК'};
			var DTtime = $('#date_added').val();
			var cStats = $('#status').val();
			if ( cStats && cStats != '99' ) $('.footable').trigger('footable_filter', {filter: stt[cStats]});
			else if ( DTtime ) $('.footable').trigger('footable_filter', {filter: DTtime});
			else $('.footable').trigger('footable_clear_filter');
		});
		
	});
	
@endsection

@section('content')
		<div class="ibox">
			<div class="ibox-content">
				<div class="col-sm-4">
					<div class="form-group"> <label class="control-label" for="filter">*Живой фильтр</label>
						<input type="text" id="filter" name="filter" placeholder="Найти в таблице совпадения ключевых слов" class="form-control" />
					</div>
				</div>
				
				<div class="col-sm-3">
					<div class="form-group"> <label class="control-label" for="date_added">Дата отгрузки</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<input id="date_added" type="text" class="form-control" value="{{ date('Y-m-d') }}" />
						</div>
					</div>
				</div>
					
				<div class="col-sm-3">
					<div class="form-group"> <label class="control-label" for="status">Статус рейса</label>
						<select name="status" id="status" class="form-control">
							<option value="99" selected>Все</option>
							<option value="0">Запланирован</option>
							<option value="1">Отгружен в УДК</option>
						</select>
					</div>
				</div>
				
				<div class="col-sm-2 row">
					<div class="form-group"> <label class="control-label" for="optionFilter"> &nbsp; </label>
						<button class="form-control btn-danger btn btn-xs" id="optionFilter"> <i class="fa fa-option"></i> Применить фильтр</button> 
					</div>
				</div>
				
			</div>
		</div>
		
		<div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
							<table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15" data-filter="#filter">
								<thead> <tr>
									<th data-toggle="true">Авто</th>
									<th>Карта</th>
                                    <th data-hide="phone">Вес</th>
                                    <th data-hide="phone,tablet">Сумма</th>
                                    <th data-hide="phone">Пробег</th>
									<th data-hide="phone">На дату</th>
									<th data-hide="phone,tablet">Комментарий</th>
									<th>Статус</th>
                                    <th class="text-right" data-sort-ignore="true">Опции</th>
                                </tr> </thead>
                                <tbody>
								@foreach ($cruise_list as $session)
									@if ($session->cruises)
										@foreach ($session->cruises as $list)
										<tr id="rtRow_{{$list->id}}">
											<td>{{ $list->name_auto }}</td>
											<td><a href="/map/{{ $list->id }}" target="_blank"><i class="fa fa-link"></i> {{ $list->id }}</a></td>
											<td>{{ $list->weith_sum }} кг</td>
											<td>{{ $list->summa_sum }} грн</td>
											<td>{{ $list->kmdirect }} км </td>
											<td>{{ substr($session->session_todate, 0, 10) }}</td>
											<td>{{ $list->comment }}</td>
											<td> <span class="label label-{{($list->status_auto) ? 'success' : 'danger'}}">{{($list->status_auto) ? 'Отгружен в УДК' : 'Запланирован'}}</span> </td>
											<td class="text-right">
												<div class="btn-group">
													@if ( $list->status_auto == 0 )
														<button class="btn-danger btn btn-xs" onClick="LoadXMLCruise({{$list->id}});"> <i class="fa fa-save"></i> Загрузить XML </button>
													@endif
													@if ( $list->status_auto >= 1 )
														<button class="btn-success btn btn-xs" onClick="openMapCruise({{$list->id}});"> <i class="fa fa-location-arrow"></i> Карта </button> 
													@endif
													<!--<button class="btn-white btn btn-xs" onClick="editorCruise({{$list->id}});"> <i class="fa fa-wrench"></i> Изменить </button>-->
													<button class="btn-danger btn btn-xs" onClick="butDeleteCruise({{$list->id}});"> <i class="fa fa-trash"></i> Удалить </button>
												</div>
											</td>
										</tr>
										@endforeach
									@endif
								@endforeach
								</tbody>
                                <tfoot><tr> <td colspan="6"> <ul class="pagination pull-right"></ul> </td> </tr></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
		</div>
		
@endsection