<?php

namespace Shanerutter\LaravelAdminEmailTwoFactor;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Shanerutter\LaravelAdminEmailTwoFactor\Http\Middleware\AuthAdminEmailTwoFactor;

class AuthAttemptsServiceProvider extends ServiceProvider
{
    /**
     * @param AuthEmailTwoFactor $extension
     */
    public function boot(AuthEmailTwoFactor $extension, Router $router, Kernel $kernel)
    {
        if (!AuthEmailTwoFactor::boot()) {
            return;
        }

        // Register middleware
        $router->aliasMiddleware('admin.auth.2fa.email', AuthAdminEmailTwoFactor::class);
        $router->pushMiddlewareToGroup('admin', 'admin.auth.2fa.email');

        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, AuthEmailTwoFactor::$group);
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/shanerutter/laravel-admin-email-two-factor')],
                AuthEmailTwoFactor::$group
            );
        }

        $this->app->booted(function () {
            AuthEmailTwoFactor::routes(__DIR__ . '/../routes/web.php');
        });
    }
}
