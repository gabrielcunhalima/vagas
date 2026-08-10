<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCandidatoEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $candidato = Auth::guard('candidato')->user();

        if (!$candidato || !$candidato->hasVerifiedEmail()) {
            // Guarda o destino para que verificar não custe reencontrar a vaga.
            if ($request->isMethod('GET')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }

            return redirect()->route('candidato.verification.notice')
                ->with('info', 'Confirme seu e-mail para continuar.');
        }

        return $next($request);
    }
}
