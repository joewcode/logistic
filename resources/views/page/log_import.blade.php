@extends('layouts.app')

@section('stylesheet')
<link href="/assets/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/assets/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/plugins/datapicker/datepicker3.css" rel="stylesheet">

@endsection

@section('javascript')
<script src="/assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="/assets/js/plugins/footable/footable.all.min.js"></script>
<script src="/assets/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<!-- M -->
<script src="{{ asset('js/models/log-import.js') }}"></script>
@endsection

@section('javascript_content')
		$(document).ready(function() {
			// init
			__construct();
			
			// Таблички
			$('.footable').footable();
			
			// Стандартный датапикчер
			$('#data_1 .input-group.date').datepicker({
					language: 'ru',
					format: 'yyyy-mm-dd',
					keyboardNavigation: false,
					forceParse: false,
					calendarWeeks: true,
					autoclose: true,
					showToday: true,
					daysOfWeekDisabled: [0],
			});
			
			// Запуск формы отправки создания сессию доставки
			$('#CreatedSess').click(function(){ $('#loadForm').submit(); });
			
			// Форма редактирования сессии доставки
			$('.butEditSessionDistr').click(function(){ butEdit_distrLoad(this.id); });
			
		});
@endsection

@section('content')
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Импорт заказов c УДК</h5>
						<div class="ibox-tools">
                                <a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
								<a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-wrench"></i> </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#" data-toggle="modal" data-target="#CreateSessionModal"> Добавить сессию</a></li>
                                </ul>
						</div>
					</div>
					<div class="ibox-content">
						<table class="footable table table-stripped toggle-arrow-tiny">
							<thead><tr>
                                    <th data-toggle="true">На дату</th>
									<th>Комментарий</th>
                                    <th>Кол-во заказов</th>
                                    <th>Вес заказов</th>
                                    <th data-hide="all">Автор</th>
                                    <th data-hide="all">Импортировано ТТ</th>
									<th data-hide="all">UPS Заказов</th>
									<th data-hide="all">Количество авто</th>
									<th data-hide="all">Тонаж авто x1</th>
									<th>Сумма развоза</th>
									<th data-hide="all">Филиал</th>
									<th data-hide="all">Время загрузки</th>
									<th data-hide="all">Коэфициент сложности</th>
                                    <th>Опцыи</th>
							</tr></thead>
							<tbody>
								@foreach ($distrSessionList as $sess)
									<tr id="sdlRow_{{$sess->id}}">
										<td>{{ $sess->session_todate }}</td>
										<td>{{ $sess->author_comment }}</td>
										<td>{{ $sess->import_count_orders }} шт</td>
										<td>{{ $sess->import_count_wieght }} кг</td>
										<td><a href="#" >{{ $sess->name }}</a></td>
										<td>{{ $sess->import_count_outlets }} шт</td>
										<td>{{ $sess->import_count_ups }} шт</td>
										<td>{{ $sess->import_count_cars }} шт</td>
										<td>{{ $sess->import_count_cars_wieght }} кг</td>
										<td>{{ $sess->import_count_money }} грн.</td>
										<td>{{ $sess->session_teritorial }}</td>
										<td>{{ $sess->created_at }}</td>
										<td>{{ $sess->coefficient }}</td>
										<td> <a class="btn btn-danger btn-facebook btn-xs" onClick="butDelete_distrLoad({{$sess->id}});"><i class="fa fa-trash"> </i> Удалить</a> </td>
									</tr>
								@endforeach
							</tbody>
							<tfoot>
                                <tr>
                                    <td colspan="5">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
							</tfoot>
						</table>
					</div>
				</div>
				
				
			</div>
		</div>
		
		
		
		<div class="modal inmodal fade" id="CreateSessionModal" tabindex="-1" role="dialog"  aria-hidden="true">
			<div class="modal-dialog modal-lg"><div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
					<h4 class="modal-title">Создание сессии доставки</h4>
					<small class="font-bold">Задайте настройки сесси и загрузите 2Out.txt с УДК</small>
				</div>
				<div class="modal-body"><form class="m-t" role="form" method="POST" id="loadForm" action="{{ url('/log/import') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="fileinput fileinput-new input-group" data-provides="fileinput">
						<div class="form-control" data-trigger="fileinput">
							<i class="fa fa-file fileinput-exists"></i><span class="fileinput-filename"></span>
						</div>
						<span class="input-group-addon btn btn-default btn-file">
							<span class="fileinput-new"> Загрузить </span>
							<span class="fileinput-exists">Изменить</span>
							<input type="file" name="outfile" id="outfile" />
						</span>
						<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Удалить</a>
					</div>
					
					<div class="form-group" id="data_1">
						<label class="font-normal">Установите дату доставки</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" name="datadeliveri" class="form-control" value="{{ date('Y-m-d') }}">
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Территориальное подразделение</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-globe"></i></span>
							<select class="form-control" name="teritorial">
								<option value="0"> </option>
								<option value="1"{{($uTeritorial==1)?' selected' : ''}}>Филиал №1 Котовск</option>
								<option value="2"{{($uTeritorial==2)?' selected' : ''}}>Филиал №2 Одесса</option>
								<option value="3"{{($uTeritorial==3)?' selected' : ''}}>Филиал №3 ***</option>
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Комментарий к импорту</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-book"></i></span><input type="text" name="comment" class="form-control" value="">
						</div>
					</div>
				</form></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
					<button type="button" class="btn btn-primary" id="CreatedSess">Создать сессию</button>
				</div>
			</div></div>
		</div>
		
		
		
@endsection