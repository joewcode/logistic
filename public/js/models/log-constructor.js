'use strict';
let google_map;					// Глобальная карта google
let google_map_cruise;			// Карта google для рейса
let allOrders 			= {}; 	// Список всех заказов
let allCars 			= {}; 	// Список автопарка
let baseCoordinate;				// Хранит LatLng google map объекта местоположение базы
let CurrentCar 			= 0;	// Идентификатор активной машины
let DirService; 				// Directions
let DirDisplay;					// Directions
let CountOrders			= 0;	// Кол-во заказов
let CountWieght 		= 0;	// Общий вес


// Init function
function __construct() {
	try {
		let terr = getTerritory(territoryID);
		baseCoordinate = new google.maps.LatLng(terr[0], terr[1]);
		let mapOptions = {	zoom: 9, center: baseCoordinate, mapTypeId: google.maps.MapTypeId.ROADMAP, zoomControl: true, mapTypeControl: true, scaleControl: false, streetViewControl: false, rotateControl: false, styles: map_Options };
		
		// Запускаем карты
		google_map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		google_map_cruise = new google.maps.Map(document.getElementById('DrvMAPdiv'), mapOptions);
		
		// Подключаем службу просчета маршрута для карты рейса
		DirService = new google.maps.DirectionsService();
		DirDisplay = new google.maps.DirectionsRenderer({map: google_map_cruise, suppressMarkers: true});
		
		// Добавить иконки склада на карты
		let merker = new google.maps.Marker({ position:baseCoordinate, icon:'/assets/img/ico/base32.png', map: google_map, title: 'Склад ФБП', id: 'skladIcon', draggable: false });
		let merker2 = new google.maps.Marker({ position:baseCoordinate, icon:'/assets/img/ico/base32.png', map: google_map_cruise, title: 'Склад ФБП', id: 'skladIcon2', draggable: false });
		merker.setMap( google_map ); merker2.setMap( google_map_cruise );
		
		// Все готово к работе
		errHelper(7);
	} catch (e) { errHelper(8); }
}

// Обработчик подключенной сессии с ответа AJAX
function createSession( res ) {
	// Заполняем общие данные
	CountOrders = res.main.count_orders; CountWieght = res.main.count_wieght;
	$('#dataDeli').html( res.main.session_todate.slice(0, -9) ); 
	// */
	
	// Заказы + маркера
	for ( let i in res.orders ) {
		let ord = res.orders[i];
		allOrders[ord.id] = new Orders(ord);
		allOrders[ord.id].toMarkerOrderCreate();
		allOrders[ord.id].toAddTableAll();
	}
	// */
	
	// Автопарк HTML + ДБ авто
	let rr = ''; $('#autoparkList').html('<option value="">Нет</option>');
	for ( let i in res.cars ) {
		let car = res.cars[i]; rr+= '<option value="'+car.id+'">'+car.name+'</option>';
		allCars[car.id] = new Autopark( car );
		allCars[car.id].searchOrder();
	}
	$('#autoparkList').append(rr);
	// */
	
	// Рейсы 
	for ( let i in res.cruises ) {
		addHTMLCruiseTable(res.cruises[i]);
	}
	// */

	// Все ок
	mapInfoGUpView();
	upFootable();
	errHelper(4);
//	console.log('Сессия загружена успешно. № '+res.main.id);
}

// Отображаем хтмл глоб инфо
function mapInfoGUpView() {
	$('#statCouOrd').html( CountOrders ); 
	$('#statTonn').html( CountWieght.toFixed(2) );
}

// Обновляем глобальные параметры веса
function mapInfoGlobSet(tp, wg) {
	if ( tp == 1 ) { CountOrders++; CountWieght+= wg;
	} else { CountOrders--; CountWieght-= wg; }
}

// HTML рейса [id, ?????????????????????????????????????????????????????????????????????????????????????????????????????
function addHTMLCruise(cru){
	let r = '<li id="cruise_'+cru.id+'"><span class="m-l-xs">'+cru.name_auto+' №'+cru.id+'</span>' 
			+'<small class="label label-primary">'+cru.weith_sum+' кг</small>'
			+'<small class="label label-primary">'+cru.summa_sum+' грн</small>'
			+'<small class="label label-'+(cru.status_auto?'primary':'danger')+'">'+(cru.status_auto ? 'Отгружен в УДК' : 'Запланирован')+'</small>'
			+'<small class="label label-primary" data-toggle="tooltip" data-placement="top" title="'+cru.comment+'" data-original-title="'+cru.comment+'"> <i class="fa fa-bell" ></i> </small>'
			+'<small class="label label-primary" title="Скачать XML"> <i class="fa fa-cloud-download" ></i> </small>'
			+'</li>';
	return r;
}

// Записанные рейсы
function addHTMLCruiseTable(cru) {
	let t = '<td>'+cru.name_auto+'</td>';
		t+= '<td>'+cru.weith_sum+' кг</td>';
		t+= '<td>'+cru.summa_sum+' грн</td>';
		t+= '<td>'+cru.kmdirect+' км</td>';
		t+= '<td>открыть <a htef="#" onClick="window.open(\'/map/'+cru.id+'\', \'\'); return false;">№ '+cru.id+'</a> </td>';
		t+= '<td><b class="crstats">'+(cru.status_auto ? 'Отгружен в УДК' : 'Запланирован')+'</b> <small class="label label-primary" onClick="downloadCruiseToId('+cru.id+');" title="Скачать XML"><i class="fa fa-cloud-download" ></i></small></td>';
		t+= '<td>'+cru.comment+'</td>';
	$('#cruiseTableList tbody').append('<tr id="crulist_'+cru.id+'">'+t+'</tr>');
}

// Скачать рейс
function downloadCruiseToId(id) {
//	console.log('Скачивание рейса № '+id);
	$('#crulist_'+id+' .crstats').html('Отгружен в УДК');
	window.open('/log/constructor/download/'+id);
	return true;
}

// Обработчик клика по маркеру
function clickMarker( id ) {
	if ( !CurrentCar ) { errHelper(0); return false;}
	
	// Если заказ не состоит в рейсе, добавим
	if ( allOrders[id].status == 0 ) { 
		allOrders[id].status = CurrentCar;
		allOrders[id].Marker.setMap( google_map_cruise );
		allCars[CurrentCar].cur_weith+= allOrders[id].massa;
		allCars[CurrentCar].cur_count+= 1;
		allCars[CurrentCar].cur_summa+= allOrders[id].summa;
		allCars[CurrentCar].cur_orders[id] = allOrders[id];
		
		//
		mapInfoGlobSet(0, allOrders[id].massa);
		allOrders[id].toAddTableCruise();
		$('#trordtd_'+id).remove();
		
	} else { // Обратная процедура + обработка исключения
		if ( allOrders[id].status != CurrentCar ) { errHelper(1); return false; }
		allOrders[id].status = 0;
		allOrders[id].Marker.setMap( google_map );
		allCars[CurrentCar].cur_weith-= allOrders[id].massa;
		allCars[CurrentCar].cur_count-= 1;
		allCars[CurrentCar].cur_summa-= allOrders[id].summa;
		allCars[CurrentCar].cur_orders[id] = null;
		
		//
		mapInfoGlobSet(1, allOrders[id].massa);
		allOrders[id].toAddTableAll();
		$('#trordtdc_'+id).remove();
		
	}
	// 
	$('#savestatusHTM').show();
	allCars[CurrentCar].modifis = true;
	allCars[CurrentCar].infoHTML();
	mapInfoGUpView();
}

// Выбор машины
function autoparkOnChange(ee){
	if ( !ee.value ) { errHelper(2); return false;}
	let oldAuto = CurrentCar;
	CurrentCar = ee.value;
	// Удалить маркера, если нужно и тд
	if ( oldAuto ) allCars[oldAuto].unsetMarkers();
	// Обновляем авто и перезаполняем маркера
	let car = allCars[CurrentCar];
	// Манипуляции...
	if ( car.modifis ) $('#savestatusHTM').show(); else $('#savestatusHTM').hide();
	
	
	car.searchOrder();
	car.infoHTML();
	car.viewMarkers();
	// Все ок
	upFootable(0);
//	console.log('Выбран автомобиль ID: '+CurrentCar);
}

// Записать маршрут
function createCruise(){
	if ( !CurrentCar ) { errHelper(0); return false;}
	allCars[CurrentCar].searchOrder();
	allCars[CurrentCar].SavedDB();
}

// Записать маршрут ???
function cruiseCreatedCurrentBut(){
	if ( !CurrentCar ) { errHelper(0); return false;}
	swal({	title: "Записать рейс?", text: "После записи, рейс нельзя будет изменить, но можно скачать для импорта в УДК. Для удаления маршрута, перейдите в раздел Мои маршруты.",
			type: "info", showCancelButton: true, confirmButtonColor: "#DD6B55",
			confirmButtonText: "Да, записать!", closeOnConfirm: true
		}, function () { cruiseCreatedCurrent(); }
	);
}

// Подтвердить рейс
function cruiseCreatedCurrent() {
	if ( !CurrentCar ) { errHelper(0); return false;}
	allCars[CurrentCar].searchOrder();
	allCars[CurrentCar].CreatedDB();
}

// Открыть окно автопарка
function autoparkInfo() {
	$("#autoparkDiv").modal('toggle');

}


// ### --- ### --- ### --- ### --- ### --- ######################################################################## микрофункции ----- #####

// Подгружает сессию юзеру по AJAX
function loadingSession(id){
	// Если что-то уже загружено?
	if ( CountWieght ) { $("#sessionConnect .close").click(); errHelper(6); return false; }
	$.post('/log/constructor/'+id, {'id': id}, function(r){ return createSession(r); });
}

// Очистить JS данные текущего рейса
function clearDriverOrders( mthret = false, ligh = false) { 
	if ( !CurrentCar ) { errHelper(0); return false;} 
	mapInfoGUpView();
	upFootable();
	allCars[CurrentCar].clearedAuto(mthret, ligh); 
	return true;
}

// Обработчик перетаскивания маркера // отображаем координаты
function dragMarker(id) {
	let position_x = allOrders[id].Marker.getPosition().lat().toFixed(6);
	let position_y = allOrders[id].Marker.getPosition().lng().toFixed(6);
	$('#markerCords').empty().append('Координаты ТТ: '+position_x+' - '+position_y).show();
}

// messg helper
function errHelper(id, name = false) {
	if ( !name ) {
		let allList = [	["Нужно выбрать автомобиль.", "danger"],							// 0
						["Заказ не пренадлежит авто.", "danger"],							// 1
						["Авто не доступно.", "error"],										// 2
						["Ошибка построения", "danger"],									// 3
						["Данные сессии успешно загружены.", "success"],					// 4
						["Расчет маршрута завершен успешно.", "success"],					// 5
						["Одновременно можно подключить только одну сессию.", "danger"],	// 6
						["АРМ логиста запущено успешно.", "success"],						// 7
						["Произошла ошибка.", "danger"],									// 8
						["Маршрут сохранен.", "success"],									// 9
						["Маршрут не изменен, сохранение не требуется", "info"],			// 10
						["Маршрут записан успешно, вы можете его скачать.", "success"],		// 11
						["В маршруте нет заказов, сохранение невозможно.", "danger"],		// 12
						["Вы не выбрали сессию которую хотите загрузить", "danger"],		// 13
						["", "success"],													// 14
						["", "success"],													// 15
						["", "success"],													// 16
						["", "success"],													// 17
						["", "success"],													// 18
						["", "success"],													// 19
						["", "success"],													// 20
						["", "success"],													// 21
						["", "success"],													// 22
						["", "success"],													// 23
						["", "success"],													// 24
					];
		// View
		$.notify({message: allList[id][0] },{ type: allList[id][1], delay: 1000, allow_dismiss: true, placement: { align: 'left' } });
	} else $.notify({message: name },{ type: 'info', delay: 1000, allow_dismiss: true, placement: { align: 'left' } });
	return true;
}



// ### --- ### --- ### --- ### --- ### --- ######################################################################## километраж ----- #####

// Запрос на расчет маршрута
function tocreateDirection() {
	if ( !CurrentCar ) return false;
	swal({
			title: "Рассчитать маршрут?",
			text: "Количество использований в сутки ограничено! Расчет будет производится с помощью сервисов Google Maps.",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Да, рассчитать!",
			closeOnConfirm: true
		}, function () {
			// Обновляем заказы в рейсе и стоим маршрут
			allCars[CurrentCar].searchOrder();
			createdDistantionFunc();
			errHelper(5);
		}
	);
}

// Просчет маршрута
function createdDistantionFunc() {
	let direction = allCars[CurrentCar].getCruiseCordsOptemize();
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
			errHelper(3); 
		//	console.log('Ошибка построения маршрута: '+status); 
		}
	});
	DirDisplay.setMap(google_map_cruise);
	return true;
}

// На экран
function directionJAction(respon){
	let kmtr = computeTotalDistance(respon);
	let res = respon.routes[0].legs[0];
	$('#car_kilometr').html(kmtr+' км');
	// Сохранить к авто
	allCars[CurrentCar].upDirection( kmtr );
}

function computeTotalDistance(result) {
	let total = 0;
	let myroute = result.routes[0];
	for (let i = 0; i < myroute.legs.length; i++) {
		total += myroute.legs[i].distance.value;
	}
	return total = total / 1000;
}

// ################### Находит отдаленность одной точки от другой на карте
// function rad(x){return x*Math.PI/180;}
// let getDistance = function(p1,p2) {let R=6371,dLat=rad(p2.lat()-p1.lat()),dLong=rad(p2.lng()-p1.lng());let a=Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(rad(p1.lat()))*Math.cos(rad(p2.lat()))*Math.sin(dLong/2)*Math.sin(dLong/2);let c=2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));let d=R*c;return d.toFixed(6);}
// ###################






