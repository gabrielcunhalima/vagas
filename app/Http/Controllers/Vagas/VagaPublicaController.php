<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Vaga;
use Illuminate\Http\Request;

class VagaPublicaController extends Controller
{
    public function index(Request $request)
    {
        $query = Vaga::ativas();

        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }
        if ($request->filled('area')) {
            $query->porArea($request->area);
        }
        if ($request->filled('tipo')) {
            $query->porTipo($request->tipo);
        }
        if ($request->filled('modalidade')) {
            $query->porModalidade($request->modalidade);
        }
        if ($request->filled('curso')) {
            $query->porCurso($request->curso);
        }
        if ($request->filled('cidade')) {
            $query->where('cidade', 'like', '%' . $request->cidade . '%');
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('salario_min')) {
            $query->where('remuneracao', '>=', $request->salario_min);
        }
        if ($request->filled('salario_max')) {
            $query->where(fn($q) => $q->where('remuneracao_max', '<=', $request->salario_max)
                                       ->orWhere('remuneracao', '<=', $request->salario_max));
        }

        $ordenar = $request->get('ordenar', 'recentes');
        $query->when($ordenar === 'recentes', fn($q) => $q->latest('autorizada_em'))
              ->when($ordenar === 'encerramento', fn($q) => $q->orderBy('data_encerramento'))
              ->when($ordenar === 'relevancia', fn($q) => $q->latest('autorizada_em'));

        $total  = $query->count();
        $vagas  = $query->paginate(12)->withQueryString();
        $areas  = Vaga::$areas;
        $cursos = Vaga::$cursos;

        return view('vagas.publico.index', compact('vagas', 'areas', 'cursos', 'total'));
    }

    public function show(Vaga $vaga)
    {
        abort_unless($vaga->esta_aberta, 404);

        $vagasRelacionadas = Vaga::ativas()
            ->where('area', $vaga->area)
            ->where('id', '!=', $vaga->id)
            ->latest('autorizada_em')
            ->take(3)
            ->get();

        return view('vagas.publico.show', compact('vaga', 'vagasRelacionadas'));
    }
}
