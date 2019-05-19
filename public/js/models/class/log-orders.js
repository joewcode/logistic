'use strict';
// ----- ### ### ### ### ### ### ### Class module ### ### ### ### ### ### ### ###

// Заказы и манипуляции с ними
class Orders {
	constructor(data) {
		this.id = data.id;							// ID заказа
		this.codeTT = data.contragent.code;			// Код ТТ
		this.nameTT = data.contragent.name;			// Название ТТ
		this.tpComment = data.koment;				// Коммент ТП
		this.massa = data.weith;					// Вес заказа
		this.summa = data.summa;					// Сумма заказа
		this.party = data.razvoz;					// Команда / ...
		this.status = data.auto_id;					// ID автомобиля 
		this.cruise = data.cruise_id;				// ID рейса
		this.TimeStart = data.contragent.nachalo; 	// Открытие ТТ
		this.TimeEnd = data.contragent.konec; 		// Закрытие ТТ
		this.CordLat = data.contragent.shirota;		// Гео широта
		this.CordLng = data.contragent.dolgota;		// Гео долгота
		this.Address = data.contragent.addresses;	// Адрес ТТ
		this.Marker; 								// Объект маркера
		//
		this.ttIcon = vipDetector(this.Address) ? 'vip' : iconTimeConvector( timeWork( this.TimeStart, this.TimeEnd ) );
	}
	
	// Добавить в маршрут
	toCruise( car_id ) { }
	
	// Удалить с маршрута
	delCruise() { }
	
	// Увеличивает показатели глобальной инфы
	plusHTML() { }
	
	// Уменьшает показатели глобальной инфы
	minusHTML() { }
	
	// Добавляет маркер на карту
	toMarkerOrderCreate() {
		let cord = new google.maps.LatLng(this.CordLat, this.CordLng);
		let title = '<h5>'+this.nameTT+'</h5><b>'+this.massa+'</b> кг, с <b>'+this.TimeStart+'</b> по <b>'+this.TimeEnd+'</b><br>'+this.Address+'<br><b>'+this.tpComment+'</b> ТП: ';
	//	let ico = this.ttIcon; //vipDetector(this.Address) ? 'vip' : iconTimeConvector( timeWork( this.TimeStart, this.TimeEnd ) );
		// Создаем маркер
		this.Marker = new google.maps.Marker({ position: cord, icon:'/assets/img/ico/'+this.ttIcon+'.png', map: google_map, tooltip: title, id: this.id, draggable: true });
		// Устанавливаем обработчики повидения маркера
		let tooltip = new Tooltip( {map: google_map}, this.Marker ); tooltip.bindTo('text', this.Marker, 'tooltip');
		let ordID = this.id;
		this.Marker.addListener('click', function(){ tooltip.removeTip(); clickMarker(ordID); }); 
		this.Marker.addListener('dragend', function(){ dragMarker(ordID); });
		this.Marker.addListener('mouseover', function() { tooltip.addTip(); tooltip.getPos2(this.getPosition()); });
		this.Marker.addListener('mouseout', function() { tooltip.removeTip(); });
		// Отображаем на карте
		this.Marker.setMap( google_map );
		// Если уже закинули?
		if ( this.status > 0 )
			this.Marker.setMap( null );
	}
	
	// Добавить строку в таблицу заказов
	toAddTableAll(){
		let r = '<td>'+this.nameTT+'</td> ';
			r+= '<td>'+this.massa+' кг</td> ';
			r+= '<td>с '+this.TimeStart+' по '+this.TimeEnd+'</td> ';
			r+= '<td>'+this.Address+'</td> ';
			r+= '<td>'+this.tpComment+'</td> ';
			r+= '<td>'+this.summa+' грн.</td> ';
			r+= '<td><a class="" onClick="clickMarker('+this.id+');" title="Добавить в маршрут"><i class="fa fa-minus-circle text-navy"></i></a></td>';
		$('#allTableOrders tbody').append('<tr id="trordtd_'+this.id+'"> '+r+'</tr>');
	}
	
	// Добавить строку в таблицу заказов авто
	toAddTableCruise(){
	//	let ico = vipDetector(this.Address) ? 'vip' : iconTimeConvector( timeWork( this.TimeStart, this.TimeEnd ) );
		let t = '<td>'+this.nameTT+'</td>';
			t+= '<td>'+this.massa+'</td>';
			t+= '<td><a class="" onClick="clickMarker('+this.id+');" title="Удалить с маршрута"><i class="fa fa-minus-circle text-navy"></i></a></td>';
			t+= '<td><img src="/assets/img/ico/'+this.ttIcon+'.png" title="с '+this.TimeStart+' по '+this.TimeEnd+'"></td>';
			t+= '<td>'+this.Address+'</td>';
			t+= '<td>'+this.tpComment+'</td>';
			t+= '<td>'+this.summa+' <i class="fa fa-eur"></i></td>';
			
		$('#cruiseTableOrders tbody').append('<tr id="trordtdc_'+this.id+'">'+t+'</tr>');
	}
	
}
