<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BusinessPlan extends Model
{
	protected $fillable = ['id', 'auhtor_id', 'text', 'status', 'created_at', 'updated_at'];
    //
	public $timestamps = true;
	
}
