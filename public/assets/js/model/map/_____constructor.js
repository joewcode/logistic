'use strict';
// ----- ### ### ### ### ### ### ### Constructor model options ### ### ### ### ### ### ### ###
let csrftoken = $('meta[name=csrf-token]').attr('content');
let google_map;				
let google_map_cruise;
// 
let SessionStatus = true;				// Можно загрузить сессию?
let Distribution = {};					// Содержит основную инфу, 



let allOrders = {}; 		// Список всех заказов
let allCars = {}; 			// Список автопарка
let baseCoordinate;			// Хранит LatLng google map объекта местоположение базы
let CurrentCar = 0;			// Идентификатор активной машины
let UsrTerritory = 2; 		// Подразделение пользователя по умолчанию
let DirService; 			// 
let DirDisplay;				//
let CountOrders = 0;		//
let CountWieght = 0;		//

// Init function
function __construct() {
	try {
		let terr = getTerritory(UsrTerritory);
		baseCoordinate = new google.maps.LatLng(terr[0], terr[1]);
		let mapOptions = {	zoom: 9, center: baseCoordinate, mapTypeId: google.maps.MapTypeId.ROADMAP, zoomControl: true, 
							mapTypeControl: true, scaleControl: false, streetViewControl: false, rotateControl: false, styles: map_Options };
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



