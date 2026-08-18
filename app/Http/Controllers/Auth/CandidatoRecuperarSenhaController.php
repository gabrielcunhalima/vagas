<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class CandidatoRecuperarSenhaController extends Controller
{
    /** Mensagem genérica: nunca revela se o e-mail está ou não cadastrado. */
    private const MENSAGEM_GENERICA = 'Se este e-mail estiver cadastrado, enviaremos um link de recuperação em instantes.';

    public function showLinkRequestForm()
    {
        if (Auth::guard('candidato')->check()) {
            return redirect()->route('candidato.vagas');
        }

        return view('candidato.auth.esqueci-senha');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Informe seu e-mail.',
            'email.email'    => 'E-mail inválido.',
        ]);

        // Candidatos inativos não recebem o link: mesma regra de negócio do login,
        // mas resposta genérica para não revelar o estado da conta a um visitante.
        $candidato = Candidato::where('email', $request->input('email'))->first();
        if ($candidato && !$candidato->ativo) {
            return back()->with('success', self::MENSAGEM_GENERICA);
        }

        try {
            $status = Password::broker('candidatos')->sendResetLink(
                $request->only('email')
            );
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            Log::error('Falha no transporte de e-mail ao enviar recuperação de senha do candidato.', [
                'email' => $request->input('email'),
                'erro'  => $e->getMessage(),
            ]);

            return back()->with('success', self::MENSAGEM_GENERICA);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->with('success', 'Um link já foi enviado recentemente. Verifique sua caixa de entrada ou aguarde alguns instantes antes de tentar novamente.');
        }

        return back()->with('success', self::MENSAGEM_GENERICA);
    }

    public function showResetForm(Request $request, string $token)
    {
        if (Auth::guard('candidato')->check()) {
            return redirect()->route('candidato.vagas');
        }

        return view('candidato.auth.redefinir-senha', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'token.required'      => 'Link de redefinição inválido.',
            'email.required'      => 'Informe seu e-mail.',
            'email.email'         => 'E-mail inválido.',
            'password.required'   => 'Informe a nova senha.',
            'password.confirmed'  => 'As senhas não coincidem.',
            'password.min'        => 'A senha deve ter no mínimo 8 caracteres.',
        ]);

        // Candidatos inativos não podem redefinir senha: mesma regra do login,
        // aplicada antes do broker para não trocar a senha de uma conta bloqueada.
        $candidatoAlvo = Candidato::where('email', $request->input('email'))->first();
        if ($candidatoAlvo && !$candidatoAlvo->ativo) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Este link de redefinição é inválido ou já expirou. Solicite um novo.']);
        }

        $status = Password::broker('candidatos')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($candidato, $password) {
                $candidato->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $candidato->save();

                event(new PasswordReset($candidato));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Este link de redefinição é inválido ou já expirou. Solicite um novo.']);
        }

        return redirect()->route('candidato.login')
            ->with('success', 'Senha redefinida com sucesso! Entre com sua nova senha.');
    }
}
