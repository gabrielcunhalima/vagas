<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use App\Models\Vagas\CandidaturaEvento;
use App\Policies\CandidaturaPolicy;
use App\Mail\Vagas\ConviteEntrevistaMail;
use App\Mail\Vagas\AprovacaoMail;
use App\Mail\Vagas\ReprovacaoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CandidaturaController extends Controller
{
    /**
     * Resumo de candidatura para listagens internas.
     *
     * Os dados pessoais vêm do perfil vivo e só aparecem enquanto o acesso estiver
     * vigente — do contrário a listagem seria uma porta lateral para contornar o
     * decaimento imposto na tela de detalhe.
     */
    private function candidaturaResumo(Candidatura $c): array
    {
        $podeVerPessoais = Auth::user()->can('verDadosPessoais', $c);

        return [
            'id'         => $c->id,
            'vaga_id'    => $c->vaga_id,
            'nome'       => $podeVerPessoais ? $c->nome : null,
            'email'      => $podeVerPessoais ? $c->email : null,
            'cursos'     => $podeVerPessoais ? $c->formacoes->pluck('curso')->filter()->implode(', ') : null,
            'pcd'        => $podeVerPessoais ? $c->pcd : null,
            'acesso_expirado' => !$podeVerPessoais,
            'status'     => $c->status,
            'created_at' => $c->created_at,
            'vaga'       => $c->vaga?->only(['id', 'titulo']),
        ];
    }

    public function todas(Request $request)
    {
        $user = Auth::user();

        $base = fn() => Candidatura::when(
            !$user->isAdmin(),
            fn($q) => $q->whereHas('vaga', fn($q2) => $q2->where('coordenador_id', $user->id))
        );

        // `candidato` é obrigatório aqui: os dados pessoais vêm dele por delegação.
        $query = $base()->with(['vaga', 'candidato.formacoes'])->latest();

        if ($request->filled('status')) {
            $query->porStatus($request->status);
        }
        if ($request->filled('vaga_id')) {
            $query->where('vaga_id', $request->vaga_id);
        }
        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }

        $candidaturas = $query->paginate(25)->withQueryString()
            ->through(fn(Candidatura $c) => $this->candidaturaResumo($c));

        $vagas = $user->isAdmin()
            ? Vaga::orderBy('titulo')->get(['id', 'titulo'])
            : Vaga::where('coordenador_id', $user->id)->orderBy('titulo')->get(['id', 'titulo']);

        $contadores = [
            'todos'      => $base()->count(),
            'recebida'   => $base()->porStatus('recebida')->count(),
            'em_analise' => $base()->porStatus('em_analise')->count(),
            'entrevista' => $base()->porStatus('entrevista')->count(),
            'aprovado'   => $base()->porStatus('aprovado')->count(),
            'reprovado'  => $base()->porStatus('reprovado')->count(),
        ];

        return Inertia::render('Coord/Candidaturas/Todas', [
            'candidaturas' => $candidaturas,
            'vagas'        => $vagas,
            'contadores'   => $contadores,
            'filtros'      => $request->only(['status', 'vaga_id', 'busca']),
        ]);
    }

    public function index(Request $request, Vaga $vaga)
    {
        $this->autorizarVaga($vaga);

        $query = $vaga->candidaturas()->with(['vaga', 'candidato.formacoes'])->latest();

        if ($request->filled('status')) {
            $query->porStatus($request->status);
        }

        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }

        $candidaturas = $query->paginate(20)->withQueryString()
            ->through(fn(Candidatura $c) => $this->candidaturaResumo($c));

        $contadores = [
            'todos'      => $vaga->candidaturas()->count(),
            'recebida'   => $vaga->candidaturas()->porStatus('recebida')->count(),
            'em_analise' => $vaga->candidaturas()->porStatus('em_analise')->count(),
            'entrevista' => $vaga->candidaturas()->porStatus('entrevista')->count(),
            'aprovado'   => $vaga->candidaturas()->porStatus('aprovado')->count(),
            'reprovado'  => $vaga->candidaturas()->porStatus('reprovado')->count(),
        ];

        return Inertia::render('Coord/Candidaturas/Index', [
            'vaga'         => $vaga->only(['id', 'titulo', 'status', 'data_encerramento']),
            'candidaturas' => $candidaturas,
            'contadores'   => $contadores,
            'filtros'      => $request->only(['status', 'busca']),
        ]);
    }

    public function show(Vaga $vaga, Candidatura $candidatura)
    {
        $this->autorizarVaga($vaga);
        abort_unless($candidatura->vaga_id === $vaga->id, 404);

        $candidatura->load(['candidato', 'eventos.autor', 'eventos.curriculoVigente']);

        // O registro do processo permanece sempre acessível; os dados pessoais
        // dependem do acesso ainda estar vigente.
        $registroProcesso = array_merge($candidatura->only([
            'id', 'carta_apresentacao', 'conflito_interesse', 'conflito_interesse_detalhe',
            'codigo_conduta_aceito_em', 'status', 'entrevista_data', 'entrevista_local',
            'entrevista_observacoes', 'observacoes_internas', 'created_at',
        ]), [
            'eventos' => $candidatura->eventos->map(fn ($ev) => [
                'descricao'   => $ev->descricao,
                'autor'       => $ev->autor?->name,
                'ocorrido_em' => $ev->ocorrido_em,
                'curriculo'   => $ev->curriculoVigente?->nome_original,
            ]),
        ]);

        $podeVerPessoais = Auth::user()->can('verDadosPessoais', $candidatura);

        return Inertia::render('Coord/Candidaturas/Show', [
            'vaga' => $vaga->only(['id', 'titulo', 'status']),
            'candidatura' => $podeVerPessoais
                ? array_merge($registroProcesso, $candidatura->only([
                    'nome', 'email', 'telefone', 'linkedin', 'pretensao_salarial',
                    'disponibilidade', 'pcd', 'pcd_tipo',
                ]), [
                    'cpf_formatado'     => $candidatura->cpf_formatado,
                    'endereco_completo' => $candidatura->endereco_completo,
                    'formacoes'         => $candidatura->formacoes->map(fn ($f) => [
                        'nivel_escolaridade' => $f->nivel_escolaridade,
                        'situacao_curso'     => $f->situacao_curso,
                        'curso'              => $f->curso,
                        'instituicao'        => $f->instituicao,
                        'semestre'           => $f->semestre,
                        'previsao_conclusao' => $f->previsao_conclusao?->format('Y-m-d'),
                    ])->values(),
                    'outras_formacoes_mec'    => $candidatura->candidato?->outras_formacoes_mec,
                    'outros_cursos'           => $candidatura->candidato?->outros_cursos,
                    'tem_curriculo'     => $candidatura->temCurriculo(),
                    'curriculo_nome_original' => $candidatura->curriculo_nome_original,
                    // Deixa claro que a ficha é viva, não um retrato da inscrição.
                    'perfil_atualizado_em'    => $candidatura->candidato?->updated_at,
                ])
                : $registroProcesso,
            'acessoExpirado' => !$podeVerPessoais,
            'motivoExpiracao' => $podeVerPessoais ? null : $this->motivoExpiracao($candidatura),
            'proximosStatus' => Candidatura::$proximosStatus[$candidatura->status] ?? [],
        ]);
    }

    private function motivoExpiracao(Candidatura $candidatura): string
    {
        if (!$candidatura->candidato || $candidatura->candidato->trashed()) {
            return 'O candidato excluiu a conta e seus dados pessoais foram removidos, conforme a LGPD.';
        }

        $dias = CandidaturaPolicy::CARENCIA_DIAS;

        if ($candidatura->status === 'reprovado') {
            return "Este processo foi encerrado há mais de {$dias} dias. Os dados pessoais do candidato não estão mais acessíveis.";
        }

        return "A vaga foi encerrada há mais de {$dias} dias sem decisão sobre esta candidatura. Os dados pessoais do candidato não estão mais acessíveis.";
    }

    public function updateStatus(Request $request, Vaga $vaga, Candidatura $candidatura)
    {
        $this->autorizarVaga($vaga);
        abort_unless($candidatura->vaga_id === $vaga->id, 404);

        $novoStatus = $request->input('status');
        $mudandoStatus = $novoStatus !== $candidatura->status;

        if ($mudandoStatus) {
            abort_unless(
                $candidatura->podeTransicionarPara($novoStatus),
                422,
                "Transição de '{$candidatura->status}' para '{$novoStatus}' não permitida."
            );
        }

        $dados = $mudandoStatus ? ['status' => $novoStatus] : [];

        if ($novoStatus === 'entrevista') {
            $request->validate([
                'entrevista_data'  => 'required|date|after:now',
                'entrevista_local' => 'required|string|max:255',
            ], [
                'entrevista_data.required'  => 'Informe a data da entrevista.',
                'entrevista_data.after'     => 'A data da entrevista deve ser futura.',
                'entrevista_local.required' => 'Informe o local da entrevista.',
            ]);

            $dados['entrevista_data']        = $request->entrevista_data;
            $dados['entrevista_local']       = $request->entrevista_local;
            $dados['entrevista_observacoes'] = $request->entrevista_observacoes;
        }

        if ($request->filled('observacoes_internas')) {
            $dados['observacoes_internas'] = $request->observacoes_internas;
        }

        $statusAnterior = $candidatura->status;

        $candidatura->update($dados);

        if ($mudandoStatus) {
            // Registro do processo: quem decidiu, quando, e sobre qual currículo.
            // Nenhum dado de identidade é copiado para cá.
            $candidatura->eventos()->create([
                'tipo'                 => CandidaturaEvento::TIPO_TRANSICAO,
                'status_anterior'      => $statusAnterior,
                'status_novo'          => $novoStatus,
                'autor_id'             => Auth::id(),
                'curriculo_id_vigente' => $candidatura->candidato?->curriculo_atual_id,
                'observacao'           => $request->input('observacoes_internas'),
                'ocorrido_em'          => now(),
            ]);

            $this->enviarEmailStatus($candidatura, $novoStatus);
        }

        $mensagem = $mudandoStatus
            ? 'Status atualizado para "' . Candidatura::$statusLabel[$novoStatus] . '".'
            : 'Observações salvas com sucesso.';

        return redirect()
            ->route('coord.candidaturas.show', [$vaga, $candidatura])
            ->with('sucesso', $mensagem);
    }

    public function downloadCurriculo(Vaga $vaga, Candidatura $candidatura)
    {
        $this->autorizarVaga($vaga);
        abort_unless($candidatura->vaga_id === $vaga->id, 404);

        // O decaimento precisa valer aqui também: sem isto o currículo seria a
        // porta aberta de um processo cujos dados já deixaram de estar acessíveis.
        abort_unless(Auth::user()->can('baixarCurriculo', $candidatura), 403, 'Acesso aos dados deste candidato expirou.');

        return Storage::disk('local')->download(
            $candidatura->curriculo_path,
            $candidatura->curriculo_nome_original ?? 'curriculo.pdf'
        );
    }

    private function autorizarVaga(Vaga $vaga): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && (int) $vaga->coordenador_id !== (int) $user->id) {
            abort(403, 'Acesso não autorizado.');
        }
    }

    private function enviarEmailStatus(Candidatura $candidatura, string $status): void
    {
        try {
            $mail = match ($status) {
                'entrevista' => new ConviteEntrevistaMail($candidatura),
                'aprovado'   => new AprovacaoMail($candidatura),
                'reprovado'  => new ReprovacaoMail($candidatura),
                default      => null,
            };

            if ($mail) {
                Mail::to($candidatura->email)->send($mail);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de status: ' . $e->getMessage());
        }
    }
}
