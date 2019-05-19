'use strict';
let tmpCruiseId;


// Init function
function __construct() {
	try {
		
		$.notify({message: 'Мои маршруты найти можно тут!'},{ type: 'info', delay: 1000, allow_dismiss: true, placement: { align: 'right' } });
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

//
function butDeleteCruise(id) {
	tmpCruiseId = id;
	swal({	title: "Удалить рейс?", text: "Рейс будет удален, а заказы снова будут доступны для распределения.",
			type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55",
			confirmButtonText: "Да, удалить!", closeOnConfirm: true
		}, function () { DeleteCruise(); }
	);
}

//
function DeleteCruise() {
	$.post('/log/routes/'+tmpCruiseId+'/delete', {'id': tmpCruiseId}, function(r){
		if ( r.success ) {
			$('#rtRow_'+tmpCruiseId).remove();
			$.notify({message: 'Сессия доставки и все данные удалены!'},{ type: 'success', delay: 2000, allow_dismiss: true, placement: { align: 'right' } });
		} else $.notify({message: 'Ошибка удаления! '+r.error},{ type: 'warning', delay: 2000, allow_dismiss: true, placement: { align: 'right' } });
	});
}