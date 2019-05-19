@extends('layouts.app')

@section('stylesheet')
<link href="/assets/css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
<link href="/assets/css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">
@endsection

@section('javascript')
<script src="/assets/js/plugins/jqGrid/grid.locale-ru.js"></script>
<script src="/assets/js/plugins/jqGrid/jquery.jqGrid.min.js"></script>
<script src="/assets/js/plugins/jquery-ui/jquery-ui.min.js"></script>
@endsection

@section('javascript_content')
	$(document).ready(function() {
		// Конфигурация таблички
		$("#PhonebookTable").jqGrid({
			url: '/disp/pbook/get',
			mtype: "POST",
			postData: {'_token': csrftoken},
			datatype: "json",
			height: 500,
			width: $(this).width(),
		//	autowidth: true,
			shrinkToFit: true,
			rowNum: 20,
			rowList: [20, 40, 60],
			// id fio text territory descr updated_at
			colNames:['#', 'Ф.И.О.','Телефоны', 'Описание', 'Филиал', 'Обновлено'],
			colModel:[
				{name:'id', index:'id', editable:false, width:30, align:"center", sorttype:"int", search:false, sortable:false},
				{name:'fio', index:'fio', editable:true, width:150, edittype:"text", sortable:true, search:true},
				{name:'text', index:'text', editable:true, width:130, edittype:"textarea", editoptions:{rows:"5",cols:"60"}},
				{name:'descr', index:'descr', editable:true, width:120, align:"left"},
				{name:'territory', index:'territory', editable:true, width:100, align:"center", edittype:"select", editoptions:{value:"0:Нет;1:Филиал №1;2:Филиал №2;3:Филиал №3"}},
				{name:'updated_at', index:'updated_at', editable:false, width:60, align:"center", sorttype:"date"}
			],
			pager: "#PhonebookNav",
			iconSet: "fontAwesome",
			viewrecords: true,
			loadonce: false,
			caption: "Справочник ФБП (общий)",
			add: true,
			edit: true,
			addtext: 'Добавить',
			edittext: 'Изменить',
			hidegrid: false,
			editurl: '/disp/pbook/edit'
		});
		
		// Скрыть идентификатор
		$('#PhonebookTable').hideCol('id');
		
		// Кнопки настроек
		$("#PhonebookTable").jqGrid('navGrid', '#PhonebookNav', {edit: true, add: true, del: true, search: true},
			{width: 500, height: 270, recreateForm: true, closeAfterEdit: true, reloadAfterSubmit: true, savekey: [true, 13]},
			{width: 500, height: 270, recreateForm: true, closeAfterAdd: true},
			{width: 260, height: 130},
			{width: 380, height: 150, multipleSearch: true, searchOnEnter: true, searchOperators: true}
		);
		
	//	$("#PhonebookTable").jqGrid('filterToolbar', {autosearch: true});
		
		// Слушаем
		$(window).bind('resize', function () { var width = $('.jqGrid_wrapper').width(); $('#PhonebookTable').setGridWidth(width); });
		
		// Добавить выбор
	//	$("#PhonebookTable").setSelection(1, true);
	});
	
@endsection

@section('content')
	<div class="ibox"> 
		<div class="ibox-content"> 
			<div class="jqGrid_wrapper">
				<table id="PhonebookTable" class="table"></table>
				<div id="PhonebookNav"></div>
			</div> 
			<div class="m-t-md">
				<small class="pull-right"><i class="fa fa-handshake-o"> </i> Быстро и удобно! </small>
				<small><strong>*</strong> Наиболее читаемый формат номера телефона: 099-000-00-00.</small>
			</div>
		</div> 
	</div>
@endsection
