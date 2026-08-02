import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, CheckCircle2, Loader2, ShieldCheck, XCircle } from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import Field from '@/components/Field';
import { ModalidadeBadge, StatusVagaBadge, TipoBadge } from '@/components/badges';
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
import { Textarea } from '@/components/ui/textarea';
import { faixaSalarial, formatDate } from '@/lib/format';

function Secao({ titulo, children }) {
    return (
        <section>
            <h2 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">{titulo}</h2>
            <div className="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{children}</div>
        </section>
    );
}

function Detalhe({ label, children }) {
    if (children === null || children === undefined || children === '') return null;
    return (
        <div className="flex items-start justify-between gap-3 text-sm">
            <span className="shrink-0 text-muted-foreground">{label}</span>
            <span className="text-right font-medium">{children}</span>
        </div>
    );
}

export default function Show({ vaga }) {
    const { errors } = usePage().props;
    const [dialogRecusa, setDialogRecusa] = useState(false);
    const [motivo, setMotivo] = useState('');
    const [processando, setProcessando] = useState(false);

    function autorizar() {
        router.patch(route('gestor.vagas.autorizar', vaga.id), {}, {
            onStart: () => setProcessando(true),
            onFinish: () => setProcessando(false),
        });
    }

    function recusar(e) {
        e.preventDefault();
        router.patch(route('gestor.vagas.recusar', vaga.id), { motivo_recusa: motivo }, {
            onStart: () => setProcessando(true),
            onFinish: () => setProcessando(false),
            onSuccess: () => setDialogRecusa(false),
        });
    }

    return (
        <InternalLayout title={`Vaga: ${vaga.titulo}`} pageTitle="Análise de vaga" breadcrumb="Gestor / Autorizar vagas">
            <Link
                href={route('gestor.vagas.index')}
                className="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <ArrowLeft className="size-4" /> Autorizar vagas
            </Link>

            <header className="mt-4">
                <div className="flex flex-wrap items-center gap-2">
                    <TipoBadge tipo={vaga.tipo} />
                    <ModalidadeBadge modalidade={vaga.modalidade} />
                    <StatusVagaBadge status={vaga.status} />
                </div>
                <h1 className="mt-3 max-w-3xl text-2xl font-bold leading-tight tracking-tight">{vaga.titulo}</h1>
                <p className="mt-1 text-sm text-muted-foreground">
                    {vaga.area} · enviada em {formatDate(vaga.created_at)}
                    {vaga.coordenador && <> · por {vaga.coordenador.name}</>}
                </p>
            </header>

            {vaga.status === 'recusada' && vaga.motivo_recusa && (
                <div className="mt-4 max-w-3xl rounded-xl border border-destructive/30 bg-destructive/5 p-4">
                    <p className="text-sm font-semibold text-destructive">Motivo da recusa</p>
                    <p className="mt-1 text-sm text-muted-foreground">{vaga.motivo_recusa}</p>
                </div>
            )}

            <div className="mt-6 grid items-start gap-6 xl:grid-cols-[1fr_330px]">
                <article className="flex min-w-0 flex-col gap-7 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
                    <Secao titulo="Descrição">{vaga.descricao}</Secao>
                    <Secao titulo="Requisitos">{vaga.requisitos}</Secao>
                    {vaga.requisitos_desejaveis && <Secao titulo="Diferenciais">{vaga.requisitos_desejaveis}</Secao>}
                    {vaga.beneficios && <Secao titulo="Benefícios">{vaga.beneficios}</Secao>}
                    {vaga.curso_desejado?.length > 0 && (
                        <section>
                            <h2 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                Cursos desejados
                            </h2>
                            <div className="mt-2.5 flex flex-wrap gap-1.5">
                                {vaga.curso_desejado.map((curso) => (
                                    <Badge key={curso} variant="secondary">
                                        {curso}
                                    </Badge>
                                ))}
                            </div>
                        </section>
                    )}
                    {vaga.endereco_completo && <Secao titulo="Local de trabalho">{vaga.endereco_completo}</Secao>}
                </article>

                <aside className="flex flex-col gap-4 xl:sticky xl:top-20">
                    <div className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                        <h2 className="text-sm font-bold">Detalhes</h2>
                        <div className="mt-3 flex flex-col gap-2.5">
                            <Detalhe label="Remuneração">{faixaSalarial(vaga)}</Detalhe>
                            <Detalhe label="Carga horária">{vaga.carga_horaria ? `${vaga.carga_horaria}h/sem` : null}</Detalhe>
                            <Detalhe label="Inscrições até">{formatDate(vaga.data_encerramento)}</Detalhe>
                            <Detalhe label="Projeto">{vaga.projeto_nome}</Detalhe>
                            <Detalhe label="Código">{vaga.projeto_codigo}</Detalhe>
                            {vaga.coordenador && (
                                <>
                                    <div className="my-1 border-t" />
                                    <Detalhe label="Coordenador">{vaga.coordenador.name}</Detalhe>
                                    <Detalhe label="E-mail">
                                        <a href={`mailto:${vaga.coordenador.email}`} className="text-primary hover:underline">
                                            {vaga.coordenador.email}
                                        </a>
                                    </Detalhe>
                                </>
                            )}
                        </div>
                    </div>

                    {vaga.status === 'aguardando_autorizacao' && (
                        <div className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                            <h2 className="flex items-center gap-2 text-sm font-bold">
                                <ShieldCheck className="size-4 text-primary" /> Decisão
                            </h2>
                            <p className="mt-1.5 text-xs leading-relaxed text-muted-foreground">
                                Ao autorizar, a vaga é publicada no portal e os alertas de candidatos compatíveis são
                                disparados.
                            </p>
                            <div className="mt-4 flex flex-col gap-2">
                                <AlertDialog>
                                    <AlertDialogTrigger asChild>
                                        <Button disabled={processando}>
                                            {processando ? (
                                                <Loader2 className="animate-spin" data-icon="inline-start" />
                                            ) : (
                                                <CheckCircle2 data-icon="inline-start" />
                                            )}
                                            Autorizar e publicar
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Autorizar esta vaga?</AlertDialogTitle>
                                            <AlertDialogDescription>
                                                “{vaga.titulo}” será publicada imediatamente no portal público e os
                                                candidatos com alertas compatíveis serão notificados por e-mail.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Cancelar</AlertDialogCancel>
                                            <Button onClick={autorizar}>Autorizar</Button>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>

                                <Dialog open={dialogRecusa} onOpenChange={setDialogRecusa}>
                                    <DialogTrigger asChild>
                                        <Button variant="destructive" disabled={processando}>
                                            <XCircle data-icon="inline-start" /> Recusar
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <form onSubmit={recusar}>
                                            <DialogHeader>
                                                <DialogTitle>Recusar vaga</DialogTitle>
                                                <DialogDescription>
                                                    O coordenador será notificado por e-mail com o motivo abaixo e poderá
                                                    ajustar a vaga e reenviá-la.
                                                </DialogDescription>
                                            </DialogHeader>
                                            <div className="mt-4">
                                                <Field label="Motivo da recusa" htmlFor="motivo_recusa" required error={errors?.motivo_recusa}>
                                                    <Textarea
                                                        id="motivo_recusa"
                                                        rows={4}
                                                        value={motivo}
                                                        onChange={(e) => setMotivo(e.target.value)}
                                                        placeholder="Explique o que precisa ser ajustado (mín. 10 caracteres)…"
                                                        required
                                                        minLength={10}
                                                    />
                                                </Field>
                                            </div>
                                            <DialogFooter className="mt-5">
                                                <Button type="button" variant="ghost" onClick={() => setDialogRecusa(false)}>
                                                    Cancelar
                                                </Button>
                                                <Button type="submit" variant="destructive" disabled={processando}>
                                                    {processando && <Loader2 className="animate-spin" data-icon="inline-start" />}
                                                    Recusar vaga
                                                </Button>
                                            </DialogFooter>
                                        </form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </div>
                    )}
                </aside>
            </div>
        </InternalLayout>
    );
}
