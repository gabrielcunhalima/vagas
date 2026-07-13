<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPerfil
{
    public function handle(Request $request, Closure $next, string ...$perfis): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->ativo) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Usuário inativo.']);
        }

        if (!in_array($user->perfil, $perfis)) {
            abort(403, 'Acesso não autorizado para seu perfil.');
        }

        return $next($request);
    }
}
