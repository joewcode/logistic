<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $r)
    {
		//  Debugbar
		$cookie = $r->cookie('debugbar_enabled', false);
		$enabled = false;
		if ($cookie !== false) {
			$matches = \Hash::check(implode(':', [ config('app.key'), date('Ymd'), $r->server('REMOTE_ADDR') ]), $cookie);
			if ($matches) $enabled = true;
		}
		if ($enabled) $this->app['config']->set('debugbar.enabled', true);
		
		// Название филиалов на все шаблоны
		\View::share('territory', function($id){ return \Jhelp::territoryToName($id); });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
		
    }
}
