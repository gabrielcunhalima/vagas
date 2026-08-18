<?php

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
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'perfil'        => \App\Http\Middleware\CheckPerfil::class,
            'candidato.auth' => \App\Http\Middleware\CandidatoAuth::class,
            'candidato.verified' => \App\Http\Middleware\EnsureCandidatoEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
            $status = $response->getStatusCode();

            if (! in_array($status, [403, 404, 419, 500, 503], true) || $request->expectsJson()) {
                return $response;
            }

            // Migração Inertia -> Blade em andamento: só reescreve para o componente
            // Inertia quando a requisição é de fato uma visita Inertia (páginas ainda
            // não migradas). Fora disso, cai no padrão do Laravel — que já resolve
            // para resources/views/errors/{status}.blade.php, o substituto direto.
            if (! $request->header('X-Inertia')) {
                return $response;
            }

            // Em modo debug, mantém a página detalhada para erros de servidor
            if ($status === 500 && config('app.debug') && ! $request->header('X-Inertia')) {
                return $response;
            }

            return \Inertia\Inertia::render('Error', ['status' => $status])
                ->toResponse($request)
                ->setStatusCode($status);
        });
    })->create();
