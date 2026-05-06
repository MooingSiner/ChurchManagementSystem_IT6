<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        RateLimiter::for('scan-attendance', function (Request $request) {
            return [
                Limit::perMinute(30)->by($request->ip()),
            ];
        });

        View::composer([
            'dashboard',
            'members',
            'events',
            'attendance',
            'report',
        ], function ($view) {
            $view->with('navigationBadges', [
                'attendance_pending' => DB::table('attendances')->where('status', 'Pending')->count(),
            ]);

            $view->with('currentRoleLabel', Auth::user()?->role_label);
        });
    }
}
