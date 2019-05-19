<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Phonebook extends Model
{
	public $timestamps = true;
	protected $fillable = ['id', 'territory', 'fio', 'descr', 'text', 'created_at', 'updated_at'];
	
	
	
	
	
}
