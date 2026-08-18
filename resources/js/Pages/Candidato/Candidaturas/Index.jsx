import { Link } from '@inertiajs/react';
import { AlertTriangle, ArrowRight, Briefcase, FileSearch, MapPin } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import AndamentoInscricao, { AndamentoBadge } from '@/components/AndamentoInscricao';
import EmptyState from '@/components/EmptyState';
import { TipoAdmissaoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { formatDate, localVaga } from '@/lib/format';

/*
 * As inscrições vêm do DRHFlow, chaveadas por CPF + código da vaga — não há id
 * próprio, então o link usa `cd_vaga_emprego`.
 */
export default function Index({ candidaturas, indisponivel = false }) {
    return (
        <PublicLayout title="Minhas candidaturas">
            <div className="mx-auto w-full max-w-3xl px-4 pt-10">
                <h1 className="text-2xl font-bold tracking-tight">Minhas candidaturas</h1>
                <p className="mt-1 text-sm text-muted-foreground">
                    Acompanhe aqui o andamento de cada processo seletivo.
                </p>

                {indisponivel ? (
                    /* Lista vazia aqui seria lida como "não tenho candidaturas". */
                    <div className="mt-6 rounded-xl bg-card ring-1 ring-foreground/10">
                        <EmptyState
                            icon={AlertTriangle}
                            title="Candidaturas temporariamente indisponíveis"
                            description="Não conseguimos consultar suas candidaturas agora. Elas continuam registradas — tente novamente em alguns minutos."
                        />
                    </div>
                ) : candidaturas.length === 0 ? (
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
                                key={c.cd_vaga_emprego}
                                href={route('candidato.candidaturas.show', c.cd_vaga_emprego)}
                                className="group block rounded-xl bg-card p-5 ring-1 ring-foreground/10 transition-all hover:shadow-md hover:ring-primary/40"
                            >
                                <div className="flex flex-wrap items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <div className="flex flex-wrap items-center gap-2">
                                            {c.vaga && (
                                                <TipoAdmissaoBadge
                                                    tipo={c.vaga.tipo}
                                                    codigo={c.vaga.tipo_codigo}
                                                />
                                            )}
                                            <AndamentoBadge
                                                andamento={c.andamento}
                                                rotulo={c.andamento_rotulo}
                                            />
                                        </div>
                                        <h2 className="mt-2 font-semibold leading-snug transition-colors group-hover:text-primary">
                                            {c.vaga?.titulo ?? 'Vaga encerrada'}
                                        </h2>
                                        <div className="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                                            {c.vaga && localVaga(c.vaga) && (
                                                <span className="inline-flex items-center gap-1">
                                                    <MapPin className="size-3" />
                                                    {localVaga(c.vaga)}
                                                </span>
                                            )}
                                            {c.vaga?.projeto_nome && (
                                                <span className="inline-flex items-center gap-1">
                                                    <Briefcase className="size-3" />
                                                    <span className="line-clamp-1">{c.vaga.projeto_nome}</span>
                                                </span>
                                            )}
                                            {c.enviada_em && <span>Enviada em {formatDate(c.enviada_em)}</span>}
                                        </div>
                                    </div>
                                    <ArrowRight className="mt-1 size-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:text-primary" />
                                </div>
                                <AndamentoInscricao andamento={c.andamento} className="mt-5" />
                            </Link>
                        ))}
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
