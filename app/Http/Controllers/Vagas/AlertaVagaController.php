<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\AlertaVaga;
use Illuminate\Http\Request;

class AlertaVagaController extends Controller
{
    public function create()
    {
        $areas      = \App\Models\Vagas\Vaga::$areas;
        $modalidades = \App\Models\Vagas\Vaga::$modalidadesLabel;
        $tipos      = \App\Models\Vagas\Vaga::$tiposLabel;
        return view('vagas.publico.alertas', compact('areas', 'modalidades', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'              => 'required|email|max:255',
            'areas'              => 'nullable|array',
            'modalidades'        => 'nullable|array',
            'tipos'              => 'nullable|array',
            'lgpd_consentimento' => 'accepted',
        ], [
            'lgpd_consentimento.accepted' => 'É necessário concordar com o tratamento dos seus dados para ativar os alertas.',
        ]);

        $alerta = AlertaVaga::where('email', $request->email)->first();

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
                'email'                 => $request->email,
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
        return view('vagas.publico.alerta-cancelado');
    }
}
