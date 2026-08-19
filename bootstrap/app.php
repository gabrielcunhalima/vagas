<?php

use App\Http\Middleware\CandidatoAuth;
use App\Http\Middleware\CheckPerfil;
use App\Http\Middleware\EnsureCandidatoEmailIsVerified;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'perfil' => CheckPerfil::class,
            'candidato.auth' => CandidatoAuth::class,
            'candidato.verified' => EnsureCandidatoEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
