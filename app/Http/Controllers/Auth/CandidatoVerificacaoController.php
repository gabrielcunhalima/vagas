<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CandidatoVerificacaoController extends Controller
{
    public function notice()
    {
        $candidato = Auth::guard('candidato')->user();

        if ($candidato->hasVerifiedEmail()) {
            return redirect()->route('candidato.vagas');
        }

        return Inertia::render('Candidato/Auth/VerificarEmail');
    }

    public function verify(Request $request)
    {
        $candidato = Auth::guard('candidato')->user();

        if ((string) $candidato->getKey() !== (string) $request->route('id')
            || !hash_equals((string) $request->route('hash'), sha1($candidato->getEmailForVerification()))
        ) {
            abort(403);
        }

        if (!$candidato->hasVerifiedEmail()) {
            $candidato->markEmailAsVerified();
            event(new Verified($candidato));
        }

        return redirect()->route('candidato.vagas')->with('success', 'E-mail confirmado com sucesso!');
    }

    public function resend(Request $request)
    {
        $candidato = Auth::guard('candidato')->user();

        if ($candidato->hasVerifiedEmail()) {
            return redirect()->route('candidato.vagas');
        }

        $candidato->sendEmailVerificationNotification();

        return back()->with('success', 'Um novo e-mail de confirmação foi enviado.');
    }
}
