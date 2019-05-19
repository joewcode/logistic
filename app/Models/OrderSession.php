<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderSession extends Model
{
	//
	protected $fillable = ['author_id', 'author_comment', 'session_todate', 'session_teritorial'];
	
    //
	public $timestamps = true;
	
	//
	public static function getUserSessions($id) {
		$arr = Array(); $aname = Array('', 'Филиал №1', 'Филиал №2', 'Филиал №3');
		$link =  OrderSession::where('author_id', $id)->select('order_sessions.*', 'users.name')
			->leftJoin('users', 'order_sessions.author_id', '=', 'users.id')
			->orderBy('order_sessions.created_at', 'desc')->take(60)->get();
		foreach ( $link as $val ) {
			$val->session_teritorial = $aname[ $val->session_teritorial ];
			$arr[] = $val;
		}
		return $arr;
	}
	
	// Вымышленная вычисляемая единица
	public static function getCoefficient($ord, $car){ 
		$a = $ord[1] / $ord[0] ; // Средний вес на 1 ТТ
		$b = $ord[3] * 1; // 1 тт за 1 m  
		$c = ( $ord[1] > $car[1] ) ? ($ord[1] - $car[1]) : 1; // Привышение возможностей автопарка
		$res = ( $a + $b + $c ) * $car[0];// Сумма параметров нагрузки умнажается на кол-во автопарка х1
		return $res / 100;
	}
    
	//
	public function cars() {
        return $this->hasMany('App\Models\Car', 'session_id');
    }
	
	//
	public function orders() {
        return $this->hasMany('App\Models\Order', 'session_id');
    }
	
	//
	public function cruises() {
        return $this->hasMany('App\Models\Cruise', 'session_id');
    }
	
	
}
