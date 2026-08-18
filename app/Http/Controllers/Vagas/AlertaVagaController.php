<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\AlertaVaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * O alerta deixa de ser um e-mail solto numa tabela e passa a pertencer a uma conta.
 *
 * A exigência de e-mail verificado (imposta na rota) existe porque o alerta origina
 * envio: sem ela, qualquer um inscreveria o endereço de terceiros e o portal viraria
 * o remetente do spam. Já o cancelamento por token continua sem autenticação —
 * exigir login para sair de uma lista de e-mails é abusivo.
 */
class AlertaVagaController extends Controller
{
    public function create()
    {
        $candidato = Auth::guard('candidato')->user();
        $alerta    = $candidato->alerta;

        return view('publico.alertas', [
            'areas'       => \App\Models\Vagas\Vaga::$areas,
            'modalidades' => \App\Models\Vagas\Vaga::$modalidadesLabel,
            'tipos'       => \App\Models\Vagas\Vaga::$tiposLabel,
            'email'       => $candidato->email,
            'alerta'      => $alerta ? [
                'areas'       => $alerta->areas ?? [],
                'modalidades' => $alerta->modalidades ?? [],
                'tipos'       => $alerta->tipos ?? [],
                'ativo'       => $alerta->ativo,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $candidato = Auth::guard('candidato')->user();

        // O e-mail não é coletado: vem da conta, e acompanha a troca dela.
        $request->validate([
            'areas'       => 'nullable|array',
            'modalidades' => 'nullable|array',
            'tipos'       => 'nullable|array',
        ]);

        $preferencias = [
            'email'       => $candidato->email,
            'areas'       => $request->areas ?? [],
            'modalidades' => $request->modalidades ?? [],
            'tipos'       => $request->tipos ?? [],
            'ativo'       => true,
        ];

        // Um alerta por conta: reconfigurar atualiza o existente, inclusive quando
        // ele havia sido cancelado pelo link do e-mail.
        //
        // Consulta pela relação, não pelo atributo: um `alerta` já resolvido como
        // nulo na instância ficaria em cache e faria nascer um segundo registro.
        $alerta = $candidato->alerta()->first();

        if ($alerta) {
            $alerta->update($preferencias);
        } else {
            $candidato->alerta()->create($preferencias + [
                'lgpd_consentimento'    => true,
                'lgpd_consentimento_em' => $candidato->lgpd_consentimento_em ?? now(),
            ]);
        }

        return back()->with('success', 'Alerta salvo! Você receberá e-mails quando houver novas vagas.');
    }

    public function cancelar(string $token)
    {
        $alerta = AlertaVaga::where('token', $token)->firstOrFail();
        $alerta->update(['ativo' => false]);

        return view('publico.alerta-cancelado');
    }
}
