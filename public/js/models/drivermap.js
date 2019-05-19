'use strict';
let google_map;				// Глобальная карта google
let baseCoordinate;			// Хранит LatLng google map объекта местоположение базы
let DirService; 			// 
let DirDisplay;				//
let MarkersAll = {};		// Маркера
let LastOptem = 100;		// 
let LastMrCl = false;		// 
let InfoWindows = {}; 		//
let GPSInterval = false; 	//
let augInterval = false;	// 

// Init function
function __construct() {
	try {
		let terr = getTerritory(UsrTerritory);
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
		
		// Обработаем карту..
		initToMapOrders();
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

// Добавить на карту маркер (latlng, ico, titl, uniqueID, drag = true, mapID = 1) {
function addMarker ( order ) {
	if ( order.contragent == null ) {
		console.log('Uncnown contragent, order ID: '+order.id);
		return false;
	}
	let cord = new google.maps.LatLng(order.contragent.shirota, order.contragent.dolgota);
	let title = '<h5>'+order.contragent.name+'</h5><b>'+order.weith+'</b> кг, с <b>'+order.contragent.nachalo+'</b> по <b>'+order.contragent.konec+'</b><br>'+order.contragent.addresses+'<br><b>'+(order.koment)+'</b>';
	let ordID = order.id;
	let ttIcon = vipDetector(order.contragent.addresses) ? 'vip' : iconTimeConvector( timeWork( order.contragent.nachalo, order.contragent.konec ) );
	// Создаем маркер
	MarkersAll[ordID] = new google.maps.Marker({ position: cord, icon:'/assets/img/ico/'+ttIcon+'.png', map: google_map, tooltip: title, id: ordID, draggable: false });
	// Устанавливаем обработчики повидения маркера
	let tooltip = new Tooltip( {map: google_map}, MarkersAll[ordID] ); 
		tooltip.bindTo('text', MarkersAll[ordID], 'tooltip');
		InfoWindows[ordID] = new google.maps.InfoWindow({ content: title });
		//
		MarkersAll[ordID].addListener('click', function(){ tooltip.removeTip(); closedInfWinAll(); InfoWindows[ordID].open(google_map, MarkersAll[ordID]); }); 
		MarkersAll[ordID].addListener('mouseover', function() { tooltip.addTip(); tooltip.getPos2(this.getPosition()); });
		MarkersAll[ordID].addListener('mouseout', function() { tooltip.removeTip(); });
		// Отображаем на карте
		MarkersAll[ordID].setMap( google_map );
}

// Клик по маркеру
function clickMarker( ordID ) {
}

// Close All mess
function closedInfWinAll() {
	for (var i in InfoWindows) InfoWindows[i].close();
}

//
function toMarkCord(id) {
	if ( LastMrCl ) return false;
	LastMrCl = MarkersAll[id];
	google_map.getBounds().contains(LastMrCl.getPosition());
	google_map.setCenter(LastMrCl.getPosition());
	google_map.setZoom(14);
	LastMrCl.setAnimation(google.maps.Animation.BOUNCE);
	setTimeout(function(id){ LastMrCl.setAnimation(null); LastMrCl = false; }, 3000);
}


// --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ### --- ###

// ---- Функции гео

// Автообновление
function forLocation(){
	if ('geolocation' in navigator) { 
		if ( $('#autoUpGPS').is(':checked') ) {
			getUserLocation();
			augInterval = setInterval('getUserLocation();', 1000 * 20); // 20 sec
		} else {
			augInterval = false;
			console.log('Автообновление остановлено.');
		}
	}
}

function getUserLocation() {
	if ('geolocation' in navigator) {
		console.log('Функция геоположения запущена.');
		navigator.geolocation.getCurrentPosition(showUserPosition, errorUserPosition, {maximumAge: 5000, timeout: 10000, enableHighAccuracy: true});
		//
		if ( $('#autoUpGPS').is(':checked') ) {
			if ( !GPSInterval ) {
				GPSInterval = setInterval(function(){ 
					navigator.geolocation.getCurrentPosition(showUserPosition, errorUserPosition, {maximumAge: 5000, timeout: 10000, enableHighAccuracy: true}); 
				}, 10000);
			}
		} else clearInterval(GPSInterval);
	} else { console.log('Опция определения геоположения пользователя недоступна.'); alert('Опция определения геоположения пользователя недоступна.');}
}

function showUserPosition(position) {
	// console.log('Определены координаты пользователя: '+position.coords.latitude+' : '+position.coords.longitude+' (точность: '+position.coords.accuracy/1000+'км)');
	let mark = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
	// Добавить иконку юзера на карту
	let ttle = 'Моё местоположение, точность: '+position.coords.accuracy/1000+'км)';
	MarkersAll['YouPos'] = new google.maps.Marker({ position:mark, icon:'/assets/img/ico/youcar.png', map: google_map, title: ttle, id: 'YouPos', draggable: false });
	MarkersAll['YouPos'].setMap( google_map );
	google_map.getBounds().contains(MarkersAll['YouPos'].getPosition());
	google_map.setCenter(MarkersAll['YouPos'].getPosition());
	google_map.setZoom(18);
}

function errorUserPosition(error){
    switch(error.code) {
        case error.PERMISSION_DENIED: alert('Пользователь запретил считывать информацию о его местоположении.'); break;
        case error.POSITION_UNAVAILABLE: alert('Браузер не может определить местоположение пользователя.'); break;
        case error.TIMEOUT: alert('Браузер не успел определить местоположение пользователя в выделенное ему время.'); break;
        case error.UNKNOWN_ERROR: alert('Во время определения местоположения произошла неизвестная ошибка.'); break;
    }
}

// */


// Просчет маршрута
function createdDistantionFunc() {
	if ( !confirm('Вы уверены что хотите повторно пересчитать расстояние?') ) return false;
	
	let direction = getOptemizeOrderList();
	
	let request = {
			origin: baseCoordinate,
			destination: baseCoordinate,
			travelMode: google.maps.DirectionsTravelMode.DRIVING,
			unitSystem: google.maps.UnitSystem.METRIC,
			waypoints: direction,
			optimizeWaypoints: true,
			provideRouteAlternatives: false,
			avoidHighways: false,
			avoidTolls: false,
			region: 'UA'
		};
	DirService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			DirDisplay.setDirections(response); 
			directionJAction(response); 
		} else {
			console.log('Ошибка построения маршрута: '+status); 
		}
	});
	DirDisplay.setMap(google_map);
	return true;
}

// На экран инфо маршрута
function directionJAction(respon){
	let kmtr = computeTotalDistance(respon);
	let res = respon.routes[0].legs[0];
	$('#cou_rkm').html(kmtr+' км');
}

// Считаем отрезки карты
function computeTotalDistance(result) {
	let total = 0;
	let myroute = result.routes[0];
	for ( let i = 0; i < myroute.legs.length; i++ ) total += myroute.legs[i].distance.value;
	return total = total / 1000;
}

// Составляет список точек 
function getOptemizeOrderList() {
	let listR = [];
	let list = [];
	for ( var i in OrdersList ) {
		let ord = OrdersList[i];
		if ( !ord || ord.contragent == null ) continue;
		let cord = new google.maps.LatLng(ord.contragent.shirota, ord.contragent.dolgota);
		list[i] = {'loc': cord, 'addr': ord.contragent.addresses};
	}
	let coun = list.length;
	let coOptem = function(c){ let b = [ [23, 0], [25, 100], [30, 200], [40, 400], [100, 600] ]; for ( var i in b ) { if ( c < b[i][0] ) return b[i][1]; } return 1000; };
	let stop = function(i){ return ( i >= 23 ) ? false : true; };
	LastOptem = coOptem(coun);
	let tmp = [];
	let unique = [];
	for ( var i in list ) {
		if ( tmp.indexOf( list[i].addr ) >= 0 ) continue;
		if ( list[i].loc.lat().toFixed(6) == '0.000000' ) continue;
		tmp[i] = list[i];
		unique[i] = list[i];
	}
	if ( LastOptem > 0 ) {
		if ( !stop(tmp.length) ) {
			topact: for ( var i in tmp ) {
				if ( stop(unique.length) ) break;
				let curTT = tmp[i];
				if ( !curTT ) continue;
				for ( let a in unique ) { 
					if ( a == i ) continue;
					var dis = google.maps.geometry.spherical.computeDistanceBetween(curTT.loc, unique[a].loc);
					if ( dis < LastOptem ) {
						delete unique[a];
						continue topact;
					}
				}
			}
		}
	}
	for ( var i in unique ) listR.push( {location: unique[i].loc, stopover: true} );
	return listR;
}

