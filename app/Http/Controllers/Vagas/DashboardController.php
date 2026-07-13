<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $vagasQuery = Vaga::where('coordenador_id', (int) $user->id);

        $candidatosQuery = fn() => Candidatura::whereHas(
            'vaga',
            fn($q) => $q->where('coordenador_id', $user->id)
        );

        $stats = [
            'total_vagas'      => $vagasQuery->count(),
            'vagas_ativas'     => (clone $vagasQuery)->where('status', 'ativa')->count(),
            'vagas_rascunho'   => (clone $vagasQuery)->where('status', 'rascunho')->count(),
            'aguardando_aut'   => (clone $vagasQuery)->where('status', 'aguardando_autorizacao')->count(),
            'total_candidatos' => $candidatosQuery()->count(),
            'candidatos_novos' => $candidatosQuery()->where('status', 'recebida')->count(),
            'entrevistas_hoje' => $candidatosQuery()
                                    ->where('status', 'entrevista')
                                    ->whereDate('entrevista_data', Carbon::today())
                                    ->count(),
        ];

        $vagasRecentes = Vaga::where('coordenador_id', $user->id)
            ->withCount('candidaturas')
            ->latest()
            ->take(5)
            ->get();

        $candidaturasRecentes = Candidatura::whereHas(
            'vaga',
            fn($q) => $q->where('coordenador_id', $user->id)
        )->with('vaga')->latest()->take(5)->get();

        return view('vagas.coordenador.dashboard', compact(
            'stats', 'vagasRecentes', 'candidaturasRecentes'
        ));
    }

    public function indexGestor()
    {
        $stats = [
            'aguardando_aut' => Vaga::where('status', 'aguardando_autorizacao')->count(),
            'autorizadas'    => Vaga::where('gestor_id', Auth::id())->where('status', 'ativa')->count(),
            'recusadas'      => Vaga::where('gestor_id', Auth::id())->where('status', 'recusada')->count(),
            'total_ativas'   => Vaga::where('status', 'ativa')->count(),
        ];

        $vagasPendentes = Vaga::where('status', 'aguardando_autorizacao')
            ->with('coordenador')
            ->latest()
            ->take(8)
            ->get();

        return view('vagas.gestor.dashboard', compact('stats', 'vagasPendentes'));
    }
}
