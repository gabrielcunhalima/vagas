import { Link } from '@inertiajs/react';
import { ArrowRight, FileSearch, MapPin } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import CandidaturaTimeline from '@/components/CandidaturaTimeline';
import EmptyState from '@/components/EmptyState';
import { StatusCandidaturaBadge, TipoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { formatDate } from '@/lib/format';

export default function Index({ candidaturas }) {
    return (
        <PublicLayout title="Minhas candidaturas">
            <div className="mx-auto w-full max-w-3xl px-4 pt-10">
                <h1 className="text-2xl font-bold tracking-tight">Minhas candidaturas</h1>
                <p className="mt-1 text-sm text-muted-foreground">
                    Acompanhe aqui o andamento de cada processo seletivo.
                </p>

                {candidaturas.length === 0 ? (
                    <div className="mt-6 rounded-xl bg-card ring-1 ring-foreground/10">
                        <EmptyState
                            icon={FileSearch}
                            title="Você ainda não se candidatou"
                            description="Explore as vagas abertas e envie sua primeira candidatura, leva menos de 5 minutos."
                        >
                            <Button asChild>
                                <Link href={route('vagas.publicas.index')}>
                                    Ver vagas abertas <ArrowRight data-icon="inline-end" />
                                </Link>
                            </Button>
                        </EmptyState>
                    </div>
                ) : (
                    <div className="mt-6 flex flex-col gap-4">
                        {candidaturas.map((c) => (
                            <Link
                                key={c.id}
                                href={route('candidato.candidaturas.show', c.id)}
                                className="group block rounded-xl bg-card p-5 ring-1 ring-foreground/10 transition-all hover:shadow-md hover:ring-primary/40"
                            >
                                <div className="flex flex-wrap items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <div className="flex flex-wrap items-center gap-2">
                                            {c.vaga && <TipoBadge tipo={c.vaga.tipo} />}
                                            <StatusCandidaturaBadge status={c.status} />
                                        </div>
                                        <h2 className="mt-2 font-semibold leading-snug transition-colors group-hover:text-primary">
                                            {c.vaga?.titulo ?? 'Vaga removida'}
                                        </h2>
                                        <div className="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                                            {c.vaga?.cidade && (
                                                <span className="inline-flex items-center gap-1">
                                                    <MapPin className="size-3" />
                                                    {c.vaga.cidade}/{c.vaga.estado}
                                                </span>
                                            )}
                                            <span>Enviada em {formatDate(c.created_at)}</span>
                                        </div>
                                    </div>
                                    <ArrowRight className="mt-1 size-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:text-primary" />
                                </div>
                                <CandidaturaTimeline status={c.status} className="mt-5" />
                            </Link>
                        ))}
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
