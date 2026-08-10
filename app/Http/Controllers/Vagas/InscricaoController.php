<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use App\Models\Vagas\CandidaturaEvento;
use App\Http\Requests\Vagas\InscricaoRequest;
use App\Mail\Vagas\CandidaturaRecebidaMail;
use App\Mail\Vagas\NovaCandidaturaMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class InscricaoController extends Controller
{
    private function candidatoLogado(): Candidato
    {
        return Auth::guard('candidato')->user();
    }

    public function create(Vaga $vaga)
    {
        abort_unless($vaga->esta_aberta, 404);

        $candidato = $this->candidatoLogado();

        if ($candidato->jaSeInscreveuNa($vaga->id)) {
            return redirect()->route('candidato.candidaturas.index')
                ->with('info', 'Você já se candidatou a esta vaga.');
        }

        return Inertia::render('Publico/Candidatura', [
            'vaga' => $vaga->only([
                'id', 'titulo', 'tipo', 'area', 'modalidade', 'carga_horaria',
                'remuneracao', 'remuneracao_max', 'cidade', 'estado',
                'local_trabalho', 'data_encerramento',
            ]),
            // Os dados vão para conferência, não para preenchimento: o que estiver
            // aqui é o que o coordenador verá, e editar grava na conta.
            'perfil'     => $this->perfilParaConferencia($candidato),
            'completude' => $candidato->estadoCompletude(),
        ]);
    }

    public function store(InscricaoRequest $request, Vaga $vaga)
    {
        abort_unless($vaga->esta_aberta, 404);

        $candidato = $this->candidatoLogado();

        if ($candidato->jaSeInscreveuNa($vaga->id)) {
            return redirect()->route('candidato.candidaturas.index')
                ->with('info', 'Você já se candidatou a esta vaga.');
        }

        // O perfil é a fonte dos dados que o coordenador lê; incompleto, não há
        // ficha para avaliar. A interface já bloqueia, isto é a garantia final.
        if (!$candidato->perfilCompleto()) {
            return redirect()->route('candidato.perfil.edit')
                ->with('info', 'Complete seu perfil para se candidatar a esta vaga.');
        }

        $dados = $request->validated();

        $candidatura = Candidatura::create([
            'vaga_id'                    => $vaga->id,
            'candidato_id'               => $candidato->id,
            'carta_apresentacao'         => $dados['carta_apresentacao'] ?? null,
            'conflito_interesse'         => $request->boolean('conflito_interesse'),
            'conflito_interesse_detalhe' => $dados['conflito_interesse_detalhe'] ?? null,
            'codigo_conduta_aceito_em'   => now(),
            'status'                     => 'recebida',
        ]);

        // Registra a submissão e qual currículo estava vigente nela. É o que
        // permite, depois, saber qual PDF o processo recebeu.
        $candidatura->eventos()->create([
            'tipo'                 => CandidaturaEvento::TIPO_SUBMISSAO,
            'status_novo'          => 'recebida',
            'curriculo_id_vigente' => $candidato->curriculo_atual_id,
            'ocorrido_em'          => now(),
        ]);

        try {
            Mail::to($candidato->email)->send(new CandidaturaRecebidaMail($candidatura));
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

        return redirect()
            ->route('candidato.candidaturas.index')
            ->with('success', 'Candidatura enviada com sucesso! Acompanhe o status aqui.');
    }

    public function confirmacao(Vaga $vaga)
    {
        return Inertia::render('Publico/Confirmacao', [
            'vaga' => $vaga->only(['id', 'titulo']),
            'nome' => $this->candidatoLogado()->nome,
        ]);
    }

    /** O que o coordenador verá — apresentado ao candidato antes do envio. */
    private function perfilParaConferencia(Candidato $candidato): array
    {
        return [
            'nome'                  => $candidato->nome,
            'nome_social'           => $candidato->nome_social,
            'email'                 => $candidato->email,
            'cpf_formatado'         => $candidato->cpf_formatado,
            'telefone'              => $candidato->telefone,
            'nacionalidade'         => $candidato->nacionalidade,
            'linkedin'              => $candidato->linkedin,
            'formacoes'             => $candidato->formacoes->map(fn ($f) => [
                'nivel_escolaridade' => $f->nivel_escolaridade,
                'situacao_curso'     => $f->situacao_curso,
                'curso'              => $f->curso,
                'instituicao'        => $f->instituicao,
                'semestre'           => $f->semestre,
                'previsao_conclusao' => $f->previsao_conclusao?->format('Y-m-d'),
            ])->values(),
            'outras_formacoes_mec'  => $candidato->outras_formacoes_mec,
            'outros_cursos'         => $candidato->outros_cursos,
            'cidade'                => $candidato->cidade,
            'estado'                => $candidato->estado,
            'pretensao_salarial'    => $candidato->pretensao_salarial,
            'disponibilidade'       => $candidato->disponibilidade,
            'possui_acessibilidade' => $candidato->possui_acessibilidade,
            'curriculo_nome'        => $candidato->curriculoAtual?->nome_original,
        ];
    }
}
