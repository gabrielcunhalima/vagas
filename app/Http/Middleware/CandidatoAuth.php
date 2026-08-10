<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatoAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('candidato')->check()) {
            return redirect()->route('candidato.login', ['redirect' => $request->path()])
                ->with('info', 'Faça login para continuar.');
        }

        return $next($request);
    }
}
