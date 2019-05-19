<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderSession;
use App\Models\Phonebook;
use App\Models\Cruise;


class DispController extends Controller
{
	
    // Карта доставки
	public function viewMap() {
		$res['userBP'] =  Array([1], [2], [3], [4], [5], [6], [7]);
		$res['territory'] = \Auth::user()->territory;
		$res['toDate'] = \Carbon\Carbon::today();
		
		return view('page.dispmap', $res);
	}
	
	// Данные по доставке
	public function viewMapOptions(Request $request) {
		$this->validate($request, ['terr' => 'required|integer', 'deliv' => 'required|max:20']);
		$we = Array('terr' => $request->input('terr'), 'deliv' => $request->input('deliv'));
		$arr['orders'] = Array();
		$arr['cruises'] = Array();
		$obj = OrderSession::where('session_teritorial', $we['terr'])
							->where('session_todate', $we['deliv'].' 00:00:01')
							->with(['orders.contragent' => function($query) use ($we){ $query->where('teritorial_id', '=', $we['terr']); }, 'cruises'])
							->get();
		foreach ( $obj as $sesion ) {
			foreach ( $sesion->orders as $ses ) {
				if ( $ses->cruise_id > 0 ) $arr['orders'][] =  $ses;
			}
			foreach ( $sesion->cruises as $crs ) {
				if ( $crs->status_auto == 1 ) $arr['cruises'][] = $crs;
			}
		}
		return response()->json($arr);
	}
	
	// Страничка справочника
	public function viewPbook() {
		return view('page.dispbook');
	}
	
	// Получить справочник
	public function ajaxGetPbook(Request $request) {
		$arr = Array(); 
	/*
		"_search" => "false"
		"nd" => "1511786508358"
		"rows" => "20"
		"page" => "1"
		"sidx" => null
		"sord" => "asc"
			"filters" => "{"groupOp":"AND","rules":[{"field":"fio","op":"cn","data":"oooo"}]}"
			"searchField" => null
			"searchString" => null
			"searchOper" => null
	*/
		
		$rows = Phonebook::orderBy('territory', 'asc')->orderBy('fio', 'asc')->paginate(20);
		foreach ($rows as $row) {
			$tr = \Jhelp::territoryToName($row->territory);
			$arr[] = Array('id' => $row->id, 'fio' => $row->fio, 'text' => $row->text, 'territory' => $tr, 'descr' => $row->descr, 'updated_at' => $row->updated_at->format('d.m.Y H:i'));
		}
		
		return response()->json($arr);
	}
	
	// Редактирование справочника
	public function ajaxEditPbook(Request $request) {
		$arr = Array('success' => true);
		$param = Array(	"fio" => $request->fio ? $request->fio : 'Имя',
						"text" => $request->text ? $request->text : 'Телефоны',
						"territory" => $request->territory ? $request->territory : 0,
						"descr" => $request->descr ? $request->descr : '',
						"oper" => $request->oper ? $request->oper : null,
						"id" => $request->id ? $request->id : 0);
		// edit	add del
		switch ( $param['oper'] ) {
			case 'edit': $this->gridEditFunc($param); break;
			case 'add':  $this->gridAddFunc($param); break;
			case 'del':  Phonebook::whereId($param['id'])->delete(); break;
			default: $arr['success'] = false;
		}
		return response()->json($arr);
	}
	
	//
	private function gridEditFunc($par) {
		$row = Phonebook::whereId($par['id'])->first();
		if ( $row ) {
			$row->fio = $par['fio'];
			$row->text = $par['text'];
			$row->territory = $par['territory'];
			$row->descr = $par['descr'];
			$row->save();
		}
		return true;
	}
	
	//
	private function gridAddFunc($par) {
		$row = new Phonebook;
		$row->fio = $par['fio'];
		$row->text = $par['text'];
		$row->territory = $par['territory'];
		$row->descr = $par['descr'];
		$row->save();
		return true;
	}
	
	### Отображаем сессию маршрутов по ID
    public function viewDriverMap( $id ) {
		// Есть ли рейс?
		$session = Cruise::where([['id', '=', $id], ['status_auto', '=', 1]])->with('session')->select('id', 'session_id')->first();
		if ( !$session ) abort(404);
		// Получаем заказы и контрагентов
		$cruise = Cruise::whereId($session->id)->with(['orders.contragent' => function($query) use ($session){ $query->where('teritorial_id', $session->session->session_teritorial); }])->first();
		// Отдаем контент
		$res['CruiseOrderList'] = $cruise->orders;
		$res['OTerritory'] = $session->session->session_teritorial;
		$couTT = count( $this->uniqueOrdersGet( $res['CruiseOrderList'] ) );
		$res['CruiseInfo'] = Array('id' => $cruise->id, 'name_auto' => $cruise->name_auto, 'weith_sum' => $cruise->weith_sum, 
								'summa_sum' => $cruise->summa_sum, 'kmdirect' => $cruise->kmdirect, 
								'comment' => $cruise->comment, 'created_at' => $session->session->created_at->format('d.m.Y H:i'), 
								'all_count' => count($res['CruiseOrderList']), 'tt_count' => $couTT );
		//
		return view('page.drivermap', $res);
    }
	
	//
	// Получить кол-во уникальных ТТ
	private function uniqueOrdersGet( $orders ) {
		$ret = Array(); $temp = Array();
		foreach ( $orders as $ord ) {
			if ( isset($temp[$ord->code]) ) continue;
			$temp[$ord->code] = true;
			$ret[] = $ord;
		}
		return $ret;
	}
	
	
}
