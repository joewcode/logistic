<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $fillable = ['session_id', 'number', 'code', 'koment', 'weith', 'summa', 'razvoz'];
    //
	public $timestamps = false;
	
	### Формируем и записывааем список заказов order
	public static function pushAll( $list, $sid ) {
		$i = $w = $u = 0; $m = 0.0; $arr = Array();
		foreach ( $list as $val ) { $i++;
			$ww = (float)str_replace(' ', '', str_replace(',','.', $val['weith']) );
			$ss = (float)str_replace(' ', '', str_replace(',','.', $val['summa']) );
			$w+= $ww;
			$m+= $ss;
			if ( $val['razvoz'] == 'Славутич' ) $u++;
			//
			$arr[] = Array(	'session_id' => $sid,
							'number' => $val['number'],
							'code' => $val['code'],
							'koment' => $val['koment'] ? htmlspecialchars($val['koment']) : ' ',
							'weith' => $ww,
							'summa' => $ss,
							'razvoz' => $val['razvoz']
						);
			// Делаем запись N строк за раз
			if ( $i % 500 == 0  ) {
				Order::insert($arr);
				$arr = Array();
			}
		}
		// Дописываем остаток
		Order::insert($arr);
		return Array($i, $w, $m, $u);
	}
	
	public function contragent()
	{
		return $this->hasOne('App\Models\Contragent', 'code', 'code');
	}
	
}
