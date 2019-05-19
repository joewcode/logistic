<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Driver map (guest)
Route::get('map/{id}', 'DispController@viewDriverMap')->name('viewDriverMap');

// Route auth module
Auth::routes();

// Далее часть только для авторизированных и активированных --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
Route::group(['middleware' => ['auth', 'can:activated,App\User']], function() {
	
	// Главная страничка + FAQ + роуты профиля Artisan::call('view:clear');
	Route::get('/', 'HomeController@index')->name('home');
	Route::post('/', 'HomeController@createBP')->name('createBP');
	Route::post('chsbp', 'HomeController@updateBP')->name('updateBP');
	Route::post('eqfeed', 'HomeController@eqfeedCollection')->name('eqfeedCollection');
	Route::get('search', function() { abort(404, 'Нет данных для поиска!'); });
	Route::post('search', 'HomeController@searchForm')->name('searchForm');
	Route::get('faq', 'FAQController@index')->name('faq');
	
	Route::get('profile', 'HomeController@profile')->name('profile');
	Route::post('profile', 'HomeController@profile_save')->name('profile_save');
	

	// @@@ APM логиста
		// Импорт
		Route::get('log/import', 'LogistController@importIndex')->name('importIndex');
		Route::post('log/import', 'LogistController@importLoading')->name('importLoading');
		Route::post('log/import/{id}/delete', 'LogistController@importDeleteSess')->name('importDeleteSess');
		
		// Конструктор
		Route::get('log/constructor', 'LogistController@logistIndex')->name('logistIndex');
		Route::post('log/constructor/{id}', 'LogistController@logistSession')->name('logistSession');
		Route::post('log/constructor/to/{id}', 'LogistController@logistUpChanger')->name('logistUpChanger');
		Route::post('log/constructor/created/{id}', 'LogistController@createdCruise')->name('createdCruise');
		Route::get('log/constructor/download/{id}', 'LogistController@uploadCruise')->name('uploadCruise');
	
		// Страница просмотра маршрутов
		Route::get('log/routes', 'LogistController@logistRoures')->name('logistRoures');
		Route::post('log/routes/{id}/delete', 'LogistController@logistDeleteRoure')->name('logistDeleteRoure');
		
	// @@@ APM диспетчера
		// Карта
		Route::get('disp/map', 'DispController@viewMap')->name('viewMap');
		Route::post('disp/map/opt', 'DispController@viewMapOptions')->name('viewMapOptions');
		// Phonebook 
		Route::get('disp/pbook', 'DispController@viewPbook')->name('viewPbook');
		Route::post('disp/pbook/get', 'DispController@ajaxGetPbook')->name('ajaxGetPbook');
		Route::post('disp/pbook/edit', 'DispController@ajaxEditPbook')->name('ajaxEditPbook');
	
	// @@@ APM диспетчера
		// Статистика ГСМ
		Route::get('assay/gsm', 'AnalystController@viewGSMlog')->name('viewGSMlog');
		// Маршруты ТП
		Route::get('assay/tprout', 'AnalystController@viewTPrours')->name('viewTPrours');
	
	
	// --- Admin group only
	Route::group(['middleware' => 'can:admin,App\User'], function () {
		// Пользователи
		Route::get('adm/users', 'AdminController@viewUsers')->name('viewUsers');
		Route::get('adm/users/{id}/get', 'AdminController@getUsersData')->name('getUsersData');
		Route::post('adm/users/edit', 'AdminController@editUser')->name('editUser');
		
		
	//	Route::get('adm/users', ['uses' => 'Admin\Users@index']);
	//	Route::post('adm/users', ['uses' => 'Admin\Users@create']);
	//	Route::get('adm/users/{id}', ['uses' => 'Admin\Users@getUserData']);
		
		// Юзер
	//	Route::get('user/{id}', ['uses' => '\App\User@show']);
		
		// Debugbar
		Route::get('adm/initdbug', 'AdminController@admSetCookie')->name('admSetCookie');
	});
});
