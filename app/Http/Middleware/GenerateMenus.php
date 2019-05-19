<?php
namespace App\Http\Middleware;
use Closure;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		//->append('</span>')->prepend('<span class="nav-label">');
		
		\Menu::make('JNavigate', function ($menu) {
			$menu->add('Главная', ['url' => ''])->prepend('<i class="fa fa-home"></i>'); 
			
			$menu->add('АРМ Логиста', ['url' => '#'])->prepend('<i class="fa fa-truck"></i>')->link->href('javascript:void(0);');
				$menu->aRMLogista->add('Импорт заказов', ['url' => 'log/import'])->prepend('<i class="fa fa-download"></i>');
				$menu->aRMLogista->add('Карта-конструктор', ['url' => 'log/constructor'])->prepend('<i class="fa fa-map-marker"></i>');
				$menu->aRMLogista->add('Мои маршруты', ['url' => 'log/routes'])->prepend('<i class="fa fa-road"></i>');
				
			$menu->add('АРМ Диспетчера', ['url' => '#'])->prepend('<i class="fa fa-android"></i>')->link->href('javascript:void(0);');
				$menu->aRMDispetchera->add('Карта доставки', ['url' => 'disp/map'])->prepend('<i class="fa fa-globe"></i>');
				$menu->aRMDispetchera->add('Справочник', ['url' => 'disp/pbook'])->prepend('<i class="fa fa-address-book"></i>');
				
		//	$menu->add('АРМ Аналитика', ['url' => '#'])->prepend('<i class="fa fa-bar-chart-o"></i>')->link->href('javascript:void(0);');
		//		$menu->aRMAnalitika->add('Расход ГСМ', ['url' => 'assay/gsm'])->prepend('<i class="fa fa-flask"></i>');
		//		$menu->aRMAnalitika->add('Маршруты ТП', ['url' => 'assay/tprout'])->prepend('<i class="fa fa-bicycle"></i>');
			
			
			// Admin menu only
			if ( \Gate::allows('admin', \Auth::user()) ) {
				$menu->add('Управление', ['url' => '#'])->prepend('<i class="fa fa-wrench"></i>')->link->href('javascript:void(0);');
					// Пользователи
					$menu->upravlenie->add('Пользователи', ['url' => 'adm/users'])->prepend('<i class="fa fa-user"></i>');
					
					
					// Дебагбар
					$menu->upravlenie->add('Debugbar', ['url' => 'adm/initdbug'])->prepend('<i class="fa fa-binoculars"></i>');
			}
		});
        return $next($request);
    }
}
