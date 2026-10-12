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
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $isDevMode = false;
            try {
                $isDevMode = \App\Models\AppSetting::isDevMode();
            } catch (\Throwable $e) {
                // Ignore if database/table not ready yet
            }

            $currentCustomerType = \App\Models\Customer::TYPE_RETAIL;
            $currentCustomer = null;

            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $currentCustomerType = $user->getCustomerType();
                $currentCustomer = $user->customer;
            }

            $view->with([
                'isDevMode' => $isDevMode,
                'currentCustomerType' => $currentCustomerType,
                'currentCustomer' => $currentCustomer,
            ]);
        });
    }
}
