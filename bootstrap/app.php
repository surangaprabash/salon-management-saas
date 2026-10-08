<?php

use App\Http\Middleware\EnsureSalonBillingAccess;
use App\Http\Middleware\EnsureSalonIsActive;
use App\Http\Middleware\EnsureUserIsSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Super Admin routes; restricted to ADMIN_DOMAIN when it is set.
            $admin = Route::middleware('web');

            if ($domain = config('admin.domain')) {
                $admin = $admin->domain($domain);
            }

            $admin->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'super_admin' => EnsureUserIsSuperAdmin::class,
            'salon.active' => EnsureSalonIsActive::class,
            'salon.billing' => EnsureSalonBillingAccess::class,
        ]);

        // Guests hitting protected admin pages go to the admin login.
        $middleware->redirectGuestsTo(fn (Request $request) => route('admin.login'));

        // Already signed-in users visiting the login page.
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->isSuperAdmin()
            ? route('admin.dashboard')
            : url('/'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();