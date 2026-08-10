import { Link } from '@inertiajs/react';
import { ArrowRight, CheckCircle2, Clock, ShieldCheck, XCircle } from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import EmptyState from '@/components/EmptyState';
import StatCard from '@/components/StatCard';
import { TipoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatDate } from '@/lib/format';

export default function Dashboard({ stats, vagasPendentes }) {
    return (
        <InternalLayout title="Dashboard" pageTitle="Dashboard" breadcrumb="Gestor">
            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard
                    icon={Clock}
                    label="Aguardando autorização"
                    value={stats.aguardando_aut}
                    tone="amber"
                    href={route('gestor.vagas.index', { status: 'aguardando_autorizacao' })}
                />
                <StatCard icon={CheckCircle2} label="Vagas ativas no portal" value={stats.total_ativas} tone="primary" />
                <StatCard
                    icon={ShieldCheck}
                    label="Autorizadas por mim"
                    value={stats.autorizadas}
                    tone="blue"
                    href={route('gestor.vagas.index', { status: 'ativa' })}
                />
                <StatCard
                    icon={XCircle}
                    label="Recusadas por mim"
                    value={stats.recusadas}
                    tone="red"
                    href={route('gestor.vagas.index', { status: 'recusada' })}
                />
            </div>

            <section className="mt-6 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
                <div className="flex items-center justify-between border-b px-5 py-3.5">
                    <h2 className="text-sm font-bold">Vagas aguardando sua autorização</h2>
                    <Button asChild variant="ghost" size="sm" className="text-muted-foreground">
                        <Link href={route('gestor.vagas.index')}>
                            Ver todas <ArrowRight data-icon="inline-end" />
                        </Link>
                    </Button>
                </div>

                {vagasPendentes.length === 0 ? (
                    <EmptyState
                        icon={ShieldCheck}
                        title="Tudo em dia"
                        description="Nenhuma vaga aguardando autorização no momento."
                    />
                ) : (
                    <div className="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Vaga</TableHead>
                                    <TableHead>Coordenador</TableHead>
                                    <TableHead>Área</TableHead>
                                    <TableHead>Enviada em</TableHead>
                                    <TableHead className="text-right">Ação</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {vagasPendentes.map((v) => (
                                    <TableRow key={v.id}>
                                        <TableCell className="max-w-72">
                                            <div className="flex items-center gap-2">
                                                <TipoBadge tipo={v.tipo} />
                                                <span className="truncate font-medium">{v.titulo}</span>
                                            </div>
                                        </TableCell>
                                        <TableCell className="text-muted-foreground">{v.coordenador?.name ?? 'N/A'}</TableCell>
                                        <TableCell className="text-muted-foreground">{v.area}</TableCell>
                                        <TableCell className="text-muted-foreground">{formatDate(v.created_at)}</TableCell>
                                        <TableCell className="text-right">
                                            <Button asChild size="sm" variant="outline">
                                                <Link href={route('gestor.vagas.show', v.id)}>
                                                    Analisar <ArrowRight data-icon="inline-end" />
                                                </Link>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                )}
            </section>
        </InternalLayout>
    );
}
