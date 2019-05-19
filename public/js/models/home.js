'use strict';
let google_map;				// Глобальная карта google
let baseCoordinate;			// Хранит LatLng google map объекта местоположение базы
let MarkersAll = {};		// Маркера

// Init function
function __construct() {
	try {
		let terr = getTerritory(territoryID);
		baseCoordinate = new google.maps.LatLng(terr[0], terr[1]);
		let mapOptions = {zoom: 9, center: baseCoordinate, mapTypeId: google.maps.MapTypeId.ROADMAP, zoomControl: true, mapTypeControl: true, scaleControl: false, streetViewControl: false, rotateControl: false, styles: map_Options };
		
		// Запускаем карты
		google_map = new google.maps.Map(document.getElementById('map'), mapOptions);
		
		// Добавить иконки склада на карты
		let merker = new google.maps.Marker({ position:baseCoordinate, icon:'/assets/img/ico/base32.png', map: google_map, title: 'Склад ФБП', id: 'skladIcon', draggable: false });
		merker.setMap( google_map );
		
		//
        loaderOrdersBD();
	//	google_map.data.setStyle(function(feature) { let magnitude = feature.getProperty('mag'); return { icon: getCircle(magnitude) }; });
		// ok..
		$.notify({message: 'Добро пожаловать!'},{ type: 'info', delay: 1000, allow_dismiss: true, placement: { align: 'right' } });
	} catch (e) { $.notify({message: 'Ошибка загрузки карт Google!'},{ type: 'danger', delay: 2000, allow_dismiss: true, placement: { align: 'right' } }); console.log(e); }
}

//
function getCircle(magnitude) {
	return { path: google.maps.SymbolPath.CIRCLE, fillColor: 'red', fillOpacity: .2, scale: Math.pow(2, magnitude) / 2, strokeColor: 'white', strokeWeight: .5 };
}

//
function eqfeed_callback(results) {
	console.log(results);
	google_map.data.addGeoJson(results);
	
	google_map.data.setStyle(function(feature) { let magnitude = feature.getProperty('mag'); return { icon: getCircle(magnitude) }; });
}

//
function getDataLine(arrData) {
	let lineData = { labels: ["Вторник", "Среда", "Читверг", "Пятница", "Суббота"], datasets: [
                    {
                        label: "Тоннаж",
                        backgroundColor: "rgba(26,179,148,0.5)",
                        borderColor: "rgba(26,179,148,0.7)",
                        pointBackgroundColor: "rgba(26,179,148,1)",
                        pointBorderColor: "#fff",
                        data: arrData['tonn']
                    },
                    {
                        label: "Заказы",
                        backgroundColor: "rgba(147, 176, 93, 0.6)",
                        borderColor: "rgba(147, 176, 93, 1)",
                        pointBackgroundColor: "rgba(147, 176, 93, 1)",
                        pointBorderColor: "#fff",
                        data: arrData['ordc']
                    },
                    {
                        label: "Сумма отгрузки",
                        backgroundColor: "rgba(147, 78, 93, 0.6)",
                        borderColor: "rgba(147, 78, 93, 0.1)",
                        pointBackgroundColor: "rgba(147, 78, 93, 1)",
                        pointBorderColor: "#fff",
                        data: arrData['summ']
                    }/*
					,{
                        label: "Расстояние",
                        backgroundColor: "rgba(255, 93, 17, 0.5)",
                        borderColor: "rgba(255, 93, 17, 1)",
                        pointBackgroundColor: "rgba(255, 93, 17, 1)",
                        pointBorderColor: "#fff",
                        data: arrData['kmtr']
                    }
					,{
                        label: "UPS",
                        backgroundColor: "rgba(220,220,220,0.5)",
                        borderColor: "rgba(220,220,220,1)",
                        pointBackgroundColor: "rgba(220,220,220,1)",
                        pointBorderColor: "#fff",
                        data: arrData['upsp']
                    }*/
				]};
	return lineData;
}

//
function chengedStatus(id) {
	$.post('/chsbp', {'id': id}, function(r){
		if ( r.success ) {
			$.notify({message: 'Выполнено!'},{ type: 'success', delay: 1000, allow_dismiss: true, placement: { align: 'right' } });
		}
	});	
}

//
function loaderOrdersBD() {
	console.log('Init load GeoJson');
	$.post('/eqfeed', {'meth': 'jn'}, function(r){
		if ( r.error ) $.notify({message: 'Ошибка!'},{ type: 'danger', delay: 1000, allow_dismiss: true, placement: { align: 'right' } });
		else eqfeed_callback(r);
	});	
}
