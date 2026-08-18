<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vagas\InscricaoRequest;
use App\Mail\Vagas\CandidaturaRecebidaMail;
use App\Models\Candidato;
use App\Models\InscricaoComplemento;
use App\Support\Drhflow\DrhflowIndisponivelException;
use App\Support\Drhflow\InscricaoDrhflowRepository;
use App\Support\Drhflow\MapeadorInscricao;
use App\Support\Drhflow\VagaDrhflow;
use App\Support\Drhflow\VagaDrhflowRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

/**
 * O envio da inscrição.
 *
 * A inscrição é gravada em `EN_CANDIDATO_VAGA_EMPREGO`, no DRHFlow — é ela que
 * entra no processo seletivo do RH. O que o DRHFlow não comporta (carta de
 * apresentação, detalhe do conflito de interesse, versão de currículo vigente)
 * fica em `inscricao_complementos`, no banco do portal.
 *
 * A ordem das duas escritas é deliberada: primeiro o DRHFlow, depois o
 * complemento. São bancos diferentes, sem transação distribuída, então uma das
 * duas pode falhar sozinha — e a que não pode ser perdida é a que o RH lê.
 */
class InscricaoController extends Controller
{
    public function __construct(
        private readonly VagaDrhflowRepository $vagas,
        private readonly InscricaoDrhflowRepository $inscricoes,
    ) {}

    private function candidatoLogado(): Candidato
    {
        return Auth::guard('candidato')->user();
    }

    /**
     * A vaga, ou 404/503.
     *
     * O gate de vaga aberta acontece aqui, antes de qualquer outra coisa: uma
     * vaga fechada ou fora do prazo não aceita inscrição.
     */
    private function vagaAberta(int $codigo): VagaDrhflow
    {
        try {
            $vaga = $this->vagas->buscarPorCodigo($codigo);
        } catch (DrhflowIndisponivelException) {
            abort(503, 'As vagas estão temporariamente indisponíveis. Tente novamente em alguns minutos.');
        }

        abort_if($vaga === null, 404);

        return $vaga;
    }

    public function create(int $vaga)
    {
        $vagaDrhflow = $this->vagaAberta($vaga);
        $candidato = $this->candidatoLogado();

        try {
            if ($candidato->jaSeInscreveuNa($vagaDrhflow->codigo)) {
                return redirect()->route('candidato.candidaturas.index')
                    ->with('info', 'Você já se candidatou a esta vaga.');
            }
        } catch (DrhflowIndisponivelException) {
            return redirect()->route('vagas.publicas.show', $vagaDrhflow->codigo)
                ->with('error', 'Não foi possível verificar sua inscrição agora. Tente novamente em alguns minutos.');
        }

        return Inertia::render('Publico/Candidatura', [
            'vaga' => $vagaDrhflow->toArray(),
            // Os dados vão para conferência, não para preenchimento: o que estiver
            // aqui é o que o RH verá, e editar grava na conta.
            'perfil' => $this->perfilParaConferencia($candidato),
            'completude' => $candidato->estadoCompletude(),
        ]);
    }

    public function store(InscricaoRequest $request, int $vaga)
    {
        $vagaDrhflow = $this->vagaAberta($vaga);
        $candidato = $this->candidatoLogado();

        // O perfil é a fonte dos dados que o RH lê; incompleto, não há ficha para
        // avaliar. A interface já bloqueia, isto é a garantia final.
        if (! $candidato->perfilCompleto()) {
            return redirect()->route('candidato.perfil.edit')
                ->with('info', 'Complete seu perfil para se candidatar a esta vaga.');
        }

        $dados = $request->validated();

        try {
            $criou = $this->inscricoes->criar($candidato, $vagaDrhflow->codigo, aceitouCodigoConduta: true);
        } catch (DrhflowIndisponivelException $e) {
            Log::error('Inscrição não gravada no DRHFlow.', [
                'cd_vaga_emprego' => $vagaDrhflow->codigo,
                'erro' => $e->getMessage(),
            ]);

            // A inscrição não é apresentada como recebida: ela não foi.
            return back()
                ->withInput()
                ->with('error', 'Não conseguimos enviar sua inscrição agora. Nada foi perdido — tente novamente em alguns minutos.');
        }

        if (! $criou) {
            return redirect()->route('candidato.candidaturas.index')
                ->with('info', 'Você já se candidatou a esta vaga.');
        }

        $this->guardarComplemento($candidato, $vagaDrhflow->codigo, $dados, $request->boolean('conflito_interesse'));

        try {
            Mail::to($candidato->email)->send(new CandidaturaRecebidaMail($vagaDrhflow, $candidato));
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de candidatura: '.$e->getMessage());
        }

        return redirect()
            ->route('candidato.candidaturas.index')
            ->with('success', 'Candidatura enviada com sucesso! Acompanhe o andamento aqui.');
    }

    public function confirmacao(int $vaga)
    {
        return Inertia::render('Publico/Confirmacao', [
            'vaga' => $this->vagaAberta($vaga)->toArray(),
            'nome' => $this->candidatoLogado()->nome,
        ]);
    }

    /**
     * A parte local da inscrição.
     *
     * A inscrição já está gravada no DRHFlow quando isto roda. Se falhar, a
     * inscrição continua valendo — perder a carta de apresentação é ruim, perder
     * a inscrição é pior. A falha é registrada com o par CPF + vaga, que é o
     * suficiente para reconciliar depois.
     *
     * @param  array<string, mixed>  $dados
     */
    private function guardarComplemento(Candidato $candidato, int $cdVagaEmprego, array $dados, bool $conflito): void
    {
        try {
            InscricaoComplemento::updateOrCreate(
                [
                    'cpf' => MapeadorInscricao::cpf($candidato),
                    'cd_vaga_emprego' => $cdVagaEmprego,
                ],
                [
                    'candidato_id' => $candidato->id,
                    'carta_apresentacao' => $dados['carta_apresentacao'] ?? null,
                    'conflito_interesse' => $conflito,
                    'conflito_interesse_detalhe' => $dados['conflito_interesse_detalhe'] ?? null,
                    // Qual PDF o processo recebeu. Trocar o currículo depois não
                    // muda esta referência.
                    'curriculo_id_vigente' => $candidato->curriculo_atual_id,
                    'enviada_em' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::error('Inscrição gravada no DRHFlow, mas o complemento local falhou.', [
                'candidato_id' => $candidato->id,
                'cd_vaga_emprego' => $cdVagaEmprego,
                'erro' => $e->getMessage(),
            ]);
        }
    }

    /** O que o RH verá — apresentado ao candidato antes do envio. */
    private function perfilParaConferencia(Candidato $candidato): array
    {
        return [
            'nome' => $candidato->nome,
            'nome_social' => $candidato->nome_social,
            'email' => $candidato->email,
            'cpf_formatado' => $candidato->cpf_formatado,
            'telefone' => $candidato->telefone,
            'nacionalidade' => $candidato->nacionalidade,
            'linkedin' => $candidato->linkedin,
            'formacoes' => $candidato->formacoes->map(fn ($f) => [
                'nivel_escolaridade' => $f->nivel_escolaridade,
                'situacao_curso' => $f->situacao_curso,
                'curso' => $f->curso,
                'instituicao' => $f->instituicao,
                'semestre' => $f->semestre,
                'previsao_conclusao' => $f->previsao_conclusao?->format('Y-m-d'),
            ])->values(),
            'outras_formacoes_mec' => $candidato->outras_formacoes_mec,
            'outros_cursos' => $candidato->outros_cursos,
            'cidade' => $candidato->cidade,
            'estado' => $candidato->estado,
            'pretensao_salarial' => $candidato->pretensao_salarial,
            'disponibilidade' => $candidato->disponibilidade,
            'possui_acessibilidade' => $candidato->possui_acessibilidade,
            'curriculo_nome' => $candidato->curriculoAtual?->nome_original,
        ];
    }
}
