<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Vaga;
use App\Http\Requests\Vagas\VagaRequest;
use App\Mail\Vagas\VagaAutorizadaMail;
use App\Mail\Vagas\VagaRecusadaMail;
use App\Mail\Vagas\AlertaNovaVagaMail;
use App\Models\Vagas\AlertaVaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VagaController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = $user->isAdmin()
            ? Vaga::query()->latest()
            : Vaga::where('coordenador_id', $user->id)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('encerramento_de')) {
            $query->whereDate('data_encerramento', '>=', $request->encerramento_de);
        }

        if ($request->filled('encerramento_ate')) {
            $query->whereDate('data_encerramento', '<=', $request->encerramento_ate);
        }

        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }

        $vagas = $query->paginate(15)->withQueryString();

        $areas = Vaga::$areas;

        return view('vagas.coordenador.index', compact('vagas', 'areas'));
    }

    public function create()
    {
        return view('vagas.coordenador.form', ['vaga' => new Vaga()]);
    }

    public function store(VagaRequest $request)
    {
        $dados = $request->validated();
        $dados['coordenador_id']  = Auth::id();
        $dados['curso_desejado']  = array_values(array_filter($dados['curso_desejado'] ?? []));
        $dados['notificar_email'] = $request->boolean('notificar_email');
        $dados['status'] = $request->input('acao') === 'publicar'
            ? 'aguardando_autorizacao'
            : 'rascunho';

        Vaga::create($dados);

        $mensagem = $dados['status'] === 'aguardando_autorizacao'
            ? 'Vaga enviada para autorização com sucesso.'
            : 'Rascunho salvo com sucesso.';

        return redirect()
            ->route('coord.vagas.index')
            ->with('sucesso', $mensagem);
    }

    public function edit(Vaga $vaga)
    {
        $this->autorizarCoordenador($vaga);

        abort_if(
            in_array($vaga->status, ['ativa', 'encerrada']),
            403,
            'Vagas ativas ou encerradas não podem ser editadas.'
        );

        return view('vagas.coordenador.form', compact('vaga'));
    }

    public function update(VagaRequest $request, Vaga $vaga)
    {
        $this->autorizarCoordenador($vaga);

        $dados = $request->validated();
        $dados['curso_desejado']  = array_values(array_filter($dados['curso_desejado'] ?? []));
        $dados['notificar_email'] = $request->boolean('notificar_email');
        $dados['status'] = $request->input('acao') === 'publicar'
            ? 'aguardando_autorizacao'
            : 'rascunho';

        $vaga->update($dados);

        return redirect()
            ->route('coord.vagas.index')
            ->with('sucesso', 'Vaga atualizada com sucesso.');
    }

    public function destroy(Vaga $vaga)
    {
        $this->autorizarCoordenador($vaga);
        $vaga->delete();

        return redirect()
            ->route('coord.vagas.index')
            ->with('sucesso', 'Vaga removida.');
    }

    public function submeter(Vaga $vaga)
    {
        $this->autorizarCoordenador($vaga);
        abort_unless($vaga->status === 'rascunho', 403, 'Apenas rascunhos podem ser submetidos.');

        $vaga->update(['status' => 'aguardando_autorizacao']);

        return back()->with('sucesso', 'Vaga enviada para autorização.');
    }

    public function desativar(Vaga $vaga)
    {
        $this->autorizarCoordenador($vaga);

        $vaga->update([
            'status'       => 'inativa',
            'encerrada_em' => now(),
        ]);

        return back()->with('sucesso', 'Vaga desativada.');
    }

    public function reativar(Vaga $vaga)
    {
        $this->autorizarCoordenador($vaga);
        abort_unless($vaga->status === 'inativa', 403, 'Apenas vagas inativas podem ser reativadas.');
        abort_if(
            $vaga->data_encerramento && $vaga->data_encerramento->isPast(),
            403,
            'Vagas com período de encerramento expirado não podem ser reativadas.'
        );

        $vaga->update([
            'status'       => 'ativa',
            'encerrada_em' => null,
        ]);

        return back()->with('sucesso', 'Vaga reativada com sucesso.');
    }

    public function toggleNotificacao(Vaga $vaga)
    {
        $this->autorizarCoordenador($vaga);
        $vaga->update(['notificar_email' => !$vaga->notificar_email]);

        return back()->with('sucesso',
            $vaga->notificar_email
                ? 'Notificações por e-mail ativadas.'
                : 'Notificações por e-mail desativadas.'
        );
    }

    public function indexGestor(Request $request)
    {
        $status = $request->input('status', 'aguardando_autorizacao');
        $query  = Vaga::where('status', $status)->with('coordenador')->latest();

        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }

        $vagas = $query->paginate(15)->withQueryString();

        return view('vagas.gestor.index', compact('vagas', 'status'));
    }

    public function showGestor(Vaga $vaga)
    {
        return view('vagas.gestor.show', compact('vaga'));
    }

    public function autorizar(Request $request, Vaga $vaga)
    {
        abort_unless($vaga->status === 'aguardando_autorizacao', 403);

        $vaga->update([
            'status'        => 'ativa',
            'gestor_id'     => Auth::id(),
            'autorizada_em' => now(),
        ]);

        if ($vaga->coordenador) {
            try {
                Mail::to($vaga->coordenador->email)->send(new VagaAutorizadaMail($vaga));
            } catch (\Exception $e) {
                Log::error('Erro ao enviar e-mail VagaAutorizada: ' . $e->getMessage());
            }
        }

        // Disparar alertas para candidatos inscritos
        AlertaVaga::ativos()->cursor()->each(function ($alerta) use ($vaga) {
            if ($alerta->compativel($vaga)) {
                try {
                    Mail::to($alerta->email)->send(new AlertaNovaVagaMail($vaga, $alerta));
                } catch (\Exception $e) {
                    Log::error('Erro ao enviar alerta de vaga: ' . $e->getMessage());
                }
            }
        });

        return redirect()
            ->route('gestor.vagas.index')
            ->with('sucesso', "Vaga \"{$vaga->titulo}\" autorizada e publicada.");
    }

    public function recusar(Request $request, Vaga $vaga)
    {
        $request->validate([
            'motivo_recusa' => 'required|string|min:10|max:1000',
        ], [
            'motivo_recusa.required' => 'Informe o motivo da recusa.',
            'motivo_recusa.min'      => 'O motivo deve ter pelo menos 10 caracteres.',
        ]);

        abort_unless($vaga->status === 'aguardando_autorizacao', 403);

        $vaga->update([
            'status'        => 'recusada',
            'gestor_id'     => Auth::id(),
            'motivo_recusa' => $request->motivo_recusa,
        ]);

        if ($vaga->coordenador) {
            try {
                Mail::to($vaga->coordenador->email)->send(new VagaRecusadaMail($vaga));
            } catch (\Exception $e) {
                Log::error('Erro ao enviar e-mail VagaRecusada: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('gestor.vagas.index')
            ->with('aviso', "Vaga \"{$vaga->titulo}\" recusada.");
    }

    private function autorizarCoordenador(Vaga $vaga): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && (int) $vaga->coordenador_id !== (int) $user->id) {
            abort(403, 'Acesso não autorizado a esta vaga.');
        }
    }
}
