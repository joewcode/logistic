<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\OrderSession;
use App\Models\Contragent;
use App\Models\Order;
use App\Models\Car;
use App\Models\Cruise;

class LogistController extends Controller
{
	//
	public function importIndex() {
		$res['uTeritorial'] = Auth::user()->territory;
		$res['distrSessionList'] = OrderSession::getUserSessions( Auth::user()->id );
		return view('page.log_import', $res);
    }
	
	// 
    public function logistIndex() {
		$res = Array();
		$res['uTeritorial'] = Auth::user()->territory;
		$res['user_sessions'] = OrderSession::where('author_id', '=', Auth::user()->id)->orderBy('created_at', 'desc')->take(50)->get();
	//	$this->logistSession(20);
		return view('page.log_constructor', $res);
    }
	
	// 
	public function importDeleteSess( $id ) {
		$data = Array();
		$session = OrderSession::whereId($id)->select('id', 'author_id')->first();
		if ( $session ) {
			if ( $session->author_id == Auth::user()->id ) {
				// Удаляем все заказы
				Order::where('session_id', $session->id)->delete();
				// Удаляем все заказы
				Cruise::where('session_id', $session->id)->delete();
				// Удаляем все заказы
				Car::where('session_id', $session->id)->delete();
				// Удаляем сессию
				$session->delete();
				// все ок
				$data['success'] = true;
			} else $data['error'] = 'Сессия не пренадлежит пользователю.';
		} else $data['error'] = 'Сессия не существует.';
		return response()->json($data);
    }
	
	// Удаление маршрута
	public function logistDeleteRoure( $id ) {
		$data = Array();
		$route = Cruise::whereId($id)->first();
		if ( $route ) {
			// Получим сессию
			$session = OrderSession::whereId($route->session_id)->first();
			if ( $session->author_id == Auth::user()->id ) {
				// Редактируем сессию
				$session->current_count_orders+= Order::where('session_id', $session->id)->where('cruise_id', $route->id)->count();
				$session->current_count_wieght+= $route->weith_sum;
				$session->save();
				// Меняем статус заказов
				Order::where('session_id', $session->id)->where('cruise_id', $route->id)->update(['cruise_id' => 0]);
				// Удаляем маршрут
				$route->delete();
				// все ок
				$data['success'] = true;
			} else $data['error'] = 'Рейс не пренадлежит пользователю.';
		} else $data['error'] = 'Рейс не существует.';
		return response()->json($data);
    }
	
	//
	public function logistRoures() {
		$res = Array();
		$res['cruise_list'] = OrderSession::where('author_id', '=', Auth::user()->id)
								->select('order_sessions.session_todate', 'order_sessions.author_comment', 'order_sessions.id')
								->with('cruises')->orderBy('id', 'desc')->take(20)->get();
		return view('page.log_routes', $res);
    }
	
	//
	public function logistSession( $id ) {
		$data = Array();
		$session = OrderSession::whereId($id)->first();
		if ( $session->author_id == Auth::user()->id ) {
			$data['main'] = Array('count_orders' => $session->import_count_orders, 'count_wieght' => $session->import_count_wieght, 'session_todate' => $session->session_todate);
			$data['cars'] = $session->cars;
			$data['orders'] = Order::where([ ['session_id', '=',$session->id], ['cruise_id', '=',0] ])
									->with(Array('contragent' => function($query){ 
										$query->where('teritorial_id', '=', Auth::user()->territory); 
									}))->get();
			$data['cruises'] = $session->cruises;
		}
		else $data['error'] = 'Сессия не пренадлежит пользователю.';
		return response()->json($data);
	}
	
	### Загрузка сессии маршрутизации
    public function importLoading(Request $request) {	
		if( $request->hasFile('outfile') ) {
			// Получаем файл, если файл корректен - обрабатываем
			$file = simplexml_load_file( $request->file('outfile') );
			if ( $file->clients and $file->orders ) {
				$this->validate($request, ['comment' => 'required|max:200', 'datadeliveri' => 'required', 'teritorial' => 'required|integer']);
				// Записыаем сессию
				$session = OrderSession::create([
						'author_id' => Auth::user()->id,
						'author_comment' =>  $request->input('comment'),
						'session_todate' => $request->input('datadeliveri').' 00:00:01',
						'session_teritorial' => $request->input('teritorial')
					]);
				// ### Заполняем сессию
				// Торговые точки
				if ($file->clients) {
					Contragent::where('teritorial_id', Auth::user()->territory )->delete();
					$session->import_count_outlets = Contragent::pushAll($file->clients->client, Auth::user()->territory);
				}
				// Автопарк
				if ($file->cars) {
					$inf_car = Car::pushAll($file->cars->car, $session->id);
					$session->import_count_cars = $inf_car[0];
					$session->import_count_cars_wieght = $inf_car[1];
				}
				// Заказы
				if ($file->orders) {
					$inf_order = Order::pushAll($file->orders->order, $session->id);
					$session->import_count_orders = $inf_order[0];
					$session->import_count_wieght = $inf_order[1];
					$session->import_count_money = $inf_order[2];
					$session->import_count_ups = $inf_order[3];
				}
				// Кэф ?
				if ( isset($inf_order) and isset($inf_car) ) $session->coefficient = OrderSession::getCoefficient($inf_order, $inf_car);
				$session->current_count_orders = $session->import_count_orders;
				$session->current_count_wieght = $session->import_count_wieght;
				// finish
				$session->save();
			}
		}
		return redirect()->action('LogistController@importIndex');
    }
	
	// Обновить заказ .. добавить в рейс session_id
	public function logistUpChanger(Request $request, $id) {
		$this->validate($request, ['cruise_id' => 'required|integer']);
		$ret = []; 
		$data = $request->input('arrdo');
		// Получаем автомобиль
		$auto = Car::whereId( (int)$request->input('cruise_id') )->first();
		if ( $auto->session_id > 0 ) {
			// Если есть открытый рейс на авто? очистим его \\ Если что-то есть там..
			if ( $auto->cur_count > 0 ) Order::where([ ['auto_id', '=', $auto->id], ['cruise_id', '=', 0], ['session_id', '=', $auto->session_id] ])
										->update(['auto_id' => 0]);
			// Добавим заказы в рейс
			Order::where([ ['cruise_id', '=', 0], ['session_id', '=', $auto->session_id] ])->whereIn('id', $request->input('arrobj'), 'and')
										->update(['auto_id' => $auto->id]);
			// Сохраняем данные о рейсе в БД авто
			$auto->cur_weith = (double)$data[1];
			$auto->cur_summa = (double)$data[2];
			$auto->cur_count = (int)$data[0];
			$auto->save();
			// Все ок..
			$ret['success'] = true;
			$ret['title'] = 'Маршрут сохранен.';
		} else $ret['error'] = 'auto undefined';
		return response()->json($ret);
	}
	
	### Сохранение маршрута
    public function createdCruise(Request $request) {
		$this->validate($request, ['cruise_id' => 'required|integer']);
		// ID автомобиля для загрузки
		$autoID = (int)$request->input('cruise_id');
		$direcT = $request->input('arrdi');
		$ret = []; 
		// Комментарий к рейсу от юзера
		$comment = $request->input('comment');
		// Находим авто по ид
		$auto = Car::whereId( $autoID )->first();
		// Если на авто что-то грузили - продолжаем
		if ( $auto->cur_count > 0 ) {
			// Записываем рейс
			$cru = new Cruise;
				$cru->session_id	= $auto->session_id;
				$cru->code_auto		= $auto->code;
				$cru->name_auto		= $auto->name;
				$cru->weith_sum		= $auto->cur_weith;
				$cru->summa_sum		= $auto->cur_summa;
				$cru->kmdirect		= $direcT;
				$cru->status_auto	= 0;
				$cru->comment		= $comment;
			$cru->save();
			// Обновить данные сессии
			$session = OrderSession::whereId($auto->session_id)->first();
			$session->current_count_orders-= $auto->cur_count;
			$session->current_count_wieght-= $auto->cur_weith;
			$session->save();
			// Обновить данные заказов .. 1 = заблокировать
			Order::where([ ['auto_id', '=', $auto->id], ['cruise_id', '=', 0], ['session_id', '=', $auto->session_id] ])
								->update(['auto_id' => 0, 'cruise_id' => $cru->id]);
			// Обновить БД автотранспорта
			$auto->total_weith+= $auto->cur_weith;
			$auto->total_summa+= $auto->cur_summa;
			$auto->total_count+= $auto->cur_count;
			$auto->cur_weith = 0;
			$auto->cur_summa = 0;
			$auto->cur_count = 0;
			$auto->save();
			//
			$ret['success'] = true;
			$ret['title'] = 'Маршрут сохранен и готов к экспорту.';
			$ret['cruise'] = Array('id' => $cru->id, 'name_auto' => $cru->name_auto, 'weith_sum' => $cru->weith_sum, 'summa_sum' => $cru->summa_sum, 
								'kmdirect' => $cru->kmdirect, 'status_auto' => $cru->status_auto, 'comment' => $cru->comment, 
								'created_at' => $cru->created_at, 'updated_at' => $cru->updated_at);
			
		} else $ret['success'] = false;
		return response()->json($ret);
    }
	
	### Скачать маршрут
    public function uploadCruise(Request $request, $id){
		$cruise = Cruise::whereId($id)->first();
		// Был скачан нанее? Убиваем процесс
	//	if ( $cruise->status_auto ) return response()->json( Array('error' => 'The route was uploaded earlier') );
		// Определяем имя файла
		$num = ''; $k1 = (9-strlen($cruise->id)); while($k1--) $num.= '0'; $num.= $cruise->id;
		$name = 'SR('.$num.' - ).xml';
		// Сформировать XML файл и запишем его для загрузки
		$xml_file = $this->cruiseUploaderXML($cruise, $num);
		\File::put( storage_path().'/tmp/'.$name, $xml_file );
		// Обновим статус и сохраним
		$cruise->status_auto = 1;
		$cruise->save();
		// Скачаем файл и удалим его с сервера
		$headers[] = Array('Content-Description: File Transfer');
		$headers[] = Array('Content-Type: application/octet-stream');
		$headers[] = Array('Content-Disposition: attachment; filename='.$name);
		$headers[] = Array('Content-Transfer-Encoding: binary');
		$headers[] = Array('Expires: 0');
		$headers[] = Array('Cache-Control: must-revalidate');
		$headers[] = Array('Pragma: public');
		return response()->download( storage_path().'/tmp/'.$name, $name, $headers )->deleteFileAfterSend(true);
	}
	
	//  Скачать XML рейса по ID
    public function cruiseUploaderXML($cruise, $num = 0) {
		$session = OrderSession::whereId($cruise->session_id)->first();
		$orders = Order::where([ ['cruise_id', '=', $cruise->id], ['session_id', '=', $cruise->session_id] ])->select('number')->orderBy('code', 'asc')->get();
		$orderdata = \Carbon\Carbon::parse($session->session_todate)->format('Ymd').'000000';
		// составляем файл и отправляем его пользователю
		$t = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$t.= '<route auto="'.$cruise->code_auto.'" routetime="{&quot;S&quot;,&quot;&quot;}" length="{&quot;N&quot;,0}">'."\n";
		$t.= '<params num="'.$num.'" data="{&quot;D&quot;,'.$orderdata.'}" stime="{&quot;D&quot;,00010101000000}" ftime="{&quot;D&quot;,00010101000000}"/>'."\n";
		$i = 0;
		foreach ( $orders as $ord ) { $i++; 
			$t.= '<order ordernum="'.$ord->number.'" orderdata="{&quot;D&quot;,'.$orderdata.'}" timein="{&quot;D&quot;,00010101000000}" number="'.$i.'"/>'."\n";
		}
		$t.= '<point type="Старт"/>'."\n";
		$t.= '<point type="Погрузка"/>'."\n";
		$t.= '<point type="Разгрузка"/>'."\n";
		$t.= '</route>';
		return $t;
    }
}
