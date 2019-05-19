@extends('layouts.app')

@section('stylesheet')
<link href="/assets/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/assets/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
@endsection

@section('javascript')
<script src="/assets/js/plugins/footable/footable.all.min.js"></script>
<script src="/assets/js/plugins/jasny/jasny-bootstrap.min.js"></script>

@endsection

@section('javascript_content')
        $(document).ready(function() {
			// Таблички
			$('.footable').footable();
			
			// Запуск формы отправки создания сессию доставки
			$('#CreatedUser').click(function(){ $('#loadForm').submit(); });
			$('#saveEditUser').click(function(){ $('#loadForm2').submit(); });
			$('#deletedUser').click(function(){ if(confirm('Вы уверены что хотите удалить данного пользователя? Операция необратима!')) {
					setDelUser();
				} });
			
		});
		
		//
		function userEdit(id){
			$.get('/adm/users/'+id+'/get', {userid:id}, function(r){
				console.log(r.id);
				$('#euserid').val(r.id);
				$('#efullname').val(r.name);
				$('#eusermail').val(r.email);
				$('#euserpos').val(r.position);
				$('#eteritorial').val(r.territory);
				$('#eactivated').attr('checked', r.status);
				$('#eadministrator').attr('checked', r.admin);
				$('#EditUser').modal('toggle');
			});
		}

@endsection


@section('content')
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Пользователи программы</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
								<a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-wrench"></i> </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#" data-toggle="modal" data-target="#CreateUser"> Добавить пользователя</a></li>
                                </ul>
                            </div>
					</div>
					<div class="ibox-content">
						<input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="Искать в таблице">
						<table class="footable table table-stripped toggle-arrow-tiny" data-filter="#filter">
							<thead><tr>
                                    <th data-toggle="true">ФИО</th>
									<th>Роль</th>
                                    <th>Почта</th>
                                    <th data-hide="all">Был активен</th>
									<th data-hide="all">Территориальное подразделение</th>
									<th data-hide="phone">Активация</th>
									<th data-hide="phone">Привелегии</th>
                                    <th>Опцыи</th>
							</tr></thead>
							<tbody>
							@foreach ($users_all as $usr)
									<tr>
										<td>{{$usr->name}}</td>
										<td>{{$usr->position}}</td>
										<td>{{$usr->email}}</td>
										<td>{{$usr->lastaction}}</td>
										<td>{{$territory($usr->territory)}}</td>
										<td>{{$usr->status ? 'Активирован' : 'Заблокирован'}}</td>
										<td>{{$usr->admin ? 'Администратор' : 'Пользователь'}}</td>
										<td><a onClick="userEdit({{$usr->id}});"><i class="fa fa-check text-navy"></i></a></td>
									</tr>
							@endforeach
							</tbody>
							<tfoot>
                                <tr> <td colspan="5"> <ul class="pagination pull-right"></ul> </td> </tr>
							</tfoot>
						</table>
					</div>
				</div>
				
			</div>
		</div>
		
		
			
		<div class="modal inmodal fade" id="CreateUser" tabindex="-1" role="dialog"  aria-hidden="true">
			<div class="modal-dialog modal-lg"><div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
					<h4 class="modal-title">Создать нового пользователя</h4>
					<small class="font-bold">*Сгенерированный пароль будет отправлен на почту пользователя.</small>
				</div>
				<div class="modal-body"><form class="m-t" role="form" method="POST" id="loadForm" action="{{ url('/adm/users') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label class="font-normal">Фамилия Имя Отчество</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-address-book"></i></span><input type="text" name="fullname" class="form-control" value="">
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Почта пользователя</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-at"></i></span><input type="text" name="usermail" class="form-control" value="">
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Роль пользователя (должность)</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-superpowers"></i></span><input type="text" name="userpos" class="form-control" value="">
						</div>
					</div>
					
				</form></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
					<button type="button" class="btn btn-primary" id="CreatedUser">Создать пользователя</button>
				</div>
			</div></div>
		</div>
		
		
		<div class="modal inmodal fade" id="EditUser" tabindex="-1" role="dialog"  aria-hidden="true">
			<div class="modal-dialog modal-lg"><div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
					<h4 class="modal-title">Редактирование</h4>
				</div>
				<div class="modal-body"><form class="m-t" role="form" method="POST" id="loadForm2" action="{{ url('/adm/users/edit') }}" enctype="multipart/form-data">
					{{ csrf_field() }}  <input type="hidden" name="euserid" id="euserid" class="form-control" value="">
					<div class="form-group">
						<label class="font-normal">Фамилия Имя Отчество</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-address-book"></i></span><input type="text" name="efullname" id="efullname" class="form-control" value="">
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Почта пользователя</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-at"></i></span><input type="text" name="eusermail" id="eusermail" class="form-control" value="">
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Роль пользователя (должность)</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-superpowers"></i></span><input type="text" name="euserpos" id="euserpos" class="form-control" value="">
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Изменить пароль пользователя на:</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-superpowers"></i></span><input type="text" name="enewpasswd" id="enewpasswd" class="form-control" value="">
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Территориальное подразделение</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-home"></i></span>
							<select class="form-control" name="eteritorial" id="eteritorial">
								<option value="0"> </option>
								<option value="1">Филиал №1 Котовск</option>
								<option value="2">Филиал №2 Одесса</option>
								<option value="3">Филиал №3 ***</option>
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label class="font-normal">Опции</label>
						<div class="input-group date"> <label> <input type="checkbox" name="eactivated[]" id="eactivated" value=""> Пользователь активирован </label> </div>
						<div class="input-group date"> <label> <input type="checkbox" name="eadministrator[]" id="eadministrator" value=""> Администратор ПО </label> </div> 
					</div>
					
				</form></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
					<button type="button" class="btn btn-white" id="deletedUser">Удалить пользователя</button>
					<button type="button" class="btn btn-primary" id="saveEditUser">Применить</button>
				</div>
			</div></div>
		</div>

		
@endsection