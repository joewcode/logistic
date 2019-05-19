<?php
namespace App\Http;

class Jhelp {
	
	// Получить по ID название территории
	public static function territoryToName($id){ return Array('Нет', 'Филиал №1', 'Филиал №2', 'Филиал №3')[$id]; }
	
	// Получить по название территории его ID
	public static function territoryToId($name){  }
	
}
