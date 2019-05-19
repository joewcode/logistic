'use strict';
let csrftoken = $('meta[name=csrf-token]').attr('content');			// Токен ларавел
let territoryID = $('meta[name=territory-id]').attr('content');		// IT территориального подразделения


// Init function
function __construct() {
	try {
		// Вставим название региона на страничку
		
		
		
	} catch (e) {  }
}


// Скачать рейс
function LoadXMLCruise(id) {
	window.open('/log/constructor/download/'+id);
}
	
// Открыть карту
function openMapCruise(id) {
	window.open('/map/'+id);
}
	
// Редактировать рейс
function editorCruise(id) {
	alert('Редактировать рейс №'+id);
}

