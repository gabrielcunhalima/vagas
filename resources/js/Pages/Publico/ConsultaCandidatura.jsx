import { useForm } from '@inertiajs/react';
import { CalendarClock, Inbox, Loader2, MapPin, Search } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import CandidaturaTimeline from '@/components/CandidaturaTimeline';
import EmptyState from '@/components/EmptyState';
import Field from '@/components/Field';
import { StatusCandidaturaBadge, TipoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { maskCpf } from '@/lib/cpf';
import { formatDate, formatDateTime } from '@/lib/format';

export default function ConsultaCandidatura({ candidaturas = null, busca = {} }) {
    const { data, setData, post, processing, errors } = useForm({
        cpf: busca.cpf ? maskCpf(busca.cpf) : '',
        email: busca.email ?? '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('candidatura.consulta.busca'), { preserveScroll: true });
    }

    return (
        <PublicLayout title="Acompanhar candidatura">
            <div className="mx-auto w-full max-w-2xl px-4 pt-10">
                <h1 className="text-2xl font-bold tracking-tight">Acompanhar candidatura</h1>
                <p className="mt-1 text-sm text-muted-foreground">
                    Informe o CPF e o e-mail usados na candidatura para consultar o andamento.
                </p>

                <form
                    onSubmit={submit}
                    className="mt-6 grid gap-4 rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:grid-cols-[1fr_1fr_auto] sm:items-end"
                >
                    <Field label="CPF" htmlFor="cpf" error={errors.cpf}>
                        <Input
                            id="cpf"
                            inputMode="numeric"
                            value={data.cpf}
                            onChange={(e) => setData('cpf', maskCpf(e.target.value))}
                            placeholder="000.000.000-00"
                            required
                        />
                    </Field>
                    <Field label="E-mail" htmlFor="email" error={errors.email}>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="seu@email.com"
                            required
                        />
                    </Field>
                    <Button type="submit" disabled={processing}>
                        {processing ? <Loader2 className="animate-spin" data-icon="inline-start" /> : <Search data-icon="inline-start" />}
                        Consultar
                    </Button>
                </form>

                {candidaturas !== null && (
                    <div className="mt-8">
                        {candidaturas.length === 0 ? (
                            <div className="rounded-xl bg-card ring-1 ring-foreground/10">
                                <EmptyState
                                    icon={Inbox}
                                    title="Nenhuma candidatura encontrada"
                                    description="Confira se o CPF e o e-mail são os mesmos informados na candidatura."
                                />
                            </div>
                        ) : (
                            <>
                                <h2 className="text-sm font-bold uppercase tracking-wider text-muted-foreground">
                                    {candidaturas.length}{' '}
                                    {candidaturas.length === 1 ? 'candidatura encontrada' : 'candidaturas encontradas'}
                                </h2>
                                <div className="mt-3 flex flex-col gap-4">
                                    {candidaturas.map((c) => (
                                        <article key={c.id} className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                                            <div className="flex flex-wrap items-start justify-between gap-3">
                                                <div>
                                                    <div className="flex flex-wrap items-center gap-2">
                                                        {c.vaga && <TipoBadge tipo={c.vaga.tipo} />}
                                                        <StatusCandidaturaBadge status={c.status} />
                                                    </div>
                                                    <h3 className="mt-2 font-semibold leading-snug">
                                                        {c.vaga?.titulo ?? 'Vaga removida'}
                                                    </h3>
                                                    <p className="mt-0.5 text-xs text-muted-foreground">
                                                        Enviada em {formatDate(c.created_at)}
                                                    </p>
                                                </div>
                                            </div>

                                            <CandidaturaTimeline status={c.status} className="mt-5" />

                                            {c.status === 'entrevista' && c.entrevista_data && (
                                                <div className="mt-5 rounded-lg bg-accent/60 p-4 text-sm ring-1 ring-primary/20">
                                                    <p className="font-semibold text-accent-foreground">Entrevista agendada</p>
                                                    <div className="mt-2 flex flex-col gap-1.5 text-foreground">
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
                                        </article>
                                    ))}
                                </div>
                            </>
                        )}
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
