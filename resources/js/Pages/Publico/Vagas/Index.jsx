import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Bell, Search, SlidersHorizontal } from 'lucide-react';
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
import { modalidadesLabel, tiposLabel, ufs } from '@/lib/enums';

const TODOS = '__todos__';

/* 10-80-10: 10% de margem de cada lado com 80% de conteúdo, a partir de lg.
   Abaixo disso o percentual daria margens inúteis — vale largura cheia com padding. */
const CONTAINER = 'mx-auto w-full px-4 lg:w-4/5 lg:px-0';

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

export default function Index({ vagas, areas, cursos, total, filtros = {} }) {
    const [f, setF] = useState({
        busca: filtros.busca ?? '',
        area: filtros.area ?? '',
        tipo: filtros.tipo ?? '',
        modalidade: filtros.modalidade ?? '',
        curso: filtros.curso ?? '',
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

                <div className={`${CONTAINER} py-16 lg:py-24`}>
                    <div className="max-w-2xl">
                        <h1 className="text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
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

                        {/* CTA de alertas: superfície própria para não sumir sobre a foto */}
                        <div className="mt-6 flex max-w-xl flex-col gap-4 rounded-xl bg-white/10 p-4 ring-1 ring-white/25 backdrop-blur-sm transition-colors hover:bg-white/15 sm:flex-row sm:items-center sm:gap-5">
                            <div className="flex min-w-0 flex-1 items-start gap-3">
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
                            <Button asChild className="h-11 w-full px-6 sm:w-auto">
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
                                    label="Área"
                                    value={f.area}
                                    onChange={(v) => selecionar('area', v)}
                                    options={areas.map((a) => [a, a])}
                                />
                                <FiltroSelect
                                    label="Tipo"
                                    value={f.tipo}
                                    onChange={(v) => selecionar('tipo', v)}
                                    options={Object.entries(tiposLabel)}
                                    placeholder="Todos"
                                />
                                <FiltroSelect
                                    label="Modalidade"
                                    value={f.modalidade}
                                    onChange={(v) => selecionar('modalidade', v)}
                                    options={Object.entries(modalidadesLabel)}
                                />
                                <FiltroSelect
                                    label="Curso"
                                    value={f.curso}
                                    onChange={(v) => selecionar('curso', v)}
                                    options={cursos.map((c) => [c, c])}
                                    placeholder="Todos"
                                />

                                <div className="flex flex-col gap-1.5">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                        Localização
                                    </Label>
                                    <div className="grid grid-cols-[1fr_76px] gap-2">
                                        <Input
                                            value={f.cidade}
                                            onChange={(e) => set('cidade', e.target.value)}
                                            placeholder="Cidade"
                                        />
                                        <Select value={f.estado || TODOS} onValueChange={(v) => selecionar('estado', v)}>
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="UF" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value={TODOS}>UF</SelectItem>
                                                {ufs.map((uf) => (
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

                            {vagas.data.length === 0 ? (
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
                                className="hidden max-h-[calc(100dvh-6rem)] min-w-0 overflow-y-auto rounded-xl ring-1 ring-foreground/10 xl:sticky xl:top-20 xl:block"
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
