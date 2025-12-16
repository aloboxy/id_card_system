<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        if (!app()->runningInConsole()) {
            \Illuminate\Support\Facades\View::composer(['layouts.app', 'layouts.auth', 'auth.login', 'settings.edit'], function ($view) {
                // Wrap in try-catch to prevent issues during early migration stages if table doesn't exist yet
                try {
                    $systemName = \App\Models\Setting::getValue('system_name', 'ID Card System');
                    $systemLogo = \App\Models\Setting::getValue('system_logo');
                    $view->with('systemName', $systemName);
                    $view->with('systemLogo', $systemLogo);
                } catch (\Exception $e) {
                    // Fallback if table not found or other db error
                    $view->with('systemName', 'ID Card System');
                    $view->with('systemLogo', null);
                }
            });
        }
    }
}
