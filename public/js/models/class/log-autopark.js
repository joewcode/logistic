'use strict';
// ----- ### ### ### ### ### ### ### Class module ### ### ### ### ### ### ### ###

// Автопарк
class Autopark {
	constructor(data) {
		this.id = data.id;
		this.name = data.name;
		this.nomer = data.nomer;
		this.code = data.code;
		this.tonag = data.tonag;
		//
		this.cur_weith = parseFloat( data.cur_weith );
		this.cur_count = data.cur_count;
		this.cur_summa = data.cur_summa;
		this.cur_orders = {};
		this.cur_direct = false;
		this.modifis = false;
	}
	
	// Отображает данные авто
	infoHTML() {
		$('#carM').html( this.tonag ); 
		$('#carN').html( this.nomer ); 
		$('#carT').html( this.cur_weith.toFixed(2) ); 
		$('#carTT').html( this.cur_count ); 
		$('#carGRN').html( this.cur_summa.toFixed(2) ); 
	}
	
	// Находит заказы в авто
	searchOrder() {
		// Чистим и ищем по заказам заново
		this.cur_orders = {};
		for ( var i in allOrders ) {
			if ( allOrders[i].status == this.id ) {
				this.cur_orders[i] = allOrders[i];
			//	console.log('Найден заказ ('+i+') в авто ID: '+this.id);
			}
		}
	}
	
	// Записать рейс в БД
	SavedDB( mthret = false ) {
		// Если водитель не изменен, то зачем лишние запросы?.. после обновить модис..
		if ( !this.modifis ) { errHelper(10); return false; }
		this.modifis = false; $('#savestatusHTM').hide();
	//	console.log('Сохранение маршрута, авто ID: '+CurrentCar);
		// продолжаем
		let arri = []; let count = 0; let weith = 0; let summa = 0;
		for ( let i in this.cur_orders ) {
			let ord = this.cur_orders[i];
			if ( ord.id > 0 ) {
				arri.push(ord.id);
				weith+= ord.massa; summa+= ord.summa; count++;
			}
		}
		// пишим..
		let arra = [count, weith, summa];
		$.post('/log/constructor/to/'+CurrentCar, {'cruise_id': CurrentCar, 'arrobj[]': arri, 'arrdo[]': arra}, function(r){
			if ( r.success ) {
					// Запускаем запись функцию, если нужно
				if ( mthret ) allCars[CurrentCar].CreatedDB();
				// Все ок..
				errHelper(9);
				console.log('Маршрут успешно сохранен.');
			} else errHelper(0, 'Ошибка сохранения маршрута!');
		});
	}
	
	// Подтвердить созданный маршрут
	CreatedDB() {
		// Если были изменения, то запишем.. и перезапустим функцию
		if ( this.modifis ) { this.SavedDB(true); return false; }
		// Если маршрут не пуст - продолжаем
		if ( this.cur_count > 0 ) {
			// Если не просчитан км?
			if ( !this.cur_direct ) { errHelper(0, 'Нет просчитанного маршрута!'); return false; }
			let commnt = $('#cruiseComment').val() || 'нет';
			$.post('/log/constructor/created/'+CurrentCar, {'cruise_id': CurrentCar, 'arrdi': this.cur_direct, 'comment': commnt}, function(r){
				if ( r.success ) {
					// Чистим авто
					clearDriverOrders(true, false);
					// Добавить рейс на дисплей
					addHTMLCruiseTable( r.cruise );
					// все ок
					$('#cruiseComment').val('');
					errHelper(11);
					console.log('Маршрут успешно записан.');
				} else errHelper(0, 'Ошибка записи маршрута!');
			});
		} else errHelper(12);
	}
	
	// Снять маркера с карты авто
	unsetMarkers( aOrd = false, ligh = false ) {
		let mp = ( ligh ) ? google_map : null;
		for ( let i in this.cur_orders ) {
			let id = this.cur_orders[i].id;
			allOrders[id].Marker.setMap( mp );
			if ( $('*').is('#trordtdc_' +id) ) $('#trordtdc_' +id).remove();
			// Если нужно очистить, то удалим и заказы.. или нет
			if ( aOrd ) delete allOrders[id];
			else if ( ligh ) {
				allOrders[id].status = 0;
				mapInfoGlobSet(1, allOrders[id].massa);
				allOrders[id].toAddTableAll();
			}
		}
	}
	
	// Поставить маркера на карту авто
	viewMarkers() {
		for ( let i in this.cur_orders ) {
			let id = this.cur_orders[i].id;
			allOrders[id].Marker.setMap( google_map_cruise );
			allOrders[id].toAddTableCruise();
		}
	}
	
	// 
	getCruiseCords () {
		let list = [];
		for ( var i in this.cur_orders ) {
			let ord = this.cur_orders[i];
			if ( !ord ) continue;
			let cord = new google.maps.LatLng(ord.CordLat, ord.CordLng); 
			list.push( {location: cord, stopover: false} );
		}
		return list;
	}
	
	// 
	getCruiseCordsOptemize () {
		let listR = [];
		let list = [];
		for ( var i in this.cur_orders ) {
			let ord = this.cur_orders[i];
			if ( !ord ) continue;
			let cord = new google.maps.LatLng(ord.CordLat, ord.CordLng);
			list[i] = {'loc': cord, 'addr': ord.Address};
		}
		// Оптимизация количества
		let coun = list.length;
		let stop = function(i){ return ( i >= 23 ) ? false : true; };
		let OptKM = $('#optemizeInput').val();
		let tmp = [];
		let unique = [];
		// Протестируем.. и запишем
		for ( var i in list ) {
			// Есть ли такая ТТ в списке?
			if ( tmp.indexOf( list[i].addr ) >= 0 ) continue;
			// Есть ли геокод у ТТ?
			if ( list[i].loc.lat().toFixed(6) == '0.000000' ) continue;
			// end...
			tmp[i] = list[i];
			unique[i] = list[i];
		}
		if ( coun > 22 ) {
		// Если не помогло? 
		if ( !stop(tmp.length) ) {
			// Тест на присутствие точки рядом
			topact: for ( var i in tmp ) {
				if ( stop(unique.length) ) break;
				let curTT = tmp[i];
				if ( !curTT ) continue;
				for ( let a in unique ) { 
					if ( a == i ) continue;
					var dis = google.maps.geometry.spherical.computeDistanceBetween(curTT.loc, unique[a].loc);
					if ( dis < OptKM ) {
						delete unique[a];
						continue topact;
					}
				}
			}
		} }
		//
		for ( var i in unique ) {
			listR.push( {location: unique[i].loc, stopover: true} );
		}
		return listR;
	}
	
	// Обновить данные directions
	upDirection (kmt) {
		this.modifis = true;
		this.cur_direct = kmt;
	}
	
	// Очистить авто
	clearedAuto( mthret = false, ligh = false ) {
		// Удаляем маркера и заказы
		this.unsetMarkers( mthret, ligh );
		// Чистим параметры
		this.cur_weith = 0;
		this.cur_count = 0;
		this.cur_summa = 0;
		this.cur_orders = {};
		this.cur_direct = false;
		this.modifis = false;
		this.infoHTML();
		return true;
	}
	
}
