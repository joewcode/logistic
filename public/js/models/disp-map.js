'use strict';
let google_map;				// Глобальная карта google
let baseCoordinate;			// Хранит LatLng google map объекта местоположение базы
let DirService; 			// 
let DirDisplay;				//
let MarkersAll = {};		// Маркера
let OrdersList = {};		//
let CruiseList = {};		//
let ChGlobStat = false;		//

// Init function
function __construct() {
	try {
		let terr = getTerritory(territoryID);
		baseCoordinate = new google.maps.LatLng(terr[0], terr[1]);
		let mapOptions = {	zoom: 9, center: baseCoordinate, mapTypeId: google.maps.MapTypeId.ROADMAP, zoomControl: true, mapTypeControl: true, scaleControl: false, streetViewControl: false, rotateControl: false, styles: map_Options };
		
		// Запускаем карты
		google_map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		
		// Подключаем службу просчета маршрута для карты рейса
		DirService = new google.maps.DirectionsService();
		DirDisplay = new google.maps.DirectionsRenderer({map: google_map, suppressMarkers: true});
		
		// Добавить иконки склада на карты
		let merker = new google.maps.Marker({ position:baseCoordinate, icon:'/assets/img/ico/base32.png', map: google_map, title: 'Склад ФБП', id: 'skladIcon', draggable: false });
		merker.setMap( google_map );
		
	} catch (e) { console.log(e); }
}

// Обрабатываем маркера и тд
function initToMapOrders() {
	let count = OrdersList.length;
	if ( count > 0 ) {
		for ( var i in OrdersList ) {
			addMarker( OrdersList[i] );
		}
	}
	else console.log('Нет заказов в рейсе.');
}

// Обработка списка рейсов..
function initToInformation() {
	let count = CruiseList.length;
	if ( count > 0 ) {
		for ( let i in CruiseList ) {
			addCruise(CruiseList[i]);
		}
	}
	else console.log('Нет заказов в рейсе.');
}

// Добавить на карту маркер (latlng, ico, titl, uniqueID, drag = true, mapID = 1) {
function addMarker ( order ) { 
	if ( order.contragent == null ) {
		console.log('Uncnown contragent, order ID: '+order.id);
		return false;
	}
	let cord = new google.maps.LatLng(order.contragent.shirota, order.contragent.dolgota);
	let title = '<h5>'+order.contragent.name+'</h5><b>'+order.weith+'</b> кг, с <b>'+order.contragent.nachalo+'</b> по <b>'+order.contragent.konec+'</b><br>'+order.contragent.addresses+'<br><b>'+order.koment+'</b> ТП: ';
	let ordID = order.id;
	let ttIcon = vipDetector(order.contragent.addresses) ? 'vip' : iconTimeConvector( timeWork( order.contragent.nachalo, order.contragent.konec ) );
	// Создаем маркер
	MarkersAll[ordID] = new google.maps.Marker({ position: cord, icon:'/assets/img/ico/'+ttIcon+'.png', map: google_map, tooltip: title, id: ordID, draggable: false });
	// Устанавливаем обработчики повидения маркера
	let tooltip = new Tooltip( {map: google_map}, MarkersAll[ordID] ); 
		tooltip.bindTo('text', MarkersAll[ordID], 'tooltip');
		MarkersAll[ordID].addListener('click', function(){ tooltip.removeTip(); clickMarker(order); }); 
		MarkersAll[ordID].addListener('mouseover', function() { tooltip.addTip(); tooltip.getPos2(this.getPosition()); });
		MarkersAll[ordID].addListener('mouseout', function() { tooltip.removeTip(); });
		// Отображаем на карте
		MarkersAll[ordID].setMap( google_map );
}

//
function addCruise(cruise) {
	let r = '<li><a onClick="changerMaekerToCruise('+cruise.id+');" class="check-link"><i class="fa fa-flag-checkered"></i> </a> <span class="m-l-xs">'+cruise.name_auto+'</span> ';
		r+= ' <a href="#" title="Коммент: '+cruise.comment+'"><i class="fa fa-info-circle"></i> </a> ';
		r+= ' <a href="/map/'+cruise.id+'" title="Открыть маршрут в навигаторе" target="_blank"><i class="fa fa-location-arrow"></i></a>';
		r+= '<small class="label label-primary">'+cruise.weith_sum+' кг</small>';
	//	r+= '<small class="label label-danger">'+cruise.summa_sum+' грн</small>';
		r+= '<small class="label label-success">'+cruise.kmdirect+' км</small>';
		r+= '</li>';
	$('#cruiseList').append(r);
}

// Клик по маркеру
function clickMarker( order ) {
	let title = order.contragent.name+', '+order.weith+' кг, '+order.contragent.addresses+', '+order.koment;
}

//
function changerMaekerToCruise(id) {
	for (let i in OrdersList) {
		if (OrdersList[i].cruise_id == id) {
			if ( OrdersList[i].contragent == null ) continue;
			// toogle markers
			MarkersAll[OrdersList[i].id].setVisible( !MarkersAll[OrdersList[i].id].getVisible() );
		}
	}
}

//
function loadAjaxComponents() {
	let date = $('#datadiv').val();
	if ( OrdersList.length > 0 ) { alert('Нельзя загрузить сразу два периода.'); return false; }
	$.post('/disp/map/opt', {'terr': $('#teritorial').val(), 'deliv':date}, function(r){
		OrdersList = r.orders;
		CruiseList = r.cruises;
		initToMapOrders();
		initToInformation();
	});
}

// Показать или скрыть все
function changedAllTT() {
	for (let i in OrdersList) {
		if ( OrdersList[i].contragent == null ) continue;
		MarkersAll[OrdersList[i].id].setVisible( ChGlobStat );
	}
	ChGlobStat = !ChGlobStat;
}

