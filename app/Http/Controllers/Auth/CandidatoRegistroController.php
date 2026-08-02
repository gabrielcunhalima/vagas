<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CandidatoRegistroRequest;
use App\Models\Candidato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class CandidatoRegistroController extends Controller
{
    public function showForm(Request $request)
    {
        if (Auth::guard('candidato')->check()) {
            return redirect()->route('candidato.vagas');
        }

        return Inertia::render('Candidato/Auth/Registro', [
            'redirect' => $request->query('redirect'),
        ]);
    }

    public function verificarCpf(Request $request): JsonResponse
    {
        $cpf = preg_replace('/\D/', '', (string) $request->query('cpf'));

        $existe = strlen($cpf) === 11 && Candidato::where('cpf', $cpf)->exists();

        return response()->json(['existe' => $existe]);
    }

    public function store(CandidatoRegistroRequest $request)
    {
        $dados = $request->validated();

        $curriculoPath = null;
        $curriculoNomeOriginal = null;
        if ($request->hasFile('curriculo')) {
            $arquivo = $request->file('curriculo');
            $curriculoPath = $arquivo->store('candidatos/curriculos', 'local');
            $curriculoNomeOriginal = $arquivo->getClientOriginalName();
        }

        $candidato = Candidato::create([
            'nome'                       => $dados['nome'],
            'nome_social'                => $dados['nome_social'] ?? null,
            'nacionalidade'              => $dados['nacionalidade'],
            'email'                      => $dados['email'],
            'cpf'                        => $dados['cpf'],
            'telefone'                   => $dados['telefone'] ?? null,
            'password'                   => Hash::make($dados['password']),
            'cep'                        => $dados['cep'] ?? null,
            'estado'                     => $dados['estado'] ?? null,
            'cidade'                     => $dados['cidade'] ?? null,
            'bairro'                     => $dados['bairro'] ?? null,
            'logradouro'                 => $dados['logradouro'] ?? null,
            'numero'                     => $dados['numero'] ?? null,
            'complemento'                => $dados['complemento'] ?? null,
            'nivel_escolaridade'         => $dados['nivel_escolaridade'],
            'situacao_curso'             => $dados['situacao_curso'],
            'curso'                      => $dados['curso'],
            'instituicao'                => $dados['instituicao'],
            'semestre'                   => $dados['semestre'] ?? null,
            'previsao_conclusao'         => $dados['previsao_conclusao'],
            'curriculo_path'             => $curriculoPath,
            'curriculo_nome_original'    => $curriculoNomeOriginal,
            'possui_acessibilidade'      => $request->boolean('possui_acessibilidade'),
            'acessibilidade_detalhe'     => $dados['acessibilidade_detalhe'] ?? null,
            'conflito_interesse'         => $request->boolean('conflito_interesse'),
            'conflito_interesse_detalhe' => $dados['conflito_interesse_detalhe'] ?? null,
            'codigo_conduta_aceito_em'   => now(),
            'lgpd_consentimento'         => true,
            'lgpd_consentimento_em'      => now(),
            'ativo'                      => true,
        ]);

        Auth::guard('candidato')->login($candidato, false);
        $request->session()->regenerate();

        $candidato->sendEmailVerificationNotification();

        return redirect()->route('candidato.verification.notice')
            ->with('success', 'Conta criada com sucesso! Confirme seu e-mail para acessar.');
    }
}
