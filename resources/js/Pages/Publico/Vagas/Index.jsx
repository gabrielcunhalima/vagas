import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { AlertTriangle, Bell, Search, SlidersHorizontal } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import EmptyState from '@/components/EmptyState';
import Pagination from '@/components/Pagination';
import VagaDetalhePainel from '@/components/VagaDetalhePainel';
import VagaListaItem from '@/components/VagaListaItem';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetContent, SheetTitle } from '@/components/ui/sheet';
import useMediaQuery from '@/hooks/useMediaQuery';
import { asset } from '@/lib/asset';
import { CONTAINER_LARGO as CONTAINER } from '@/lib/layout';

const TODOS = '__todos__';

function limparParams(params) {
    return Object.fromEntries(
        Object.entries(params).filter(([, v]) => v !== '' && v !== null && v !== undefined && v !== TODOS),
    );
}

function FiltroSelect({ label, value, onChange, options, placeholder = 'Todas' }) {
    return (
        <div className="flex flex-col gap-1.5">
            <Label className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">{label}</Label>
            <Select value={value || TODOS} onValueChange={onChange}>
                <SelectTrigger className="w-full">
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value={TODOS}>{placeholder}</SelectItem>
                    {options.map(([v, l]) => (
                        <SelectItem key={v} value={v}>
                            {l}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}

/*
 * Os filtros são os que o DRHFlow sustenta. Área, modalidade e curso desejado
 * saíram: `EN_VAGA_EMPREGO` não tem coluna equivalente, e um filtro sem lastro
 * na origem devolveria resultado arbitrário.
 *
 * As opções de tipo, escolaridade, projeto, município e UF vêm do servidor —
 * são os domínios do próprio DRHFlow, não listas fixas no cliente.
 */
export default function Index({
    vagas,
    total,
    filtros = {},
    indisponivel = false,
    tipos = {},
    escolaridades = {},
    projetos = {},
    municipios = [],
    ufs = {},
}) {
    const [f, setF] = useState({
        busca: filtros.busca ?? '',
        tipo: filtros.tipo ?? '',
        escolaridade: filtros.escolaridade ?? '',
        projeto: filtros.projeto ?? '',
        cidade: filtros.cidade ?? '',
        estado: filtros.estado ?? '',
        salario_min: filtros.salario_min ?? '',
        salario_max: filtros.salario_max ?? '',
        ordenar: filtros.ordenar ?? '',
    });

    /* Seleção derivada, não sincronizada: os filtros usam preserveState, então a
       instância do componente sobrevive à troca de resultado. Um único fallback
       cobre seleção inicial, filtro que preserva a vaga, filtro que a remove e
       troca de página — sem o quadro de seleção velha que um useEffect deixaria. */
    const [selecionadaId, setSelecionadaId] = useState(null);
    const [sheetAberto, setSheetAberto] = useState(false);
    const selecionada = vagas.data.find((v) => v.id === selecionadaId) ?? vagas.data[0] ?? null;

    /* xl (1280px) é onde a terceira coluna passa a caber; abaixo disso o detalhe
       vai para o Sheet. O Sheet é portalado, então precisa desse teste em JS. */
    const splitView = useMediaQuery('(min-width: 80rem)');

    const temFiltros = Object.values(limparParams(f)).length > 0;

    function selecionarVaga(id) {
        setSelecionadaId(id);
        if (!splitView) setSheetAberto(true);
    }

    function set(campo, valor) {
        setF((prev) => ({ ...prev, [campo]: valor === TODOS ? '' : valor }));
    }

    function aplicar(extra = {}) {
        router.get(route('vagas.publicas.index'), limparParams({ ...f, ...extra }), {
            preserveState: true,
            preserveScroll: true,
        });
    }

    /* Selects aplicam o filtro imediatamente ao selecionar */
    function selecionar(campo, v) {
        const valor = v === TODOS ? '' : v;
        setF((prev) => ({ ...prev, [campo]: valor }));
        aplicar({ [campo]: valor });
    }

    function limpar() {
        router.get(route('vagas.publicas.index'));
    }

    return (
        <PublicLayout title="Vagas abertas">
            {/* ── Hero: foto + overlay 50% + texto branco ── */}
            <section className="relative isolate overflow-hidden">
                <img
                    src={asset('imagens/home-hero.jpg')}
                    alt=""
                    className="absolute inset-0 -z-20 size-full object-cover"
                />
                <div className="absolute inset-0 -z-10 bg-black/50" />
                <div className="absolute inset-0 -z-10 bg-gradient-to-r from-brand-deep/80 to-transparent" />

                <div className={`${CONTAINER} py-10 lg:py-14`}>
                    {/* A partir de lg a hero abre em duas colunas: busca à esquerda, alerta à direita. */}
                    <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-center lg:gap-12">
                        <div className="max-w-2xl">
                            {/* text-5xl só a partir de xl: em lg a coluna esquerda é estreita e o título quebraria em 3 linhas */}
                            <h1 className="text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl xl:text-5xl">
                                Encontre sua próxima oportunidade
                            </h1>
                            <p className="mt-3 text-sm text-white/80 sm:text-base">
                                {total} {total === 1 ? 'vaga aberta' : 'vagas abertas'} em projetos.
                            </p>

                            <form
                                onSubmit={(e) => {
                                    e.preventDefault();
                                    aplicar();
                                }}
                                className="mt-7 flex max-w-xl gap-2"
                            >
                                <div className="relative flex-1">
                                    <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input
                                        value={f.busca}
                                        onChange={(e) => set('busca', e.target.value)}
                                        placeholder="Cargo, área ou palavra-chave"
                                        className="h-11 border-transparent bg-card pl-9 shadow-lg dark:bg-card"
                                    />
                                </div>
                                <Button type="submit" className="h-11 px-6">
                                    Buscar
                                </Button>
                            </form>
                        </div>

                        {/* CTA de alertas: superfície própria para não sumir sobre a foto */}
                        <div className="flex flex-col gap-4 rounded-xl bg-white/10 p-4 ring-1 ring-white/25 backdrop-blur-sm transition-colors hover:bg-white/15 sm:flex-row sm:items-center sm:gap-5 lg:flex-col lg:items-start lg:gap-4">
                            <div className="flex min-w-0 flex-1 items-start gap-3 lg:w-full lg:flex-none">
                                <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/30">
                                    <Bell className="size-5 text-white" />
                                </span>
                                <div className="min-w-0">
                                    <p className="text-base font-semibold text-white">Não perca nenhuma vaga</p>
                                    <p className="mt-0.5 text-sm text-white/80">
                                        Receba por e-mail as vagas que combinam com o seu perfil, assim que forem
                                        publicadas.
                                    </p>
                                </div>
                            </div>
                            <Button asChild className="h-11 w-full px-6 sm:w-auto lg:w-full">
                                <Link href={route('alertas.create')}>Criar alerta de vagas</Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Conteúdo ── */}
            <section>
                <div className={`${CONTAINER} pt-8`}>
                    <div className="grid items-start gap-6 lg:grid-cols-[240px_1fr] xl:grid-cols-[240px_minmax(340px,420px)_1fr]">
                        {/* Filtros */}
                        <aside className="rounded-xl bg-card p-4 ring-1 ring-foreground/10 lg:sticky lg:top-20">
                            <div className="mb-4 flex items-center justify-between">
                                <span className="inline-flex items-center gap-2 text-sm font-semibold">
                                    <SlidersHorizontal className="size-4 text-primary" /> Filtros
                                </span>
                                {temFiltros && (
                                    <button
                                        type="button"
                                        onClick={limpar}
                                        className="cursor-pointer text-xs font-medium text-muted-foreground transition-colors hover:text-foreground"
                                    >
                                        Limpar
                                    </button>
                                )}
                            </div>

                            <div className="flex flex-col gap-4">
                                <FiltroSelect
                                    label="Tipo de contratação"
                                    value={f.tipo}
                                    onChange={(v) => selecionar('tipo', v)}
                                    options={Object.entries(tipos)}
                                    placeholder="Todos"
                                />
                                <FiltroSelect
                                    label="Escolaridade"
                                    value={f.escolaridade}
                                    onChange={(v) => selecionar('escolaridade', v)}
                                    options={Object.entries(escolaridades)}
                                />
                                <FiltroSelect
                                    label="Projeto"
                                    value={f.projeto}
                                    onChange={(v) => selecionar('projeto', v)}
                                    options={Object.entries(projetos)}
                                    placeholder="Todos"
                                />

                                <div className="flex flex-col gap-1.5">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                        Localização
                                    </Label>
                                    <div className="grid grid-cols-[1fr_76px] gap-2">
                                        <Select
                                            value={f.cidade || TODOS}
                                            onValueChange={(v) => selecionar('cidade', v)}
                                        >
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="Cidade" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value={TODOS}>Cidade</SelectItem>
                                                {municipios.map((cidade) => (
                                                    <SelectItem key={cidade} value={cidade}>
                                                        {cidade}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <Select value={f.estado || TODOS} onValueChange={(v) => selecionar('estado', v)}>
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="UF" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value={TODOS}>UF</SelectItem>
                                                {Object.keys(ufs).map((uf) => (
                                                    <SelectItem key={uf} value={uf}>
                                                        {uf}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <div className="flex flex-col gap-1.5">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                        Faixa salarial (R$)
                                    </Label>
                                    <div className="grid grid-cols-2 gap-2">
                                        <Input
                                            type="number"
                                            min="0"
                                            value={f.salario_min}
                                            onChange={(e) => set('salario_min', e.target.value)}
                                            placeholder="Mín."
                                        />
                                        <Input
                                            type="number"
                                            min="0"
                                            value={f.salario_max}
                                            onChange={(e) => set('salario_max', e.target.value)}
                                            placeholder="Máx."
                                        />
                                    </div>
                                </div>

                                <Button onClick={() => aplicar()} variant="outline" className="w-full">
                                    Buscar
                                </Button>
                            </div>
                        </aside>

                        {/* Lista */}
                        <div className={vagas.data.length === 0 ? 'min-w-0 xl:col-span-2' : 'min-w-0'}>
                            <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                                <p className="text-sm text-muted-foreground">
                                    <span className="font-semibold text-foreground">{vagas.total}</span>{' '}
                                    {vagas.total === 1 ? 'vaga encontrada' : 'vagas encontradas'}
                                </p>
                                <Select
                                    value={f.ordenar || 'recentes'}
                                    onValueChange={(v) => {
                                        set('ordenar', v);
                                        aplicar({ ordenar: v });
                                    }}
                                >
                                    <SelectTrigger size="sm" className="w-44">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="recentes">Mais recentes</SelectItem>
                                        <SelectItem value="encerramento">Encerram primeiro</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            {indisponivel ? (
                                /* Lista vazia aqui seria lida como "não há vagas abertas".
                                   Quando a origem não responde, a tela precisa dizer isso. */
                                <div className="rounded-xl bg-card ring-1 ring-foreground/10">
                                    <EmptyState
                                        icon={AlertTriangle}
                                        title="Vagas temporariamente indisponíveis"
                                        description="Não conseguimos consultar as vagas agora. Isso não significa que não há vagas abertas — tente novamente em alguns minutos."
                                    >
                                        <Button variant="outline" onClick={() => router.reload()}>
                                            Tentar novamente
                                        </Button>
                                        <Button asChild variant="ghost">
                                            <Link href={route('alertas.create')}>
                                                <Bell data-icon="inline-start" /> Criar alerta
                                            </Link>
                                        </Button>
                                    </EmptyState>
                                </div>
                            ) : vagas.data.length === 0 ? (
                                <div className="rounded-xl bg-card ring-1 ring-foreground/10">
                                    <EmptyState
                                        title="Nenhuma vaga encontrada"
                                        description="Tente ajustar os filtros ou volte em breve, novas vagas são publicadas com frequência."
                                    >
                                        {temFiltros && (
                                            <Button variant="outline" onClick={limpar}>
                                                Limpar filtros
                                            </Button>
                                        )}
                                        <Button asChild variant="ghost">
                                            <Link href={route('alertas.create')}>
                                                <Bell data-icon="inline-start" /> Criar alerta
                                            </Link>
                                        </Button>
                                    </EmptyState>
                                </div>
                            ) : (
                                /* Itens colados: divide-y no lugar de gap, sem card por vaga */
                                <ul className="divide-y overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
                                    {vagas.data.map((vaga) => (
                                        <li key={vaga.id}>
                                            <VagaListaItem
                                                vaga={vaga}
                                                selecionada={selecionada?.id === vaga.id}
                                                onSelect={selecionarVaga}
                                            />
                                        </li>
                                    ))}
                                </ul>
                            )}

                            <Pagination paginator={vagas} className="mt-6" />
                        </div>

                        {/* Detalhe — terceira coluna a partir de xl */}
                        {selecionada && (
                            <VagaDetalhePainel
                                vaga={selecionada}
                                className="hidden min-w-0 rounded-xl ring-1 ring-foreground/10 xl:sticky xl:top-20 xl:block"
                            />
                        )}
                    </div>
                </div>

                {/* Detalhe — Sheet abaixo de xl, com o mesmo painel */}
                <Sheet open={sheetAberto && !splitView} onOpenChange={setSheetAberto}>
                    <SheetContent side="bottom" showCloseButton={false} className="h-[92dvh] gap-0 p-0">
                        <SheetTitle className="sr-only">{selecionada?.titulo ?? 'Detalhe da vaga'}</SheetTitle>
                        <div className="h-full overflow-y-auto">
                            <VagaDetalhePainel vaga={selecionada} onVoltar={() => setSheetAberto(false)} />
                        </div>
                    </SheetContent>
                </Sheet>
            </section>
        </PublicLayout>
    );
}
