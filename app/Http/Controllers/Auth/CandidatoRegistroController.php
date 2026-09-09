<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CandidatoRegistroRequest;
use App\Http\Requests\Concerns\ValidaCpf;
use App\Models\Candidato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CandidatoRegistroController extends Controller
{
    use ValidaCpf;

    public function showForm(Request $request)
    {
        if (Auth::guard('candidato')->check()) {
            return redirect()->route('candidato.vagas');
        }

        return view('candidato.auth.registro', [
            'redirect' => $request->query('redirect'),
        ]);
    }

    public function verificarCpf(Request $request): JsonResponse
    {
        $cpf = preg_replace('/\D/', '', (string) $request->query('cpf'));

        // CPF malformado não vai ao banco: mesma regra de dígitos verificadores do envio.
        if (! $this->validarCpf($cpf)) {
            return response()->json(['existe' => false, 'valido' => false]);
        }

        return response()->json([
            'existe' => Candidato::where('cpf', $cpf)->exists(),
            'valido' => true,
        ]);
    }

    public function store(CandidatoRegistroRequest $request)
    {
        $dados = $request->validated();

        $candidato = Candidato::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'cpf' => $dados['cpf'],
            'password' => Hash::make($dados['password']),
            'lgpd_consentimento' => true,
            'lgpd_consentimento_em' => now(),
            'ativo' => true,
        ]);

        Auth::guard('candidato')->login($candidato, false);
        $request->session()->regenerate();

        $candidato->sendEmailVerificationNotification();

        return redirect()->route('candidato.verification.notice')
            ->with('success', 'Conta criada com sucesso! Confirme seu e-mail para acessar.');
    }
}
