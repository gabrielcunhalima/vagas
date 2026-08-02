<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByPerfil();
        }

        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Informe o e-mail.',
            'email.email'       => 'E-mail inválido.',
            'password.required' => 'Informe a senha.',
        ]);

        $credenciais = $request->only('email', 'password');
        $lembrar     = $request->boolean('remember');

        if (!Auth::attempt($credenciais, $lembrar)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'E-mail ou senha incorretos.']);
        }

        $user = Auth::user();

        if (!$user->ativo) {
            Auth::logout();
            return back()->withErrors(['email' => 'Usuário inativo. Contate o administrador.']);
        }

        $request->session()->regenerate();

        return $this->redirectByPerfil();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByPerfil()
    {
        return match (Auth::user()->perfil) {
            'gestor' => redirect()->route('gestor.dashboard'),
            default  => redirect()->route('coord.dashboard'),
        };
    }
}
