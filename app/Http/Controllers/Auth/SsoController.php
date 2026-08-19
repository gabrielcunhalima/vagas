<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    public function entrar(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            abort(401, 'Token ausente.');
        }

        $secret = config('jwt.secret');

        if (empty($secret)) {
            abort(500, 'JWT_SECRET não configurado neste sistema.');
        }

        try {
            $payload = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Throwable) {
            abort(401, 'Token inválido ou expirado. Volte ao portal e tente novamente.');
        }

        $cpf = $payload->cpf ?? null;
        $email = $payload->email ?? null;

        $user = null;

        // Tenta por CPF primeiro (quando ambos os lados têm CPF preenchido)
        if ($cpf) {
            $cpfLimpo = preg_replace('/\D/', '', $cpf);
            $user = User::where(function ($q) use ($cpf, $cpfLimpo) {
                $q->where('cpf', $cpf)->orWhere('cpf', $cpfLimpo);
            })->where('ativo', true)->first();
        }

        // Fallback por e-mail (quando CPF ainda não está sincronizado)
        if (! $user && $email) {
            $user = User::where('email', $email)->where('ativo', true)->first();
        }

        if (! $user) {
            abort(403, 'Usuário não encontrado neste sistema. Verifique se o e-mail ou CPF está cadastrado no Portal de Vagas.');
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return match ($user->perfil) {
            'gestor' => redirect()->route('gestor.dashboard'),
            default => redirect()->route('coord.dashboard'),
        };
    }
}
