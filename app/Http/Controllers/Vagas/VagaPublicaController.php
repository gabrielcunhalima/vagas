<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Support\Drhflow\DominioDrhflowRepository;
use App\Support\Drhflow\DrhflowIndisponivelException;
use App\Support\Drhflow\VagaDrhflow;
use App\Support\Drhflow\VagaDrhflowRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * A listagem e o detalhe públicos, agora lendo do DRHFlow.
 *
 * A vaga é identificada por `CD_VAGA_EMPREGO`, não pelo `id` da tabela MySQL —
 * que continua existindo para o coordenador e o gestor, no caminho legado.
 */
class VagaPublicaController extends Controller
{
    /** Filtros que a origem sustenta (design D4). Área, modalidade e curso saíram. */
    private const FILTROS = [
        'busca', 'tipo', 'escolaridade', 'cidade', 'estado',
        'projeto', 'salario_min', 'salario_max',
    ];

    public function __construct(
        private readonly VagaDrhflowRepository $vagas,
        private readonly DominioDrhflowRepository $dominios,
    ) {}

    public function index(Request $request)
    {
        $filtros = array_filter(
            $request->only(self::FILTROS),
            fn ($v) => $v !== null && $v !== ''
        );

        try {
            $pagina = $this->vagas->paginar($filtros, porPagina: 12, pagina: (int) $request->integer('page', 1));
            $total = $this->vagas->contar();

            // Uma vaga com função fora de VW_FUNCAO_5ANOS some da listagem sem
            // aviso. O log é o que torna "minha vaga não aparece" diagnosticável.
            $this->vagas->registrarVagasOcultasPorFuncao();
        } catch (DrhflowIndisponivelException) {
            return $this->listagemIndisponivel($filtros);
        }

        $pagina->setCollection(
            $pagina->getCollection()->map(fn (VagaDrhflow $v) => $v->toArray())
        );

        return Inertia::render('Publico/Vagas/Index', array_merge([
            'vagas' => $pagina->withQueryString(),
            'total' => $total,
            'filtros' => $request->only([...self::FILTROS, 'ordenar']),
            'indisponivel' => false,
        ], $this->opcoesDeFiltro()));
    }

    public function show(int $vaga)
    {
        try {
            $encontrada = $this->vagas->buscarPorCodigo($vaga);
        } catch (DrhflowIndisponivelException) {
            abort(503, 'As vagas estão temporariamente indisponíveis. Tente novamente em alguns minutos.');
        }

        // Código inexistente, vaga fechada e prazo vencido são o mesmo 404: de
        // fora, todos são "vaga não encontrada", e separá-los revelaria a
        // existência de vagas que o candidato não pode ver.
        abort_if($encontrada === null, 404);

        return Inertia::render('Publico/Vagas/Show', [
            'vaga' => $encontrada->toArray(),
        ]);
    }

    /**
     * Indisponibilidade do DRHFlow: a tela diz o que aconteceu.
     *
     * Uma listagem vazia seria lida como "não há vagas abertas" — a leitura
     * errada que a capacidade `vagas-drhflow` proíbe explicitamente.
     *
     * @param  array<string, mixed>  $filtros
     */
    private function listagemIndisponivel(array $filtros)
    {
        return Inertia::render('Publico/Vagas/Index', [
            'vagas' => [
                'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1, 'links' => [],
            ],
            'total' => 0,
            'filtros' => $filtros,
            'indisponivel' => true,
            'tipos' => [],
            'escolaridades' => [],
            'projetos' => [],
            'municipios' => [],
            'ufs' => [],
        ]);
    }

    /**
     * Opções dos selects, vindas dos domínios do próprio DRHFlow.
     *
     * Se os domínios falharem depois da listagem ter dado certo, os filtros
     * ficam vazios mas as vagas continuam na tela — perder o select é menos
     * grave que perder a lista.
     *
     * @return array<string, mixed>
     */
    private function opcoesDeFiltro(): array
    {
        try {
            return [
                'tipos' => $this->dominios->tiposAdmissao(),
                'escolaridades' => $this->dominios->grausInstrucao(),
                'projetos' => $this->dominios->projetosComVaga(),
                'municipios' => $this->dominios->municipiosComVaga(),
                'ufs' => $this->dominios->ufs(),
            ];
        } catch (DrhflowIndisponivelException) {
            return [
                'tipos' => [], 'escolaridades' => [], 'projetos' => [],
                'municipios' => [], 'ufs' => [],
            ];
        }
    }
}
