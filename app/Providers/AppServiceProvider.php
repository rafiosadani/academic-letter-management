<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
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
        Paginator::defaultView('vendor.pagination.lineone');
        Paginator::defaultSimpleView('vendor.pagination.lineone');

        Carbon::setLocale('id');

        Gate::policy(User::class, UserPolicy::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(
                env('LOGIN_RATE_LIMIT', 5)
            )->by($email, $request->ip());
        });
    }
}
