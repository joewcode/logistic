<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use App\Models\BusinessPlan;
use App\Models\OrderSession;

class HomeController extends Controller
{

    //
    public function index() {
		$res['UTerritory'] = Auth::user()->territory;
		$res['stats'] = $this->getListStatistic( $res['UTerritory'] );
		$res['busblan'] = BusinessPlan::where('auhtor_id', Auth::user()->id)->orderBy('status', 'asc')->orderBy('created_at', 'asc')->take(100)->get();
		\Artisan::call('view:clear');
		return view('page.home', $res);
    }
	
	//
	public function createBP(Request $request) {
		$this->validate($request, ['newtask' => 'required|min:10|max:255']);
		BusinessPlan::create([ 
				'auhtor_id' => Auth::user()->id, 
				'text' => $request->input('newtask')
			]);
		return redirect()->action('HomeController@index');
	}
	
	//
	public function searchForm(Request $request) {
		$this->validate($request, ['top-search' => 'required|min:2|max:100']);
		$str = $request->input('top-search');
		$res = Array();
		return view('page.search', $res);
	}

	//
	public function profile() {
		$res = Array();
		return view('page.profile', $res);
	}

	//
	public function profile_save(Request $request) {
		$this->validate($request, [['password' => 'required|string'], ['npassword' => 'required|string|min:6|confirmed']] );
		$oldpwd = $request->input('password') ?: '';
		$newpwd = $request->input('npassword') ?: '';
		$rnpwd = $request->input('npassword_confirmation') ?: '';
		if ( !empty($oldpwd) and !empty($newpwd) and !empty($rnpwd) ) {
			$usr = \Auth::user();
			if ( password_verify($oldpwd, $usr->password) and $newpwd === $rnpwd) {
				$usr->password = bcrypt($newpwd);
				$usr->save();
			}
		}
		return redirect()->action('HomeController@profile');
	}

	
	//
	public function eqfeedCollection(Request $request) {
	//	$dt['features'][] = Array(	'type' => 'Feature', 'properties' => Array( 'mag' => 8.7 ), 'geometry' => Array( "type" => "Point", "coordinates" => [29.535276, 47.740776, 200] ) );
		$features;
		// Определяем период
		$season = $this->timeToWeek();
		$sessions = OrderSession::where([ ['session_todate', '>=', $season['start']], ['session_todate', '<=', $season['end']] ])
								->where('session_teritorial', Auth::user()->territory)->orderBy('session_todate', 'desc')
								->with(Array('orders.contragent' => function($query){ $query->where('teritorial_id', '=', Auth::user()->territory); }))
								->take(50)
								->get();
		foreach ($sessions as $session) {
			foreach ($session->orders as $order) {
				if ( !$order->contragent ) continue;
				$features[] = Array('type' => 'Feature',
									'properties' => ['mag' => round($this->significance($order->weith), 1), 'status' => 'REVIEWED'],
									'geometry' => ['type' => 'Point', 'coordinates' => [round($order->contragent->dolgota, 6), round($order->contragent->shirota, 6), 100]],
									'id' => $order->id
								);
			}
		}
	//	dd($dt); {"type":"Feature","properties":{"mag":5.4,"place":"48km SSE of Pondaguitan, Philippines","time":1348176066,"tz":480,"url":"http://earthquake.usgs.gov/earthquakes/eventpage/usc000csx3","felt":2,"cdi":3.4,"mmi":null,"alert":null,"status":"REVIEWED","tsunami":null,"sig":"449","net":"us","code":"c000csx3","ids":",usc000csx3,","sources":",us,","types":",dyfi,eq-location-map,general-link,geoserve,historical-moment-tensor-map,historical-seismicity-map,nearby-cities,origin,p-wave-travel-times,phase-data,scitech-link,tectonic-summary,"},"geometry":{"type":"Point","coordinates":[126.3832,5.9775,111.16]},"id":"usc000csx3"},
		$dt = Array('type' => 'FeatureCollection', 'features' => $features);
		return response()->json($dt);
	}
	
	//
	public function updateBP(Request $request) {
		$this->validate($request, ['id' => 'required|integer']);
		$dt = Array('success' => false);
		$bp = BusinessPlan::whereId( $request->input('id') )->first();
		// Чей?
		if ( $bp->auhtor_id === Auth::user()->id ) {
			$bp->status = $bp->status ? 0 : 1;
			$bp->save();
			$dt['success'] = true;
		}
		return response()->json($dt);
	}

	//
	private function getListStatistic($ter) {
		// Возвращаем строку
		$arr = Array( 'tonn' => Array(0, 0, 0, 0, 0), 'ordc' => Array(0, 0, 0, 0, 0), 'summ' => Array(0, 0, 0, 0, 0), 'kmtr' => Array(0, 0, 0, 0, 0), 'upsp' => Array(0, 0, 0, 0, 0) );
		// Определяем период
		$season = $this->timeToWeek();
		// Вытаскиваем с БД
		$sessions = OrderSession::where([ ['session_todate', '>=', $season['start']], ['session_todate', '<=', $season['end']] ])
								->where('session_teritorial', Auth::user()->territory)->orderBy('session_todate', 'desc')
								->with(Array('orders' => function($query){ $query->where('cruise_id', '>', 0); }))->get();
		foreach ( $sessions as $session ) {
			$i = $season['list'][substr($session->session_todate, 0, 10)];
			if ( $i[0] === true ) {
				foreach ( $session->orders as $ord ) { 
					$arr['tonn'][$i[1]]+= $ord->weith;
					$arr['ordc'][$i[1]]+= 1;
					$arr['summ'][$i[1]]+= $ord->summa;
				//	$arr['kmtr'][$i[1]]+= 0;
				//	$arr['upsp'][$i[1]] = 0; 
				}
			}
		}
		// round
		$r = $arr['tonn'];
		$arr['tonn'] = Array( round($r[0], 2), round($r[1], 2), round($r[2], 2), round($r[3], 2), round($r[4], 2));
		$r = $arr['summ'];
		$arr['summ'] = Array( round($r[0], 2), round($r[1], 2), round($r[2], 2), round($r[3], 2), round($r[4], 2));
		// 
		return json_encode($arr);
	}
	
	private function timeToWeek() {
		$data = Array();
		$week_start = (new \DateTime())->setISODate(date("Y"),date("W"))->format("Y-m-d H:i:s");
		$start = Carbon::createFromFormat("Y-m-d H:i:s", $week_start);
		$start->hour(0)->minute(0)->second(0)->modify('+1 day');
		$end = $start->copy()->endOfWeek()->modify('-1 day');
		// Расделение по дням
		$currentDay = $start->copy(); 
		$ordDList = Array(	$currentDay->format("Y-m-d") => Array(true, 0), 
							$currentDay->modify('+1 day')->format("Y-m-d") => Array(true, 1), 
							$currentDay->modify('+1 day')->format("Y-m-d") => Array(true, 2), 
							$currentDay->modify('+1 day')->format("Y-m-d") => Array(true, 3), 
							$currentDay->modify('+1 day')->format("Y-m-d") => Array(true, 4)
						);
		$data['start'] = $start;
		$data['end'] = $end;
		$data['list'] = $ordDList;
		return $data;
	}
	
	//
	private function significance($w) {
		if ( $w < 100 ) $d = 3;
		elseif ( $w < 300 ) $d = 4;
		elseif ( $w < 700 ) $d = 4.5;
		elseif ( $w < 1000 ) $d = 5;
		elseif ( $w < 1500 ) $d = 5.5;
		elseif ( $w < 2000 ) $d = 6;
		elseif ( $w < 5000 ) $d = 7;
		else $d = 8;
		return $d;
	}
	
}
