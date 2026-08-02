import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Briefcase,
    CalendarClock,
    CheckCircle2,
    Clock,
    FilePen,
    Inbox,
    Users,
} from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import EmptyState from '@/components/EmptyState';
import StatCard from '@/components/StatCard';
import { StatusCandidaturaBadge, StatusVagaBadge, TipoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatDate } from '@/lib/format';

export default function Dashboard({ stats, vagasRecentes, candidaturasRecentes }) {
    return (
        <InternalLayout title="Dashboard" pageTitle="Dashboard" breadcrumb="Coordenador">
            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard icon={Briefcase} label="Vagas criadas" value={stats.total_vagas} tone="neutral" href={route('coord.vagas.index')} />
                <StatCard icon={CheckCircle2} label="Vagas ativas" value={stats.vagas_ativas} tone="primary" href={route('coord.vagas.index', { status: 'ativa' })} />
                <StatCard icon={Clock} label="Aguardando autorização" value={stats.aguardando_aut} tone="amber" href={route('coord.vagas.index', { status: 'aguardando_autorizacao' })} />
                <StatCard icon={FilePen} label="Rascunhos" value={stats.vagas_rascunho} tone="neutral" href={route('coord.vagas.index', { status: 'rascunho' })} />
                <StatCard icon={Users} label="Candidaturas recebidas" value={stats.total_candidatos} tone="blue" href={route('coord.candidaturas.todas')} />
                <StatCard icon={Inbox} label="Novas (sem análise)" value={stats.candidatos_novos} tone="sky" href={route('coord.candidaturas.todas', { status: 'recebida' })} />
                <StatCard icon={CalendarClock} label="Entrevistas hoje" value={stats.entrevistas_hoje} tone="violet" href={route('coord.candidaturas.todas', { status: 'entrevista' })} />
            </div>

            <div className="mt-6 grid items-start gap-6 xl:grid-cols-[3fr_2fr]">
                {/* Vagas recentes */}
                <section className="overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
                    <div className="flex items-center justify-between border-b px-5 py-3.5">
                        <h2 className="text-sm font-bold">Vagas recentes</h2>
                        <Button asChild variant="ghost" size="sm" className="text-muted-foreground">
                            <Link href={route('coord.vagas.index')}>
                                Ver todas <ArrowRight data-icon="inline-end" />
                            </Link>
                        </Button>
                    </div>

                    {vagasRecentes.length === 0 ? (
                        <EmptyState
                            icon={Briefcase}
                            title="Nenhuma vaga criada"
                            description="Crie sua primeira vaga para começar a receber candidaturas."
                        >
                            <Button asChild size="sm">
                                <Link href={route('coord.vagas.create')}>Nova vaga</Link>
                            </Button>
                        </EmptyState>
                    ) : (
                        <div className="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Vaga</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Encerramento</TableHead>
                                        <TableHead className="text-right">Candidaturas</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {vagasRecentes.map((v) => (
                                        <TableRow key={v.id}>
                                            <TableCell className="max-w-56">
                                                <div className="flex items-center gap-2">
                                                    <TipoBadge tipo={v.tipo} />
                                                    <span className="truncate font-medium">{v.titulo}</span>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <StatusVagaBadge status={v.status} />
                                            </TableCell>
                                            <TableCell className="text-muted-foreground">{formatDate(v.data_encerramento)}</TableCell>
                                            <TableCell className="text-right">
                                                <Link
                                                    href={route('coord.candidaturas.index', v.id)}
                                                    className="font-semibold text-primary hover:underline"
                                                >
                                                    {v.candidaturas_count}
                                                </Link>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    )}
                </section>

                {/* Candidaturas recentes */}
                <section className="overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
                    <div className="flex items-center justify-between border-b px-5 py-3.5">
                        <h2 className="text-sm font-bold">Últimas candidaturas</h2>
                        <Button asChild variant="ghost" size="sm" className="text-muted-foreground">
                            <Link href={route('coord.candidaturas.todas')}>
                                Ver todas <ArrowRight data-icon="inline-end" />
                            </Link>
                        </Button>
                    </div>

                    {candidaturasRecentes.length === 0 ? (
                        <EmptyState
                            icon={Users}
                            title="Nenhuma candidatura ainda"
                            description="Assim que alguém se candidatar às suas vagas, aparecerá aqui."
                        />
                    ) : (
                        <ul className="divide-y">
                            {candidaturasRecentes.map((c) => (
                                <li key={c.id}>
                                    <Link
                                        href={route('coord.candidaturas.show', [c.vaga_id, c.id])}
                                        className="flex items-center justify-between gap-3 px-5 py-3 transition-colors hover:bg-muted/50"
                                    >
                                        <div className="min-w-0">
                                            <div className="truncate text-sm font-medium">{c.nome}</div>
                                            <div className="truncate text-xs text-muted-foreground">
                                                {c.vaga?.titulo} · {formatDate(c.created_at)}
                                            </div>
                                        </div>
                                        <StatusCandidaturaBadge status={c.status} />
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    )}
                </section>
            </div>
        </InternalLayout>
    );
}
