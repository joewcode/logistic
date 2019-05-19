<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Contragent extends Model
{
	protected $fillable = ['teritorial_id', 'code', 'name', 'addresses', 'nachalo', 'konec', 'shirota', 'dolgota'];
    //
	public $timestamps = false;
	
	public static function pushAll( $list, $tid ) {
		$i = 0; $arr = Array();
		foreach ( $list as $val ) { $i++;
			$arr[] = Array(	'teritorial_id' => $tid, 
							'code' => $val['code'],
							'name' => $val['name'] ? htmlspecialchars($val['name']) : ' ',
							'addresses' => $val['addresses'] ? htmlspecialchars($val['addresses']) : ' ',
							'nachalo' => substr($val['nachalo'], 11, strlen($val['nachalo'])-14),
							'konec' => substr($val['konec'], 11, strlen($val['konec'])-14),
							'shirota' => str_replace(',', '.', $val['shirota']),
							'dolgota' => str_replace(',', '.', $val['dolgota'])
						);
			// Делаем запись N строк за раз
			if ( $i % 500 == 0  ) {
				Contragent::insert($arr);
				$arr = Array();
			}
		}
		// Дописываем остаток
		Contragent::insert($arr);
		return $i;
	}
	
	
}
