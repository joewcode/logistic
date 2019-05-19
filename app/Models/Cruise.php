<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cruise extends Model
{
	protected $fillable = ['session_id', 'name_auto', 'weith_sum', 'summa_sum', 'kmdirect', 'status_auto', 'comment'];
    //
	public $timestamps = false;
	
	//
	public function orders()
	{
		return $this->hasMany('App\Models\Order', 'cruise_id');
	}
	
	//
	public function session()
	{
		return $this->hasOne('App\Models\OrderSession', 'id', 'session_id');
	}
}
