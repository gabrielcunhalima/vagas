import { Link } from '@inertiajs/react';
import { ArrowLeft, CalendarClock, Download, ExternalLink, MapPin } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import CandidaturaTimeline from '@/components/CandidaturaTimeline';
import { ModalidadeBadge, StatusCandidaturaBadge, TipoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { formatDate, formatDateTime, formatMoney } from '@/lib/format';

function Info({ label, children }) {
    if (children === null || children === undefined || children === '' || children === 'N/A') return null;
    return (
        <div className="flex flex-col gap-0.5">
            <dt className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">{label}</dt>
            <dd className="text-sm">{children}</dd>
        </div>
    );
}

export default function Show({ candidatura: c }) {
    return (
        <PublicLayout title={`Candidatura: ${c.vaga?.titulo ?? ''}`}>
            <div className="mx-auto w-full max-w-3xl px-4 pt-10">
                <Link
                    href={route('candidato.candidaturas.index')}
                    className="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft className="size-4" /> Minhas candidaturas
                </Link>

                <header className="mt-5">
                    <div className="flex flex-wrap items-center gap-2">
                        {c.vaga && <TipoBadge tipo={c.vaga.tipo} />}
                        {c.vaga && <ModalidadeBadge modalidade={c.vaga.modalidade} />}
                        <StatusCandidaturaBadge status={c.status} />
                    </div>
                    <h1 className="mt-3 text-2xl font-bold leading-tight tracking-tight">
                        {c.vaga?.titulo ?? 'Vaga removida'}
                    </h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Candidatura enviada em {formatDate(c.created_at)}
                        {c.vaga?.status === 'ativa' && (
                            <>
                                {' · '}
                                <Link
                                    href={route('vagas.publicas.show', c.vaga.id)}
                                    className="inline-flex items-center gap-1 font-medium text-primary hover:underline"
                                >
                                    Ver vaga <ExternalLink className="size-3" />
                                </Link>
                            </>
                        )}
                    </p>
                </header>

                <div className="mt-7 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
                    <h2 className="text-sm font-bold">Andamento do processo</h2>
                    <CandidaturaTimeline status={c.status} className="mt-5" />
                </div>

                {c.status === 'entrevista' && c.entrevista_data && (
                    <div className="mt-4 rounded-xl bg-accent/60 p-5 ring-1 ring-primary/20">
                        <h2 className="text-sm font-bold text-accent-foreground">Entrevista agendada</h2>
                        <div className="mt-3 flex flex-col gap-2 text-sm">
                            <span className="inline-flex items-center gap-2">
                                <CalendarClock className="size-4 text-primary" />
                                {formatDateTime(c.entrevista_data)}
                            </span>
                            {c.entrevista_local && (
                                <span className="inline-flex items-center gap-2">
                                    <MapPin className="size-4 text-primary" />
                                    {c.entrevista_local}
                                </span>
                            )}
                            {c.entrevista_observacoes && (
                                <p className="text-muted-foreground">{c.entrevista_observacoes}</p>
                            )}
                        </div>
                    </div>
                )}

                <div className="mt-4 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <h2 className="text-sm font-bold">Dados enviados</h2>
                        {c.tem_curriculo && (
                            <Button asChild variant="outline" size="sm">
                                <a href={route('candidato.candidaturas.curriculo', c.id)}>
                                    <Download data-icon="inline-start" /> Currículo enviado
                                </a>
                            </Button>
                        )}
                    </div>

                    <dl className="mt-5 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                        <Info label="Nome">{c.nome}</Info>
                        <Info label="E-mail">{c.email}</Info>
                        <Info label="CPF">{c.cpf_formatado}</Info>
                        <Info label="Telefone">{c.telefone}</Info>
                        <Info label="Curso">{c.curso}</Info>
                        <Info label="Instituição">{c.instituicao}</Info>
                        <Info label="Semestre">{c.semestre}</Info>
                        <Info label="Previsão de conclusão">{c.previsao_conclusao ? formatDate(c.previsao_conclusao) : null}</Info>
                        <Info label="LinkedIn">{c.linkedin}</Info>
                        <Info label="Pretensão salarial">{formatMoney(c.pretensao_salarial)}</Info>
                        <Info label="Disponibilidade">{c.disponibilidade}</Info>
                        <Info label="PcD">{c.pcd ? (c.pcd_tipo ? `Sim, ${c.pcd_tipo}` : 'Sim') : null}</Info>
                        <Info label="Endereço">{c.endereco_completo || null}</Info>
                    </dl>

                    {c.carta_apresentacao && (
                        <div className="mt-5 border-t pt-5">
                            <h3 className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                Carta de apresentação
                            </h3>
                            <p className="mt-2 whitespace-pre-line text-sm leading-relaxed">{c.carta_apresentacao}</p>
                        </div>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
