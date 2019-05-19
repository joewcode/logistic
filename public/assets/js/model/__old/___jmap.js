/* Максимально упростил для дальнейшего взаимодействия */

var jact = 0;			// Переменная отвечает за тип работы
var map, map2; 			// инициализируем карты
var sess_orders = {}; 	// храним и взаимодействуем с заказами
var sess_autopark = {};//{"cruises":{}}; // хранится автопарк 
var sess_markers = {}; 	// все маркеры что были созданы, тип почти идентичен sess_orders 
var sess_cruises = {}; 	// все маркеры что были созданы, тип почти идентичен sess_orders 

var LatLngSKLAD; 		// Хранит LatLng google map объекта местоположение базы
var CurrentCar = 0;		// Идентификатор активной машины
var CountOrders = 0; 	// По умолчанию
var CountWieght = 0; 	// По умолчанию
var infowindow; 		// модуль всплывающих окон
var optDistance = 1;	// Оптимизация ТТ в км

var directionsDisplay; 	// 
var directionsService; 	// 


// Init function
function __construct() {
	try {
		// Объявляем стиль, метод и координаты для карты
		jact = $(".google-map").is("#DrvMAPdiv") ? 1 : 0;
		infowindow = new google.maps.InfoWindow({ content: map_Window });
		LatLngSKLAD = new google.maps.LatLng(46.481841, 30.669300);
		var mapOptions = { zoom: 9, center: LatLngSKLAD, mapTypeId: google.maps.MapTypeId.ROADMAP, zoomControl: true, mapTypeControl: true, scaleControl: false, streetViewControl: false, rotateControl: false, styles: map_Options};
		map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		if ( jact ) map2 = new google.maps.Map(document.getElementById('DrvMAPdiv'), mapOptions);
		
		// службы просчета маршрута
		directionsService = new google.maps.DirectionsService();
		directionsDisplay = new google.maps.DirectionsRenderer({map:map2, suppressMarkers: true});
		
		// Добавляем иконки склада на карты
		addMarker(LatLngSKLAD, 'base32', '<b>Склад ФБП</b>', 'BaseSclad', false);
		if ( jact ) addMarker(LatLngSKLAD, 'base32', '<b>Склад ФБП</b>', 'BaseSclad2', false, 2);
		
		$.notify({message: 'АРМ логиста запущено успешно.'},{type: 'info', delay: 1000});
	} catch (e) { $.notify({message: 'Произошла ошибка.'},{type: 'danger'}); }
}

// Добавить маркер на карту
function addMarker(latlng, ico, titl, uniqueID, drag = true, mapID = 1) {
	var cmp = (mapID == 1) ? map : map2;
	sess_markers[uniqueID] = new google.maps.Marker({position:latlng,icon:'/assets/img/ico/'+ico+'.png',map:cmp,tooltip:titl,id:uniqueID,draggable:drag});
	if ( uniqueID != 'BaseSclad' && uniqueID != 'BaseSclad2' ) {
		sess_markers[uniqueID].addListener('click', function(){ tooltip.removeTip(); clickMarker(uniqueID); }); 
		sess_markers[uniqueID].addListener('dragend', function(){ dragMarker(uniqueID); });
	}
	// title
	var tooltip = new Tooltip({map: cmp}, sess_markers[uniqueID]); tooltip.bindTo('text', sess_markers[uniqueID], 'tooltip');
	sess_markers[uniqueID].addListener('mouseover', function() { tooltip.addTip(); tooltip.getPos2(this.getPosition()); });
	sess_markers[uniqueID].addListener('mouseout', function() { tooltip.removeTip(); });
	// view
	sess_markers[uniqueID].setMap( cmp );
}

// Обработчик клика по маркеру
function clickMarker(id) {
	if ( !CurrentCar ) { $.notify({message: 'Нужно выбрать автомобиль.'},{type: 'danger'}); return false;}
	
	if ( sess_orders[id].status > 0 ) {
		sess_orders[id].status = 0; 
		sess_markers[id].setMap( map );
		HTMLchanger(id, false);
	} else { 
		sess_orders[id].status = CurrentCar; 
		sess_markers[id].setMap( map2 ); 
		HTMLchanger(id, true);
	}
}

// Обработчик перетаскивания маркера
function dragMarker(id) { $('#markerCords').html('cord: '+sess_markers[id].getPosition() ); }

// Подгружает сессию юзеру по аякс
function loadingSession(id) { //
	var csrftoken = $('meta[name=csrf-token]').attr('content');
	$.post('/log/constructor/'+id, {'_token':csrftoken, 'id':id},function(r){ return cruiseLoadParser(r); });
}

// Распарсивает в нужном виде полученные данные для работы и выбрасываем маркера на карту
function cruiseLoadParser(res){
	console.log('Init session load '+res.main.id);
	createMainInfo(res.main);
	createMarkerOrders( res.orders );
	autoparkCreate( res.cars );
	cruisesCreate( res.cruises );
	// Все ок
	$.notify({message: 'Данные сессии успешно загружены.'},{type: 'success'});
}

//
function createMainInfo(i){
	CountOrders = i.count_orders;
	CountWieght = i.count_wieght;
	$('#dataDeli').html(i.session_todate);
	$('#statCouOrd').html(i.count_orders);
	$('#statTonn').html(i.count_wieght);
}

//
function HTMLchanger( id, type ){
	if ( type ) {
		delTableOrders(id); // Удалить из таблицы заказов
		addTableCruise( sess_orders[id] ); // Добавить в таблицу авто
		CountOrders+= 1;
		CountWieght+= sess_orders[id].weith;
		sess_autopark[CurrentCar]['cur_count']+= 1; 
		sess_autopark[CurrentCar]['cur_weith']+= sess_orders[id].weith;
		console.log('Marker changed to auto');
	} else {
		addTableOrders( sess_orders[id] ); // Добавить в таблицу заказов
		delTableCruise( id ); // Удалить из таблицы авто
		CountOrders-= 1;
		CountWieght-= sess_orders[id].weith;
		sess_autopark[CurrentCar]['cur_count']-= 1; 
		sess_autopark[CurrentCar]['cur_weith']-= sess_orders[id].weith;
	}
	//
	//
	$('#statCouOrd').html( CountOrders ); $('#statTonn').html( CountWieght.toFixed(2) );
	$('#carT').html( sess_autopark[CurrentCar]['cur_weith'].toFixed(2) );
	$('#carTT').html( sess_autopark[CurrentCar]['cur_count'] );
}

// Добавляем маркера на карту развоза
function createMarkerOrders(orders) {
	for ( var i in orders ) {
		var ord = orders[i];
		if ( ord.cruise > 0 ) continue;
		
		sess_orders[ord.id] = ord;
		var cord = new google.maps.LatLng(ord.shirota, ord.dolgota);
		var title = '<h5>'+ord.name+'</h5><b>'+ord.weith+'</b> кг, с <b>'+ord.nachalo+'</b> по <b>'+ord.konec+'</b><br>'+ord.addresses+'<br><b>'+ord.koment+'</b>';
		// Подбор иконки
		var timed = timeWork(ord.nachalo, ord.konec);
		var ico = iconTimeConvector(timed);
		// Добавить на карту и в таблицу
		addMarker(cord, ico, title, ord.id, true, (!ord.status?1:2));
		addTableOrders(ord);
	}
	$('#allTableOrders').trigger('footable_initialize');
}

// Генерим автопарк
function autoparkCreate(cars){
	var rr = ''; $('#autoparkList').html('<option value="">Нет</option>');
	$(cars).each(function(e, i){
		rr+= '<option value="'+i.id+'">'+i.name+'</option>';
		i.cruises = [];
		sess_autopark[i.id] = i;
	});
	$('#autoparkList').append(rr);
}

// Генерим рейсы
function cruisesCreate(cruis){
	var rr = ''; $('#cruiseList').html('');
	$(cruis).each(function(e, i) {
		var auto = getAutoCode(i.code_auto);
		i.auto = auto.id; sess_cruises[i.id] = i;
		rr+= addHTMLCruise(i, auto['name']);
		sess_autopark[auto.id]['cruises'].push(i); 
	});
	$('#cruiseList').append(rr);
}

// HTML рейса
function addHTMLCruise(i, n){
	var r = '<li id="cruise_'+i.id+'"><span class="m-l-xs">'+n+' №'+i.id+'</span>' 
			+'<small class="label label-primary">'+i.weith_sum+' кг</small>'
			+'<small class="label label-primary">'+(i.status_auto ? 'Запланирован' : 'Отгружен')+'</small>'
			+'<small class="label label-primary" data-toggle="tooltip" data-placement="top" title="'+i.comment+'" data-original-title="'+i.comment+'"> <i class="fa fa-bell" ></i> </small>'
			+'</li>';
	return r;
}


// Добавить строку в таблицу заказов
function addTableOrders(item){
	var r = $('<tr id="trordtd_'+item.id+'"> <td>'+item.name+'</td> <td>'+item.weith+' кг</td> <td>с '+item.nachalo+' по '+item.konec+'</td> <td>'+item.addresses+'</td> <td>'+item.koment+'</td> <td>'+item.summa+' грн.</td> <td><a class="" id="toAuto('+item.id+');"><i class="fa fa-minus-circle text-navy"></i></a></td></tr>');
    $('#allTableOrders tbody').append(r);
}

// Добавить строку в таблицу заказов авто
function addTableCruise(item){
	var t = '<td>'+item.name+'</td>';
	t+= '<td>'+item.weith+'</td>';
	t+= '<td><i class="fa fa-bell" data-toggle="tooltip" data-placement="top" title="с '+item.nachalo+' по '+item.konec+'" data-original-title="с '+item.nachalo+' по '+item.konec+'"></i></td>';
	t+= '<td>'+item.addresses+'</td>';
	t+= '<td>'+item.koment+'</td>';
	t+= '<td>'+item.summa+' грн.</td>';
	t+= '<td><a class="" id="noAuto('+item.id+');"><i class="fa fa-minus-circle text-navy"></i></a></td>';
	var r = $('<tr id="trordtdc_'+item.id+'">'+t+'</tr>');
    $('#cruiseTableOrders tbody').append(r);
}

// Удалить строку из таблицы заказов
function delTableOrders(id){ $('#trordtd_'+id).detach(); }
// Удалить строку из таблицы заказов авто
function delTableCruise(id){ $('#trordtdc_'+id).detach(); }
// Получить ID машины по коду... мерзкая операция, зато экономим
function getAutoCode(code){ for (var i in sess_autopark) { if ( code == sess_autopark[i].code ) return sess_autopark[i]; } return false; }

// Выбор машины
function autoparkOnChange(ee){
	if ( !ee.value ) {$.notify({message: 'Авто не доступно.'},{type: 'error'}); return false;}
	var v = sess_autopark[ee.value]; $('#carM').html(v.tonag); $('#carN').html(v.nomer); $('#carT').html(v.cur_weith.toFixed(2)); $('#carTT').html(v.cur_count); 
	CurrentCar = ee.value;
	$('#cruiseTableOrders tbody').html('');
	for ( var i in sess_orders ) {
		var ord = sess_orders[i];
		// Удаляем все маркера с карты авто если они в рейсе
		if ( ord.status > 0 ) { sess_markers[ord.id].setMap(null); }
		// Если авто принадлежит заказу - показываем и добавляем в таблицу
		if ( ord.status == CurrentCar ) {
			sess_markers[ord.id].setMap( map2 );
			// add list cruise
			addTableCruise(ord);
		}
	}
	console.log('changed auto '+CurrentCar);
	$('#cruiseTableOrders').trigger('footable_initialize');
}

// Записать маршрут
function createCruise(){
	if ( !CurrentCar ) {$.notify({message: 'Нужно выбрать автомобиль.'},{type: 'danger'}); return false;}
	if ( confirm('Записать данные по рейсу в базу данных?') ) {
		console.log('Saved cruise auto id: '+CurrentCar);
		var arri = []; var count = weith = summa = 0;
		for ( var i in sess_orders ) {
			if ( sess_orders[i].status == CurrentCar ) { count++;
				arri.push(i);
				weith+= sess_orders[i].weith;
				summa+= sess_orders[i].summa;
			}
		}
		var arra = [count, weith, summa];
		var csrftoken = $('meta[name=csrf-token]').attr('content');
		$.post('/log/constructor/to/'+CurrentCar, {'_token':csrftoken, 'cruise_id':CurrentCar, 'arrobj[]':arri, 'arrdo[]':arra,'comment':$('#cruiseComment').val()},function(r){ return retSavedCruise(r); });
	}
}

//
function retSavedCruise(r){ 
	var st = (r.success == true) ? ['Маршрут сохранен', 'success'] : ['Маршрут не сохранен', 'danger'];
	addHTMLCruise(i, auto['name']);
	$.notify({message: st[0] },{type: st[1]});
}

// Просчет маршрута
function tocreateDirection() {
	if ( !CurrentCar ) return false;
	var direction = [];
	var all_tt = [];
	for ( var i in sess_orders ) {
		if ( sess_orders[i].status == CurrentCar ) {
			var cord = new google.maps.LatLng(sess_orders[i]['shirota'], sess_orders[i]['dolgota']);
			// Маршрут
			var stop = 1;
			for (var i in all_tt) {
				var c = all_tt[i]; if ( !c ) continue;
				var distance = distHaversine(cord, c);
				if ( distance < optDistance ) { stop = 0; break; }
			}
			if (stop){
				direction.push({location: cord, stopover: false});
			}
			all_tt.push(cord);
		}
	}
	console.log(direction);
	//
	var request = {
			origin: LatLngSKLAD,
			destination: LatLngSKLAD,
			travelMode: google.maps.TravelMode.DRIVING,
			unitSystem: google.maps.UnitSystem.METRIC,
			waypoints: direction,
			optimizeWaypoints: true,
			provideRouteAlternatives: false,
			avoidHighways: false,
			avoidTolls: false,
			region: 'UA'
		};
	directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response); 
			directionJAction(response); 
		} else $.notify({message: 'Ошибка построения' },{type: 'danger'});
	});
	directionsDisplay.setMap(map2);
//	Distance(direction);
	return true;
}

var directionJAction = function(respon){
	var res = respon.routes[0];
	$('#car_kilometr').html(res.legs[0].distance.text);
	$('#car_timework').html(res.legs[0].duration.text);
};

/*
var directionJAction = function(respon){
	var res = respon.routes, km = 0;
	for ( var i in res ) {
		var dd = res[i].legs;
		for (var b in dd ){var r = dd[b]; km+= r.distance.value; }
	}
	$('#all_kilometr').html(km/1000 + ' км');
};
*/



// ---- Микро функции помощники
// Вытаскиваем часы от до .. без минут
var timeWork = function(ot, to){ ot = ot.split(':'); to = to.split(':'); return [ot[0], to[0]]; }

// Подбираем иконку для заказа
var iconTimeConvector = function(arr){
	var ico = false; var o = parseInt(arr[1]); var d = parseInt(arr[0]);
	 if ( d > 0 ) { // Маркера - Доставка С
		if ( !ico && d >= 15 ) ico = 7; // с 15.00 Красный маркер
		if ( !ico && d >= 12 ) ico = 6; // с 12.00 Синий маркер
		if ( !ico && d >= 10 ) ico = 5; // с 10.00 Зеленый маркер
	}
	if ( o > 0  ) { // Флаги - Доставка ДО
		if ( !ico && o <= 11 ) ico = 1; // до 11.00 Большие часы
		if ( !ico && o <= 14 ) ico = 2; // до 14.00 Красный флаг
		if ( !ico && o <= 18 ) ico = 3; // до 18.00 Синий флаг
		if ( !ico && o <= 22 ) ico = 4; // до 22.00 Зеленый флаг
	}
	if ( !ico ) ico = 'def';
	return ico;
}

// Вернуть название филиала
var geoGetUsr = function(id) {
	var res = ['', 'Филиал №1 Котовск', 'Филиал №2 Одесса', 'Филиал №3 Измаил'];
	return res[id];
}



//*********DISTANCE AND DURATION**********************//
function Distance(direction) {
	var service = new google.maps.DistanceMatrixService();
    service.getDistanceMatrix({
        origins: LatLngSKLAD,
        destinations: LatLngSKLAD,
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC,
	//	waypoints: direction,
		avoidHighways: false,
        avoidTolls: false
    }, function (response, status) {
        if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
            var distance = response.rows[0].elements[0].distance.text;
            var duration = response.rows[0].elements[0].duration.text;
            var dvDistance = document.getElementById("all_statistic");
			dvDistance.innerHTML = "";
            dvDistance.innerHTML += "Distance: " + distance + "<br />";
            dvDistance.innerHTML += "Duration:" + duration;
 
        } else {
            alert("Unable to find the distance via road.");
        }
    });

}















// ################### Определение попадания точки в радиус другой
var rad = function(x){return x*Math.PI/180;}
var distHaversine = function(p1,p2) {var R=6371,dLat=rad(p2.lat()-p1.lat()),dLong=rad(p2.lng()-p1.lng());var a=Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(rad(p1.lat()))*Math.cos(rad(p2.lat()))*Math.sin(dLong/2)*Math.sin(dLong/2);var c=2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));var d=R*c;return d.toFixed(3);}
// ###################



// ---- Функции гео

function getUserLocation() {
	if ('geolocation' in navigator) {
		console.log('Функция геоположения запущена.');
		navigator.geolocation.getCurrentPosition(showUserPosition, errorUserPosition, {maximumAge: 5000, timeout: 10000, enableHighAccuracy: true});
	} else { console.log('Опция определения геоположения пользователя недоступна.'); alert('Опция определения геоположения пользователя недоступна.');}
}

function showUserPosition(position) {
	console.log('Определены координаты пользователя: '+position.coords.latitude+' : '+position.coords.longitude+' (точность: '+position.coords.accuracy/1000+'км)');
	var mark = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
	addMarker(mark, 'm14', 'Моё местоположение, точность: '+position.coords.accuracy/1000+'км)', 'UserPosition', false);
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


