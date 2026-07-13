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
            return redirect()->route('candidato.verification.notice');
        }

        return $next($request);
    }
}
