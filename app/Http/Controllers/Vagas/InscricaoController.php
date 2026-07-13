<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use App\Http\Requests\Vagas\InscricaoRequest;
use App\Mail\Vagas\CandidaturaRecebidaMail;
use App\Mail\Vagas\NovaCandidaturaMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InscricaoController extends Controller
{
    private function candidatoLogado()
    {
        return Auth::guard('candidato')->user();
    }

    public function create(Vaga $vaga)
    {
        abort_unless($vaga->esta_aberta, 404);

        $candidato = $this->candidatoLogado();

        // Se candidato logado já se candidatou, redireciona para suas candidaturas
        if ($candidato && $candidato->jaSeInscreveuNa($vaga->id)) {
            return redirect()->route('candidato.candidaturas.index')
                ->with('info', 'Você já se candidatou a esta vaga.');
        }

        // Pré-preenchimento: prioridade old() → perfil do candidato → vazio
        $prefill = $candidato ? $candidato->dadosParaCandidatura() : [];

        return view('vagas.publico.candidatura', compact('vaga', 'candidato', 'prefill'));
    }

    public function store(InscricaoRequest $request, Vaga $vaga)
    {
        abort_unless($vaga->esta_aberta, 404);

        $candidato = $this->candidatoLogado();

        // Impede dupla candidatura mesmo via POST direto
        if ($candidato && $candidato->jaSeInscreveuNa($vaga->id)) {
            return redirect()->route('candidato.candidaturas.index')
                ->with('info', 'Você já se candidatou a esta vaga.');
        }

        $dados = $request->validated();

        if ($request->hasFile('curriculo')) {
            $arquivo = $request->file('curriculo');
            $dados['curriculo_path']          = $arquivo->store('vagas/curriculos', 'local');
            $dados['curriculo_nome_original'] = $arquivo->getClientOriginalName();
        } elseif ($candidato && $candidato->temCurriculo()) {
            // Usa currículo do perfil se não foi enviado novo
            $dados['curriculo_path']          = $candidato->curriculo_path;
            $dados['curriculo_nome_original'] = $candidato->curriculo_nome_original;
        }

        unset($dados['curriculo'], $dados['_honeypot'], $dados['usar_curriculo_perfil']);

        $dados['vaga_id']      = $vaga->id;
        $dados['status']       = 'recebida';
        $dados['candidato_id'] = $candidato?->id;

        $candidatura = Candidatura::create($dados);

        // Atualiza perfil do candidato com dados mais recentes (optional: pode-se oferecer)
        if ($candidato) {
            $candidato->update(array_filter([
                'telefone'           => $dados['telefone'] ?? $candidato->telefone,
                'curso'              => $dados['curso'] ?? $candidato->curso,
                'instituicao'        => $dados['instituicao'] ?? $candidato->instituicao,
                'semestre'           => $dados['semestre'] ?? $candidato->semestre,
                'previsao_conclusao' => $dados['previsao_conclusao'] ?? $candidato->previsao_conclusao,
                'linkedin'           => $dados['linkedin'] ?? $candidato->linkedin,
                'pretensao_salarial' => $dados['pretensao_salarial'] ?? $candidato->pretensao_salarial,
                'disponibilidade'    => $dados['disponibilidade'] ?? $candidato->disponibilidade,
            ], fn($v) => $v !== null));
        }

        try {
            Mail::to($candidatura->email)->send(new CandidaturaRecebidaMail($candidatura));
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de candidatura: ' . $e->getMessage());
        }

        if ($vaga->notificar_email && $vaga->coordenador) {
            try {
                Mail::to($vaga->coordenador->email)->send(new NovaCandidaturaMail($candidatura));
            } catch (\Exception $e) {
                Log::error('Erro ao notificar coordenador: ' . $e->getMessage());
            }
        }

        if ($candidato) {
            return redirect()
                ->route('candidato.candidaturas.index')
                ->with('success', 'Candidatura enviada com sucesso! Acompanhe o status aqui.');
        }

        return redirect()
            ->route('inscricao.confirmacao', $vaga)
            ->with('candidatura_nome', $candidatura->nome);
    }

    public function confirmacao(Vaga $vaga)
    {
        return view('vagas.publico.confirmacao', compact('vaga'));
    }

    public function consultaForm()
    {
        return view('vagas.publico.consulta-candidatura');
    }

    public function consulta(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'cpf'   => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $cpf = preg_replace('/\D/', '', $request->cpf);

        $candidaturas = Candidatura::with('vaga')
            ->where('cpf', $cpf)
            ->where('email', $request->email)
            ->orderByDesc('created_at')
            ->get();

        return view('vagas.publico.consulta-candidatura', compact('candidaturas'));
    }
}
