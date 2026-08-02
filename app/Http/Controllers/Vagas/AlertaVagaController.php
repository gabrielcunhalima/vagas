<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\AlertaVaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AlertaVagaController extends Controller
{
    public function create()
    {
        $candidato = Auth::guard('candidato')->user();

        return Inertia::render('Publico/Alertas', [
            'areas'       => \App\Models\Vagas\Vaga::$areas,
            'modalidades' => \App\Models\Vagas\Vaga::$modalidadesLabel,
            'tipos'       => \App\Models\Vagas\Vaga::$tiposLabel,
            'email'       => $candidato?->email,
        ]);
    }

    public function store(Request $request)
    {
        $candidato = Auth::guard('candidato')->user();
        $email = $candidato?->email ?? $request->email;

        $request->validate([
            'email'              => $candidato ? 'nullable' : 'required|email|max:255',
            'areas'              => 'nullable|array',
            'modalidades'        => 'nullable|array',
            'tipos'              => 'nullable|array',
            'lgpd_consentimento' => 'accepted',
        ], [
            'lgpd_consentimento.accepted' => 'É necessário concordar com o tratamento dos seus dados para ativar os alertas.',
        ]);

        $alerta = AlertaVaga::where('email', $email)->first();

        if ($alerta) {
            $alerta->update([
                'areas'                 => $request->areas ?? [],
                'modalidades'           => $request->modalidades ?? [],
                'tipos'                 => $request->tipos ?? [],
                'ativo'                 => true,
                'lgpd_consentimento'    => true,
                'lgpd_consentimento_em' => $alerta->lgpd_consentimento_em ?? now(),
            ]);
        } else {
            AlertaVaga::create([
                'email'                 => $email,
                'areas'                 => $request->areas ?? [],
                'modalidades'           => $request->modalidades ?? [],
                'tipos'                 => $request->tipos ?? [],
                'lgpd_consentimento'    => true,
                'lgpd_consentimento_em' => now(),
            ]);
        }

        return back()->with('success', 'Alerta cadastrado! Você receberá e-mails quando houver novas vagas.');
    }

    public function cancelar(string $token)
    {
        $alerta = AlertaVaga::where('token', $token)->firstOrFail();
        $alerta->update(['ativo' => false]);
        return Inertia::render('Publico/AlertaCancelado');
    }
}
