<?php

namespace App\Http\Controllers\Candidato;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use App\Models\InscricaoComplemento;
use App\Support\Drhflow\DrhflowIndisponivelException;
use App\Support\Drhflow\InscricaoDrhflow;
use App\Support\Drhflow\InscricaoDrhflowRepository;
use App\Support\Drhflow\MapeadorInscricao;
use App\Support\Drhflow\VagaDrhflowRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

/**
 * "Minhas candidaturas" — o acompanhamento do candidato.
 *
 * A inscrição vive no DRHFlow e o andamento é derivado do que o RH registra lá.
 * O portal acrescenta o que guardou por conta própria (carta de apresentação,
 * versão de currículo enviada), correlato pelo par CPF + vaga.
 */
class MinhaCandidaturaController extends Controller
{
    public function __construct(
        private readonly InscricaoDrhflowRepository $inscricoes,
        private readonly VagaDrhflowRepository $vagas,
    ) {}

    private function candidato(): Candidato
    {
        return Auth::guard('candidato')->user();
    }

    public function index()
    {
        $candidato = $this->candidato();

        try {
            $inscricoes = $this->inscricoes->doCpf(MapeadorInscricao::cpf($candidato));
        } catch (DrhflowIndisponivelException) {
            return Inertia::render('Candidato/Candidaturas/Index', [
                'candidaturas' => [],
                'indisponivel' => true,
            ]);
        }

        return Inertia::render('Candidato/Candidaturas/Index', [
            'candidaturas' => $inscricoes
                ->map(fn (InscricaoDrhflow $i) => $this->comVaga($i)->toArray())
                ->values(),
            'indisponivel' => false,
        ]);
    }

    public function show(int $candidatura)
    {
        $candidato = $this->candidato();
        $cpf = MapeadorInscricao::cpf($candidato);

        try {
            // A busca é sempre pelo CPF do autenticado: não há como pedir a
            // inscrição de outra pessoa informando outro código de vaga.
            $inscricao = $this->inscricoes->buscar($cpf, $candidatura);
        } catch (DrhflowIndisponivelException) {
            abort(503, 'Não foi possível consultar sua candidatura agora. Tente novamente em alguns minutos.');
        }

        abort_if($inscricao === null, 404);

        $complemento = InscricaoComplemento::where('cpf', $cpf)
            ->where('cd_vaga_emprego', $candidatura)
            ->first();

        return Inertia::render('Candidato/Candidaturas/Show', [
            'candidatura' => array_merge($this->comVaga($inscricao)->toArray(), [
                // Dados próprios do portal — o DRHFlow não tem campo para eles.
                'carta_apresentacao' => $complemento?->carta_apresentacao,
                'conflito_interesse' => $complemento?->conflito_interesse,
                'conflito_interesse_detalhe' => $complemento?->conflito_interesse_detalhe,
                'curriculo_nome' => $complemento?->curriculoVigente?->nome_original
                    ?? $candidato->curriculoAtual?->nome_original,
                'tem_curriculo' => $complemento?->curriculo_id_vigente !== null || $candidato->temCurriculo(),
            ]),
        ]);
    }

    public function downloadCurriculo(int $candidatura)
    {
        $candidato = $this->candidato();

        $complemento = InscricaoComplemento::where('cpf', MapeadorInscricao::cpf($candidato))
            ->where('cd_vaga_emprego', $candidatura)
            ->first();

        // A versão enviada naquele momento, não a atual do perfil: é o PDF que o
        // processo recebeu.
        $versao = $complemento?->curriculoVigente ?? $candidato->curriculoAtual;

        abort_unless($versao !== null, 404, 'Currículo não encontrado.');
        abort_unless((int) $versao->candidato_id === (int) $candidato->id, 403);

        return Storage::disk(Candidato::DISCO_CURRICULOS)->download(
            $versao->path,
            $versao->nome_original ?? 'curriculo.pdf'
        );
    }

    /**
     * Anexa a vaga à inscrição.
     *
     * Uma vaga já encerrada some da consulta de disponibilidade, mas a inscrição
     * nela continua existindo — por isso a ausência da vaga não invalida a
     * inscrição, apenas deixa o cargo sem detalhe.
     */
    private function comVaga(InscricaoDrhflow $inscricao): InscricaoDrhflow
    {
        try {
            return $inscricao->comVaga($this->vagas->buscarPorCodigo($inscricao->cdVagaEmprego));
        } catch (DrhflowIndisponivelException) {
            return $inscricao;
        }
    }
}
