import { useState } from 'react';
import { Link, router, useForm, usePage } from '@inertiajs/react';
import {
    Accessibility,
    ArrowLeft,
    CalendarClock,
    CheckCircle2,
    Download,
    ExternalLink,
    FileSearch,
    Loader2,
    Lock,
    Mail,
    MapPin,
    Phone,
    Save,
    UserRound,
    XCircle,
} from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import Field from '@/components/Field';
import { StatusCandidaturaBadge } from '@/components/badges';
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { niveisEscolaridade } from '@/lib/enums';
import { formatDate, formatDateTime, formatMoney } from '@/lib/format';

function Info({ label, children }) {
    if (children === null || children === undefined || children === '' ) return null;
    return (
        <div className="flex flex-col gap-0.5">
            <dt className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">{label}</dt>
            <dd className="text-sm">{children}</dd>
        </div>
    );
}

function TextoLivre({ label, valor }) {
    if (!valor) return null;
    return (
        <div className="flex flex-col gap-0.5 sm:col-span-2">
            <dt className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">{label}</dt>
            <dd className="whitespace-pre-line text-sm">{valor}</dd>
        </div>
    );
}

function Formacoes({ formacoes }) {
    if (!formacoes?.length) return null;
    return (
        <div className="flex flex-col gap-0.5 sm:col-span-2">
            <dt className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Formação</dt>
            <dd className="mt-1 flex flex-col gap-2">
                {formacoes.map((formacao, i) => (
                    <div key={i} className="text-sm">
                        <span className="font-medium">{formacao.curso}</span>
                        {formacao.instituicao && <span className="text-muted-foreground"> — {formacao.instituicao}</span>}
                        <div className="text-xs text-muted-foreground">
                            {[
                                niveisEscolaridade[formacao.nivel_escolaridade] || formacao.nivel_escolaridade,
                                formacao.situacao_curso === 'cursando'
                                    ? `Cursando${formacao.semestre ? ` — ${formacao.semestre}` : ''}`
                                    : formacao.situacao_curso === 'concluido'
                                      ? 'Concluído'
                                      : null,
                                formacao.previsao_conclusao ? formatDate(formacao.previsao_conclusao) : null,
                            ]
                                .filter(Boolean)
                                .join(' · ')}
                        </div>
                    </div>
                ))}
            </dd>
        </div>
    );
}

/*
 * Encerrado o processo, o coordenador deixa de ver os dados atuais do candidato —
 * é o que permite manter o perfil vivo sem que uma vaga de 2024 dê acompanhamento
 * permanente da vida de quem se candidatou. O registro do processo permanece.
 */
function AcessoExpirado({ motivo }) {
    return (
        <section className="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
            <div className="flex items-start gap-3">
                <Lock className="mt-0.5 size-5 shrink-0 text-muted-foreground" />
                <div>
                    <h2 className="text-sm font-bold">Dados pessoais não disponíveis</h2>
                    <p className="mt-1 text-sm text-muted-foreground">{motivo}</p>
                </div>
            </div>
        </section>
    );
}

function HistoricoProcesso({ eventos }) {
    if (!eventos?.length) return null;

    return (
        <section className="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
            <h2 className="text-sm font-bold">Histórico do processo</h2>
            <ol className="mt-4 flex flex-col gap-3">
                {eventos.map((ev, i) => (
                    <li key={i} className="flex gap-3 text-sm">
                        <span className="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary" />
                        <div className="min-w-0">
                            <p className="font-medium">{ev.descricao}</p>
                            <p className="text-xs text-muted-foreground">
                                {formatDate(ev.ocorrido_em)}
                                {ev.autor ? ` · ${ev.autor}` : ''}
                                {ev.curriculo ? ` · currículo: ${ev.curriculo}` : ''}
                            </p>
                        </div>
                    </li>
                ))}
            </ol>
        </section>
    );
}

export default function Show({ vaga, candidatura: c, proximosStatus, acessoExpirado, motivoExpiracao }) {
    const { errors } = usePage().props;
    const [dialogEntrevista, setDialogEntrevista] = useState(false);
    const [entrevista, setEntrevista] = useState({
        entrevista_data: '',
        entrevista_local: '',
        entrevista_observacoes: '',
    });

    const obs = useForm({
        status: c.status,
        observacoes_internas: c.observacoes_internas ?? '',
    });

    function mudarStatus(status, dados = {}, opts = {}) {
        router.patch(
            route('coord.candidaturas.updateStatus', [vaga.id, c.id]),
            { status, ...dados },
            { preserveScroll: true, ...opts },
        );
    }

    function agendarEntrevista(e) {
        e.preventDefault();
        mudarStatus('entrevista', entrevista, {
            onSuccess: () => setDialogEntrevista(false),
        });
    }

    function salvarObs(e) {
        e.preventDefault();
        obs.patch(route('coord.candidaturas.updateStatus', [vaga.id, c.id]), { preserveScroll: true });
    }

    return (
        <InternalLayout title={`Candidatura: ${c.nome ?? 'sem acesso'}`} pageTitle="Candidatura" breadcrumb="Coordenador / Candidaturas">
            <Link
                href={route('coord.candidaturas.index', vaga.id)}
                className="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <ArrowLeft className="size-4" /> Candidaturas de “{vaga.titulo}”
            </Link>

            {/* Cabeçalho */}
            <div className="mt-4 flex flex-wrap items-start justify-between gap-4 rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <div className="flex items-center gap-4">
                    <div className="flex size-12 items-center justify-center rounded-full bg-primary/10">
                        <UserRound className="size-6 text-primary" />
                    </div>
                    <div>
                        <div className="flex flex-wrap items-center gap-2">
                            <h1 className="text-lg font-bold tracking-tight">
                                {acessoExpirado ? 'Candidato não identificado' : c.nome}
                            </h1>
                            <StatusCandidaturaBadge status={c.status} />
                            {c.pcd && (
                                <Badge variant="secondary" className="gap-1">
                                    <Accessibility /> PcD{c.pcd_tipo ? `, ${c.pcd_tipo}` : ''}
                                </Badge>
                            )}
                        </div>
                        <div className="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted-foreground">
                            {!acessoExpirado && (
                                <>
                                    <a href={`mailto:${c.email}`} className="inline-flex items-center gap-1 hover:text-foreground">
                                        <Mail className="size-3" /> {c.email}
                                    </a>
                                    {c.telefone && (
                                        <span className="inline-flex items-center gap-1">
                                            <Phone className="size-3" /> {c.telefone}
                                        </span>
                                    )}
                                </>
                            )}
                            <span>Recebida em {formatDate(c.created_at)}</span>
                            {/* A ficha é viva: sem esta data ela seria tomada por um retrato da inscrição. */}
                            {!acessoExpirado && c.perfil_atualizado_em && (
                                <span>Dados atualizados em {formatDate(c.perfil_atualizado_em)}</span>
                            )}
                        </div>
                    </div>
                </div>

                <div className="flex items-center gap-2">
                    {!acessoExpirado && c.linkedin && (
                        <Button asChild variant="outline" size="sm">
                            <a href={c.linkedin} target="_blank" rel="noreferrer">
                                <ExternalLink data-icon="inline-start" /> LinkedIn
                            </a>
                        </Button>
                    )}
                    {!acessoExpirado && c.tem_curriculo && (
                        <Button asChild size="sm">
                            <a href={route('coord.candidaturas.curriculo', [vaga.id, c.id])}>
                                <Download data-icon="inline-start" /> Currículo
                            </a>
                        </Button>
                    )}
                </div>
            </div>

            <div className="mt-5 grid items-start gap-5 xl:grid-cols-[1fr_340px]">
                {/* Dados */}
                <div className="flex min-w-0 flex-col gap-5">
                    {acessoExpirado ? (
                        <AcessoExpirado motivo={motivoExpiracao} />
                    ) : (
                        <section className="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                            <h2 className="text-sm font-bold">Dados do candidato</h2>
                            <dl className="mt-4 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                                <Info label="CPF">{c.cpf_formatado}</Info>
                                <Info label="Pretensão salarial">{formatMoney(c.pretensao_salarial)}</Info>
                                <Info label="Disponibilidade">{c.disponibilidade}</Info>
                                <Info label="Endereço">{c.endereco_completo || null}</Info>
                                <Formacoes formacoes={c.formacoes} />
                                <TextoLivre label="Outras formações reconhecidas pelo MEC" valor={c.outras_formacoes_mec} />
                                <TextoLivre label="Outros cursos, palestras, etc." valor={c.outros_cursos} />
                            </dl>
                        </section>
                    )}

                    <section className="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                        <h2 className="text-sm font-bold">Respostas desta vaga</h2>
                        <dl className="mt-4 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                            <Info label="Conflito de interesse">{c.conflito_interesse ? 'Sim' : 'Não'}</Info>
                            <Info label="Código de conduta aceito em">
                                {c.codigo_conduta_aceito_em ? formatDate(c.codigo_conduta_aceito_em) : null}
                            </Info>
                            {c.conflito_interesse && (
                                <Info label="Relação declarada">{c.conflito_interesse_detalhe}</Info>
                            )}
                        </dl>

                        {c.carta_apresentacao && (
                            <div className="mt-5 border-t pt-5">
                                <h3 className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    Carta de apresentação
                                </h3>
                                <p className="mt-2 whitespace-pre-line text-sm leading-relaxed">{c.carta_apresentacao}</p>
                            </div>
                        )}
                    </section>

                    <HistoricoProcesso eventos={c.eventos} />

                    {/* Observações internas */}
                    <form onSubmit={salvarObs} className="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                        <h2 className="text-sm font-bold">Observações internas</h2>
                        <p className="mt-0.5 text-xs text-muted-foreground">
                            Visíveis apenas para a equipe. O candidato nunca vê este campo.
                        </p>
                        <Textarea
                            rows={4}
                            className="mt-3"
                            value={obs.data.observacoes_internas}
                            onChange={(e) => obs.setData('observacoes_internas', e.target.value)}
                            placeholder="Anotações sobre o candidato, entrevista, avaliação…"
                        />
                        <Button type="submit" variant="outline" size="sm" className="mt-3" disabled={obs.processing}>
                            {obs.processing ? <Loader2 className="animate-spin" data-icon="inline-start" /> : <Save data-icon="inline-start" />}
                            Salvar observações
                        </Button>
                    </form>
                </div>

                {/* Processo seletivo */}
                <aside className="flex flex-col gap-4 xl:sticky xl:top-20">
                    <div className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                        <h2 className="text-sm font-bold">Processo seletivo</h2>
                        <div className="mt-3 flex items-center justify-between text-sm">
                            <span className="text-muted-foreground">Status atual</span>
                            <StatusCandidaturaBadge status={c.status} />
                        </div>

                        {c.entrevista_data && (
                            <div className="mt-4 rounded-lg bg-accent/60 p-3.5 text-sm ring-1 ring-primary/20">
                                <p className="text-xs font-bold uppercase tracking-wide text-accent-foreground">Entrevista</p>
                                <div className="mt-1.5 flex flex-col gap-1">
                                    <span className="inline-flex items-center gap-2">
                                        <CalendarClock className="size-3.5 text-primary" />
                                        {formatDateTime(c.entrevista_data)}
                                    </span>
                                    {c.entrevista_local && (
                                        <span className="inline-flex items-center gap-2">
                                            <MapPin className="size-3.5 text-primary" />
                                            {c.entrevista_local}
                                        </span>
                                    )}
                                    {c.entrevista_observacoes && (
                                        <p className="text-xs text-muted-foreground">{c.entrevista_observacoes}</p>
                                    )}
                                </div>
                            </div>
                        )}

                        {proximosStatus.length > 0 ? (
                            <div className="mt-4 flex flex-col gap-2">
                                {proximosStatus.includes('em_analise') && (
                                    <Button variant="outline" onClick={() => mudarStatus('em_analise')}>
                                        <FileSearch data-icon="inline-start" /> Mover para análise
                                    </Button>
                                )}

                                {proximosStatus.includes('entrevista') && (
                                    <Dialog open={dialogEntrevista} onOpenChange={setDialogEntrevista}>
                                        <DialogTrigger asChild>
                                            <Button variant="outline">
                                                <CalendarClock data-icon="inline-start" /> Convidar para entrevista
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent>
                                            <form onSubmit={agendarEntrevista}>
                                                <DialogHeader>
                                                    <DialogTitle>Agendar entrevista</DialogTitle>
                                                    <DialogDescription>
                                                        O candidato receberá um convite por e-mail com data e local.
                                                    </DialogDescription>
                                                </DialogHeader>
                                                <div className="mt-4 flex flex-col gap-4">
                                                    <Field label="Data e hora" htmlFor="entrevista_data" required error={errors?.entrevista_data}>
                                                        <Input
                                                            id="entrevista_data"
                                                            type="datetime-local"
                                                            value={entrevista.entrevista_data}
                                                            onChange={(e) =>
                                                                setEntrevista((p) => ({ ...p, entrevista_data: e.target.value }))
                                                            }
                                                            required
                                                        />
                                                    </Field>
                                                    <Field label="Local (ou link da chamada)" htmlFor="entrevista_local" required error={errors?.entrevista_local}>
                                                        <Input
                                                            id="entrevista_local"
                                                            value={entrevista.entrevista_local}
                                                            onChange={(e) =>
                                                                setEntrevista((p) => ({ ...p, entrevista_local: e.target.value }))
                                                            }
                                                            placeholder="Ex.: Sala 12, FAPEU, ou link do Meet"
                                                            required
                                                        />
                                                    </Field>
                                                    <Field label="Observações" htmlFor="entrevista_observacoes" error={errors?.entrevista_observacoes}>
                                                        <Textarea
                                                            id="entrevista_observacoes"
                                                            rows={3}
                                                            value={entrevista.entrevista_observacoes}
                                                            onChange={(e) =>
                                                                setEntrevista((p) => ({ ...p, entrevista_observacoes: e.target.value }))
                                                            }
                                                            placeholder="Instruções extras para o candidato (opcional)"
                                                        />
                                                    </Field>
                                                </div>
                                                <DialogFooter className="mt-5">
                                                    <Button type="button" variant="ghost" onClick={() => setDialogEntrevista(false)}>
                                                        Cancelar
                                                    </Button>
                                                    <Button type="submit">Agendar e notificar</Button>
                                                </DialogFooter>
                                            </form>
                                        </DialogContent>
                                    </Dialog>
                                )}

                                {proximosStatus.includes('aprovado') && (
                                    <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                            <Button>
                                                <CheckCircle2 data-icon="inline-start" /> Aprovar candidato
                                            </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>Aprovar {c.nome?.split(' ')[0] ?? 'este candidato'}?</AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    O candidato será notificado da aprovação por e-mail. Esta decisão é
                                                    final.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>Cancelar</AlertDialogCancel>
                                                <Button onClick={() => mudarStatus('aprovado')}>Aprovar e notificar</Button>
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                )}

                                {proximosStatus.includes('reprovado') && (
                                    <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                            <Button variant="destructive">
                                                <XCircle data-icon="inline-start" /> Reprovar
                                            </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>Reprovar {c.nome?.split(' ')[0] ?? 'este candidato'}?</AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    O candidato será notificado por e-mail de que não seguirá no processo.
                                                    Esta decisão é final.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>Cancelar</AlertDialogCancel>
                                                <Button variant="destructive" onClick={() => mudarStatus('reprovado')}>
                                                    Reprovar e notificar
                                                </Button>
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                )}
                            </div>
                        ) : (
                            <p className="mt-4 text-xs text-muted-foreground">
                                Processo concluído, não há mais transições possíveis.
                            </p>
                        )}
                    </div>
                </aside>
            </div>
        </InternalLayout>
    );
}
