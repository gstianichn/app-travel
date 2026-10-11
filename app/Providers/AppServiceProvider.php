<?php

namespace App\Providers;

use App\Models\AppSetting;
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
