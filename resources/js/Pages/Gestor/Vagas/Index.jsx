import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { ArrowRight, Search, ShieldCheck } from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import EmptyState from '@/components/EmptyState';
import Pagination from '@/components/Pagination';
import { StatusVagaBadge, TipoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatDate } from '@/lib/format';
import { cn } from '@/lib/utils';

const ABAS = [
    ['aguardando_autorizacao', 'Aguardando'],
    ['ativa', 'Autorizadas'],
    ['recusada', 'Recusadas'],
];

export default function Index({ vagas, status, busca: buscaInicial = '' }) {
    const [busca, setBusca] = useState(buscaInicial);

    function aplicar(extra = {}) {
        const params = { status, busca, ...extra };
        router.get(
            route('gestor.vagas.index'),
            Object.fromEntries(Object.entries(params).filter(([, v]) => v)),
            { preserveState: true },
        );
    }

    return (
        <InternalLayout title="Autorizar vagas" pageTitle="Autorizar vagas" breadcrumb="Gestor">
            <div className="flex flex-wrap gap-1.5">
                {ABAS.map(([valor, label]) => (
                    <button
                        key={valor}
                        type="button"
                        onClick={() => aplicar({ status: valor })}
                        className={cn(
                            'cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors',
                            status === valor
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-card text-muted-foreground ring-1 ring-foreground/10 hover:text-foreground',
                        )}
                    >
                        {label}
                    </button>
                ))}
            </div>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    aplicar();
                }}
                className="relative mt-4"
            >
                <Search className="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    value={busca}
                    onChange={(e) => setBusca(e.target.value)}
                    placeholder="Buscar por título, descrição ou projeto…"
                    className="bg-card pl-8"
                />
            </form>

            <div className="mt-4 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
                {vagas.data.length === 0 ? (
                    <EmptyState
                        icon={ShieldCheck}
                        title="Nenhuma vaga aqui"
                        description={
                            status === 'aguardando_autorizacao'
                                ? 'Nenhuma vaga aguardando autorização no momento.'
                                : 'Nenhuma vaga encontrada com este filtro.'
                        }
                    />
                ) : (
                    <div className="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Vaga</TableHead>
                                    <TableHead>Coordenador</TableHead>
                                    <TableHead>Área</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Encerramento</TableHead>
                                    <TableHead className="w-24" />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {vagas.data.map((v) => (
                                    <TableRow key={v.id}>
                                        <TableCell className="max-w-64">
                                            <div className="flex items-center gap-2">
                                                <TipoBadge tipo={v.tipo} />
                                                <Link
                                                    href={route('gestor.vagas.show', v.id)}
                                                    className="truncate font-medium hover:text-primary"
                                                >
                                                    {v.titulo}
                                                </Link>
                                            </div>
                                        </TableCell>
                                        <TableCell className="text-muted-foreground">{v.coordenador?.name ?? 'N/A'}</TableCell>
                                        <TableCell className="text-muted-foreground">{v.area}</TableCell>
                                        <TableCell>
                                            <StatusVagaBadge status={v.status} />
                                        </TableCell>
                                        <TableCell className="text-muted-foreground">{formatDate(v.data_encerramento)}</TableCell>
                                        <TableCell className="text-right">
                                            <Button asChild size="sm" variant="outline">
                                                <Link href={route('gestor.vagas.show', v.id)}>
                                                    {v.status === 'aguardando_autorizacao' ? 'Analisar' : 'Ver'}
                                                    <ArrowRight data-icon="inline-end" />
                                                </Link>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                )}
            </div>

            <Pagination paginator={vagas} className="mt-4" />
        </InternalLayout>
    );
}
