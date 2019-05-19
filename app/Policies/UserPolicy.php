<?php
namespace App\Policies;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
	
	// Пользователь активирован?
	public function activated(User $user) {
		// Показатель активности
		$user->lastaction = \Carbon\Carbon::now();
		$user->save();
		// 0 - не активирован, 1 - активирован, 2 - заблокирован
		return $user->status === 1;
	}
	
	//
	public function enableDebugbar(User $user) {
		return true;
	}
	
	//
	public function admin(User $user) {
		return $user->admin === 1;
	}
	
}
