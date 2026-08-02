<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Models\Vagas\Vaga;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        $total = $query->count();

        /* A listagem é um split view: o painel de detalhe renderiza a vaga
           selecionada direto do payload, sem requisição por clique. Por isso os
           itens paginados carregam os mesmos campos da página de detalhe. */
        $vagas = $query->paginate(12)->withQueryString()
            ->through(fn(Vaga $v) => $this->vagaCompleta($v));

        return Inertia::render('Publico/Vagas/Index', [
            'vagas'   => $vagas,
            'areas'   => Vaga::$areas,
            'cursos'  => Vaga::$cursos,
            'total'   => $total,
            'filtros' => $request->only([
                'busca', 'area', 'tipo', 'modalidade', 'curso',
                'cidade', 'estado', 'salario_min', 'salario_max', 'ordenar',
            ]),
        ]);
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

        return Inertia::render('Publico/Vagas/Show', [
            'vaga'         => $this->vagaCompleta($vaga),
            'relacionadas' => $vagasRelacionadas->map(fn(Vaga $v) => $this->vagaResumo($v)),
        ]);
    }

    /** Campos expostos publicamente nos cards de listagem. */
    private function vagaResumo(Vaga $v): array
    {
        return $v->only([
            'id', 'titulo', 'descricao', 'tipo', 'area', 'modalidade',
            'remuneracao', 'remuneracao_max', 'carga_horaria',
            'cidade', 'estado', 'local_trabalho',
            'data_encerramento', 'autorizada_em', 'created_at',
        ]);
    }

    /** Campos expostos publicamente na página de detalhe. */
    private function vagaCompleta(Vaga $v): array
    {
        return array_merge($this->vagaResumo($v), $v->only([
            'requisitos', 'requisitos_desejaveis', 'beneficios', 'curso_desejado',
            'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'pais',
            'projeto_nome', 'projeto_codigo',
        ]), [
            'endereco_completo' => $v->endereco_completo,
        ]);
    }
}
