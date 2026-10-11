<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\Route;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Web requests only: in the console this limit would also stop `php artisan serve` after two minutes.
        if (app()->isLocal() && ! app()->runningInConsole()) {
            @ini_set('max_execution_time', '120');
        }

        // Dates in the UI are written for Indonesian readers (Senin, 12 Oktober), whatever the app locale is.
        Carbon::setLocale('id');

        View::composer('components.footer', function (ViewInstance $view): void {
            $view->with([
                // One route per pair of cities, whichever direction is cheaper, so the list covers more of the map.
                'footerRoutes' => Route::cheapestPerCityPair()
                    ->unique(function (Route $route) {
                        $cities = [$route->origin->city ?: $route->origin->name, $route->destination->city ?: $route->destination->name];
                        sort($cities);

                        return implode('|', $cities);
                    })
                    ->take(6),
                'footerContact' => [
                    'phone' => AppSetting::companyPhone(),
                    'whatsapp' => AppSetting::whatsappUrl(),
                    'email' => AppSetting::get('company_email'),
                    'address' => AppSetting::get('company_address'),
                ],
            ]);
        });
    }
}
