import { Link } from '@inertiajs/react';
import { ArrowLeft, CalendarClock, Clock, Download, ExternalLink, MapPin } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import AndamentoInscricao, { AndamentoBadge } from '@/components/AndamentoInscricao';
import { TipoAdmissaoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { formatDate, localVaga } from '@/lib/format';

function Info({ label, children }) {
    if (children === null || children === undefined || children === '' || children === 'N/A') return null;
    return (
        <div className="flex flex-col gap-0.5">
            <dt className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">{label}</dt>
            <dd className="text-sm">{children}</dd>
        </div>
    );
}

/*
 * Uma inscrição, reunindo as duas origens em uma tela só: o andamento vem do
 * DRHFlow, os dados próprios (carta, conflito de interesse, currículo enviado)
 * vêm do portal.
 *
 * Os dados pessoais não são repetidos aqui: eles vivem no perfil, que é fonte
 * única e viva. Reapresentá-los seria mostrar uma cópia que pode ter divergido.
 */
export default function Show({ candidatura: c }) {
    const vaga = c.vaga;

    return (
        <PublicLayout title={`Candidatura: ${vaga?.titulo ?? ''}`}>
            <div className="mx-auto w-full max-w-3xl px-4 pt-10">
                <Link
                    href={route('candidato.candidaturas.index')}
                    className="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft className="size-4" /> Minhas candidaturas
                </Link>

                <header className="mt-5">
                    <div className="flex flex-wrap items-center gap-2">
                        {vaga && <TipoAdmissaoBadge tipo={vaga.tipo} codigo={vaga.tipo_codigo} />}
                        <AndamentoBadge andamento={c.andamento} rotulo={c.andamento_rotulo} />
                    </div>
                    <h1 className="mt-3 text-2xl font-bold leading-tight tracking-tight">
                        {vaga?.titulo ?? 'Vaga encerrada'}
                    </h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        {c.enviada_em && <>Candidatura enviada em {formatDate(c.enviada_em)}</>}
                        {vaga && (
                            <>
                                {c.enviada_em && ' · '}
                                <Link
                                    href={route('vagas.publicas.show', vaga.id)}
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
                    <AndamentoInscricao andamento={c.andamento} className="mt-5" />
                    {c.andamento === 'avaliacao_concluida' && (
                        <p className="mt-5 text-sm text-muted-foreground">
                            Sua avaliação foi concluída. O RH entrará em contato com o resultado do processo
                            seletivo.
                        </p>
                    )}
                </div>

                {/* Aparece assim que o RH preenche a entrevista no DRHFlow, sem
                    nenhuma ação do portal. */}
                {c.entrevista_data && (
                    <div className="mt-4 rounded-xl bg-accent/60 p-5 ring-1 ring-primary/20">
                        <h2 className="text-sm font-bold text-accent-foreground">Entrevista agendada</h2>
                        <div className="mt-3 flex flex-col gap-2 text-sm">
                            <span className="inline-flex items-center gap-2">
                                <CalendarClock className="size-4 text-primary" />
                                {formatDate(c.entrevista_data)}
                            </span>
                            {c.entrevista_hora && (
                                <span className="inline-flex items-center gap-2">
                                    <Clock className="size-4 text-primary" />
                                    {c.entrevista_hora}
                                </span>
                            )}
                            {c.entrevista_local && (
                                <span className="inline-flex items-center gap-2">
                                    <MapPin className="size-4 text-primary" />
                                    {c.entrevista_local}
                                </span>
                            )}
                        </div>
                    </div>
                )}

                <div className="mt-4 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <h2 className="text-sm font-bold">Envio</h2>
                        {c.tem_curriculo && (
                            <Button asChild variant="outline" size="sm">
                                <a href={route('candidato.candidaturas.curriculo', c.cd_vaga_emprego)}>
                                    <Download data-icon="inline-start" /> Currículo enviado
                                </a>
                            </Button>
                        )}
                    </div>

                    <dl className="mt-5 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                        {vaga && <Info label="Local">{localVaga(vaga)}</Info>}
                        {vaga?.projeto_nome && <Info label="Projeto">{vaga.projeto_nome}</Info>}
                        <Info label="Currículo enviado">{c.curriculo_nome}</Info>
                        <Info label="Conflito de interesse">
                            {c.conflito_interesse === true
                                ? c.conflito_interesse_detalhe || 'Declarado'
                                : c.conflito_interesse === false
                                  ? 'Não declarado'
                                  : null}
                        </Info>
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

                <p className="mt-4 text-center text-xs text-muted-foreground">
                    Seus dados pessoais ficam no seu{' '}
                    <Link href={route('candidato.perfil.edit')} className="font-medium text-primary hover:underline">
                        perfil
                    </Link>
                    . O que você atualizar lá vale para os processos em andamento.
                </p>
            </div>
        </PublicLayout>
    );
}
