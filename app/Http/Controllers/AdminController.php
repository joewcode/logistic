<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;

class AdminController extends Controller
{
    //
	public function viewUsers() {
		$res = Array();
		$res['users_all'] = User::where('id', '>', '0')->get();
		return view('admin.users', $res);
	}
	
	//
	public function getUsersData($id) {
		$usr = User::whereId($id)->first();
		return response()->json($usr);
	}
	
	//
	public function editUser(Request $request) {
		$euserid = $request->input('euserid') ?: 0;
		$efullname = $request->input('efullname') ?: 'Пользователь';
		$eusermail = $request->input('eusermail') ?: '';
		$euserpos = $request->input('euserpos') ?: '';
		$enewpasswd = $request->input('enewpasswd') ?: '';
		$eteritorial = $request->input('eteritorial') ?: 0;
		$eactivated = $request->has('eactivated') ? 1 : 0;
		$eadministrator = $request->has('eadministrator') ? 1 : 0;
		//
		$user = User::whereId($euserid)->first();
		if ( $user ) {
			$user->name = $efullname;
			$user->email = $eusermail;
			if ( strlen($enewpasswd) > 5 ) $user->password = bcrypt($enewpasswd);
			$user->position = $euserpos;
			$user->territory = $eteritorial;
			$user->status = $eactivated;
			$user->admin = $eadministrator;
			$user->save();
		}
		return redirect()->action('AdminController@viewUsers');
	}
	
	//
	public function admSetCookie(Request $request) {
		$this->authorize('enableDebugbar', \Auth::user()); // Check with UserPolicy if you are authorized to do this
		$is = $request->cookie('debugbar_enabled', false);
		if (!$is) {
			$code = \Hash::make(implode(':', [
				config('app.key'), // This is secret, so that nobody else can fake this cookie
				date('Ymd'), // Force the cookie to expire tomorrow
				$request->server('REMOTE_ADDR'), // Cookie only valid for current IP
			]));
			return redirect()->back()->withCookie(
				'debugbar_enabled', $code, 1440
			);
		} else {
			return redirect()->back()->withCookie('debugbar_enabled', null);
		}
	}
	
}
