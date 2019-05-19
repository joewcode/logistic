



// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- next

// 
function stringOptemizer ( text ) {
	var blocks = [60, 60, 60];
	var reString = '';
	var lastSpace = 0;
	var beginString = 0;
	var endString = 0;
	blocks.forEach(function(block, i) {
		endString += block;
		lastSpace = Math.max( text.lastIndexOf(' ', endString), text.lastIndexOf('.', endString), text.lastIndexOf(',', endString) );
		var restx = text.substring(beginString, lastSpace);
		if ( restx.trim() != '' && restx != '.' ) reString+= restx+'<br>';
		beginString = lastSpace + 1;
	});
	return reString;
}

// is VIP?
function vipDetector(text) { return (text.indexOf('VIP') + 1); }

// Возвращает координаты базы по ID округа..
function getTerritory(id){ let arr = ['', ['00.000000', '00.000000'], ['00.000000', '00.000000'], [0, 0] ]; return arr[id]; }

// Возвращает название округа..
function getTerritoryName(id) { let res = ['Нет', 'Филиал №1', 'Филиал №2', 'Филиал №3']; return res[id]; }

// Парсер времени ТТ, без минут
function timeWork(ot, to){ ot = ot.split(':'); to = to.split(':'); return [ot[0], to[0]]; }

// Подбираем иконку для заказа
function iconTimeConvector(arr) {
	let ico = false; let o = parseInt(arr[1]); let d = parseInt(arr[0]);
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
	if ( !ico ) ico = 'def'; return ico;
}

//
function copyTextInDiv(eID) {
	let elem = $('#'+eID).text();
	let temp = $('<input>');
	$('body').append(temp);
	temp.val(elem).select();
	document.execCommand('copy');
	temp.remove();
	$('#'+eID).hide();
	$.notify({message: 'Текст скопирован в буфер обмена.'},{ type: 'info', delay: 1000, allow_dismiss: true, placement: { align: 'right' } });
}



