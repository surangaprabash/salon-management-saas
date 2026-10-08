<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(function () {
            $rule = Password::min(10)->letters()->mixedCase()->numbers();

            return $this->app->environment('production') ? $rule->uncompromised() : $rule;
        });
    }
}


// ?php

// namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
// use Illuminate\Validation\Rules\Password;

// class AppServiceProvider extends ServiceProvider
// {
//     public function register(): void
//     {
//         //
//     }

//     public function boot(): void
//     {
//         Password::defaults(function () {
//             $rule = Password::min(10)->letters()->mixedCase()->numbers();

//             return $this->app->isProduction() ? $rule->uncompromised() : $rule;
//         });
//     }
// } 