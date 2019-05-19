'use strict';
let tmpDistr; 

// Init function
function __construct() {
	try {
		
		
		$.notify({message: 'Тут вы можете загрузить данные на отгрузку с УДК.'},{ type: 'info', delay: 2000, allow_dismiss: true, placement: { align: 'right' } });
	} catch (e) {  }
}

// Удалить сессии доставки по ID
function butDelete_distrLoad(id){
	tmpDistr = id;
	swal({	title: "Удалить сессию?", text: "Будут удалены все загруженные данные, такие как заказы, автомобили и рейсы для сессии (все).",
			type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55",
			confirmButtonText: "Да, удалить все!", closeOnConfirm: true
		}, function () { Delete_distrLoad(); }
	);
}

// 
function Delete_distrLoad() {
	$.post('/log/import/'+tmpDistr+'/delete', {'id': tmpDistr}, function(r){
		if ( r.success ) {
			$('#sdlRow_'+tmpDistr).remove();
			$.notify({message: 'Сессия доставки и все данные удалены!'},{ type: 'success', delay: 2000, allow_dismiss: true, placement: { align: 'right' } });
		} else $.notify({message: 'Ошибка удаления! '+r.error},{ type: 'warning', delay: 2000, allow_dismiss: true, placement: { align: 'right' } });
	});
}

