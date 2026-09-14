<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatoVerificacaoController extends Controller
{
    public function notice()
    {
        $candidato = Auth::guard('candidato')->user();

        if ($candidato->hasVerifiedEmail()) {
            return redirect()->route('candidato.vagas');
        }

        return view('candidato.auth.verificar-email');
    }

    /**
     * Confirma a conta do link, não a da sessão.
     *
     * A URL assinada carrega o id e o hash do e-mail: é ela que diz qual conta
     * está sendo confirmada. A sessão só decide para onde a pessoa vai depois —
     * se estiver logada nessa mesma conta, segue; se estiver logada em outra, sai
     * dela, para não continuar navegando com a conta errada; sem sessão, entra.
     */
    public function verify(Request $request, string $id, string $hash)
    {
        $candidato = Candidato::find($id);

        if (! $candidato || ! hash_equals($hash, sha1($candidato->getEmailForVerification()))) {
            abort(403);
        }

        if (! $candidato->hasVerifiedEmail()) {
            $candidato->markEmailAsVerified();
            event(new Verified($candidato));
        }

        $logado = Auth::guard('candidato')->user();

        if ($logado?->is($candidato)) {
            // Retoma o que o candidato tentava fazer antes de ser barrado pela verificação.
            return redirect()->intended(route('candidato.vagas'))
                ->with('success', 'E-mail confirmado com sucesso!');
        }

        if ($logado) {
            Auth::guard('candidato')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('candidato.login')
            ->with('success', 'E-mail confirmado! Entre com sua conta para continuar.');
    }

    public function resend(Request $request)
    {
        $candidato = Auth::guard('candidato')->user();

        if ($candidato->hasVerifiedEmail()) {
            return redirect()->route('candidato.vagas');
        }

        $candidato->enviarVerificacaoDeEmailAposResposta();

        return back()->with('success', 'Um novo e-mail de confirmação foi enviado.');
    }
}
