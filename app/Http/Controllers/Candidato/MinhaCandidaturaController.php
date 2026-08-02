<?php

namespace App\Http\Controllers\Candidato;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Candidatura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MinhaCandidaturaController extends Controller
{
    private function candidato()
    {
        return Auth::guard('candidato')->user();
    }

    public function index()
    {
        $candidaturas = $this->candidato()
            ->candidaturas()
            ->with('vaga')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Candidato/Candidaturas/Index', [
            'candidaturas' => $candidaturas->map(fn(Candidatura $c) => [
                'id'         => $c->id,
                'status'     => $c->status,
                'created_at' => $c->created_at,
                'vaga'       => $c->vaga?->only(['id', 'titulo', 'tipo', 'area', 'modalidade', 'cidade', 'estado']),
            ]),
        ]);
    }

    public function show(Candidatura $candidatura)
    {
        abort_unless(
            $candidatura->candidato_id === $this->candidato()->id,
            403
        );

        $candidatura->load('vaga');

        // Visão do candidato: sem observações internas do coordenador
        return Inertia::render('Candidato/Candidaturas/Show', [
            'candidatura' => [
                'id'                     => $candidatura->id,
                'status'                 => $candidatura->status,
                'created_at'             => $candidatura->created_at,
                'nome'                   => $candidatura->nome,
                'email'                  => $candidatura->email,
                'cpf_formatado'          => $candidatura->cpf_formatado,
                'telefone'               => $candidatura->telefone,
                'curso'                  => $candidatura->curso,
                'instituicao'            => $candidatura->instituicao,
                'semestre'               => $candidatura->semestre,
                'previsao_conclusao'     => $candidatura->previsao_conclusao,
                'carta_apresentacao'     => $candidatura->carta_apresentacao,
                'linkedin'               => $candidatura->linkedin,
                'pretensao_salarial'     => $candidatura->pretensao_salarial,
                'disponibilidade'        => $candidatura->disponibilidade,
                'pcd'                    => $candidatura->pcd,
                'pcd_tipo'               => $candidatura->pcd_tipo,
                'endereco_completo'      => $candidatura->endereco_completo,
                'curriculo_nome'         => $candidatura->curriculo_nome_original,
                'tem_curriculo'          => $candidatura->temCurriculo(),
                'entrevista_data'        => $candidatura->entrevista_data,
                'entrevista_local'       => $candidatura->entrevista_local,
                'entrevista_observacoes' => $candidatura->entrevista_observacoes,
                'vaga'                   => $candidatura->vaga?->only([
                    'id', 'titulo', 'tipo', 'area', 'modalidade', 'cidade', 'estado', 'status', 'data_encerramento',
                ]),
            ],
        ]);
    }

    public function downloadCurriculo(Candidatura $candidatura)
    {
        abort_unless($candidatura->candidato_id === $this->candidato()->id, 403);
        abort_unless($candidatura->temCurriculo(), 404, 'Currículo não encontrado.');

        return Storage::disk('local')->download(
            $candidatura->curriculo_path,
            $candidatura->curriculo_nome_original ?? 'curriculo.pdf'
        );
    }
}
