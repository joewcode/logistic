<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    //
	public $timestamps = false;
	
	
	### Записываем автопарк для сессии
	public static function pushAll( $list, $sid ) {
	//  name="ГАЗ" nomer="ВН00000" dlina="0" shirina="0" visota="0" obyem="0" tonag="5" code="ОД0000416"
		$i = 0; $tn = 0; $arr = Array();
		foreach ( $list as $val ) { $i++;
			$t = ( (int)str_replace(' ', '', $val['tonag']) ) * 1000;
			$tn+= $t;
			$arr[] = Array(	'session_id' => $sid,
							'name' => $val['name'] ? htmlspecialchars($val['name']) : 'Not name '.$i,
							'nomer' => $val['nomer'] ? htmlspecialchars($val['nomer']) : 'not',
							'tonag' => $t,
							'code' => $val['code'] ? htmlspecialchars($val['code']) : 'not',
						);
			// Делаем запись N строк за раз
			if ( $i % 500 == 0  ) {
				Car::insert($arr);
				$arr = Array(); 
			}
		}
		// Дописываем остаток
		Car::insert($arr);
		return Array($i, $tn);
	}
	
}
