<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('chat', fn (Request $request) => Limit::perMinute(6)->by($request->user()->id)
            ->response(fn () => response()->json(['message' => 'Has enviado varias consultas seguidas. Espera un minuto para continuar.'], 429))
        );
    }
}
