<?php

namespace App\Http\Controllers\Candidato;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Candidatura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        return view('candidato.candidaturas.index', compact('candidaturas'));
    }

    public function show(Candidatura $candidatura)
    {
        abort_unless(
            $candidatura->candidato_id === $this->candidato()->id,
            403
        );

        $candidatura->load('vaga');

        return view('candidato.candidaturas.show', compact('candidatura'));
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
