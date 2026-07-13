<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use App\Mail\Vagas\ConviteEntrevistaMail;
use App\Mail\Vagas\AprovacaoMail;
use App\Mail\Vagas\ReprovacaoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CandidaturaController extends Controller
{
    public function todas(Request $request)
    {
        $user = Auth::user();

        $base = fn() => Candidatura::when(
            !$user->isAdmin(),
            fn($q) => $q->whereHas('vaga', fn($q2) => $q2->where('coordenador_id', $user->id))
        );

        $query = $base()->with('vaga')->latest();

        if ($request->filled('status')) {
            $query->porStatus($request->status);
        }
        if ($request->filled('vaga_id')) {
            $query->where('vaga_id', $request->vaga_id);
        }
        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }

        $candidaturas = $query->paginate(25)->withQueryString();

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

        return view('vagas.coordenador.candidaturas.todas', compact('candidaturas', 'vagas', 'contadores'));
    }

    public function index(Request $request, Vaga $vaga)
    {
        $this->autorizarVaga($vaga);

        $query = $vaga->candidaturas()->latest();

        if ($request->filled('status')) {
            $query->porStatus($request->status);
        }

        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }

        $candidaturas = $query->paginate(20)->withQueryString();

        $contadores = [
            'todos'      => $vaga->candidaturas()->count(),
            'recebida'   => $vaga->candidaturas()->porStatus('recebida')->count(),
            'em_analise' => $vaga->candidaturas()->porStatus('em_analise')->count(),
            'entrevista' => $vaga->candidaturas()->porStatus('entrevista')->count(),
            'aprovado'   => $vaga->candidaturas()->porStatus('aprovado')->count(),
            'reprovado'  => $vaga->candidaturas()->porStatus('reprovado')->count(),
        ];

        return view('vagas.coordenador.candidaturas.index', compact(
            'vaga', 'candidaturas', 'contadores'
        ));
    }

    public function show(Vaga $vaga, Candidatura $candidatura)
    {
        $this->autorizarVaga($vaga);
        abort_unless($candidatura->vaga_id === $vaga->id, 404);

        return view('vagas.coordenador.candidaturas.show', compact('vaga', 'candidatura'));
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

        $candidatura->update($dados);

        if ($mudandoStatus) {
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
        abort_unless($candidatura->temCurriculo(), 404, 'Currículo não encontrado.');

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
