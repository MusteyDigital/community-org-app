<?php
namespace App\Providers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        View::composer('layouts.navigation', function ($view) {
            if (Auth::check()) {
                $notifications = Auth::user()->notifications()->latest()->take(10)->get();
                $unreadCount = Auth::user()->unreadNotifications()->count();
            } else {
                $notifications = collect();
                $unreadCount = 0;
            }
            $view->with(['notifications' => $notifications, 'unreadCount' => $unreadCount]);
        });
    }
}