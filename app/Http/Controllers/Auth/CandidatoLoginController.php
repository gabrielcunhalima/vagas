<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatoLoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if (Auth::guard('candidato')->check()) {
            return redirect()->route('candidato.vagas');
        }

        // Preserva URL de destino para redirecionar após login
        return view('candidato.auth.login', [
            'redirect' => $request->query('redirect'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Informe seu e-mail.',
            'email.email'       => 'E-mail inválido.',
            'password.required' => 'Informe sua senha.',
        ]);

        $credenciais = $request->only('email', 'password');
        $lembrar     = $request->boolean('remember');

        if (!Auth::guard('candidato')->attempt($credenciais, $lembrar)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'E-mail ou senha incorretos.']);
        }

        $candidato = Auth::guard('candidato')->user();

        if (!$candidato->ativo) {
            Auth::guard('candidato')->logout();
            return back()->withErrors(['email' => 'Conta inativa. Entre em contato com o suporte.']);
        }

        $request->session()->regenerate();

        // Sinal de vida da conta, e o que cancela um aviso de inatividade em curso.
        $candidato->registrarAtividade();

        $redirect = $request->input('redirect');
        if ($redirect && str_starts_with($redirect, '/')) {
            return redirect($redirect);
        }

        return redirect()->route('candidato.vagas');
    }

    public function logout(Request $request)
    {
        Auth::guard('candidato')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Você saiu da sua conta.');
    }
}
