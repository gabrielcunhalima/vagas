import { Link } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, CalendarDays, Clock, MapPin, Wallet } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ModalidadeBadge, NovaBadge, TipoBadge } from '@/components/badges';
import { modalidadesLabel } from '@/lib/enums';
import { diasRestantes, faixaSalarial, formatDate, isNova, localVaga } from '@/lib/format';
import { cn } from '@/lib/utils';

function Fato({ icon: Icon, label, children }) {
    return (
        <div className="flex flex-col gap-0.5">
            <span className="inline-flex items-center gap-1.5 text-xs text-muted-foreground">
                <Icon className="size-3.5 shrink-0" /> {label}
            </span>
            <span className="text-sm font-semibold">{children}</span>
        </div>
    );
}

function Secao({ titulo, children }) {
    return (
        <section>
            <h3 className="text-sm font-bold uppercase tracking-wider text-muted-foreground">{titulo}</h3>
            <div className="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{children}</div>
        </section>
    );
}

/*
 * Detalhe da vaga selecionada na listagem pública.
 * Componente único para as duas apresentações: coluna fixa a partir de xl e
 * conteúdo do Sheet abaixo disso — para não existirem duas versões do detalhe.
 *
 * O cabeçalho é sticky em relação à rolagem da página (na coluna fixa) ou do
 * Sheet (em telas estreitas), então o botão "Candidatar-se" continua alcançável
 * enquanto o painel está em vista, mesmo em vagas de texto longo. `onVoltar`,
 * quando passado, adiciona o retorno à lista nesse mesmo cabeçalho — o botão de
 * fechar padrão do Sheet ficaria coberto pelo cabeçalho assim que o painel rolasse.
 */
export default function VagaDetalhePainel({ vaga, className, onVoltar }) {
    if (!vaga) return null;

    const dias = diasRestantes(vaga.data_encerramento);
    const urgente = dias <= 5;
    const cursos = vaga.curso_desejado ?? [];
    const mostrarEndereco = vaga.modalidade !== 'remoto' && Boolean(vaga.endereco_completo);

    return (
        <article className={cn('bg-card', className)}>
            <div className="sticky top-0 z-[1] border-b bg-card px-5 pb-4 pt-5">
                {onVoltar && (
                    <button
                        type="button"
                        onClick={onVoltar}
                        className="mb-3 inline-flex cursor-pointer items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        <ArrowLeft className="size-4" /> Voltar para a lista
                    </button>
                )}

                <div className="flex flex-wrap items-center gap-2">
                    <TipoBadge tipo={vaga.tipo} />
                    <ModalidadeBadge modalidade={vaga.modalidade} />
                    {isNova(vaga) && <NovaBadge />}
                </div>

                <h2 className="mt-3 text-xl font-bold leading-tight tracking-tight">{vaga.titulo}</h2>

                <div className="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground">
                    <span>{vaga.area}</span>
                    <span aria-hidden>·</span>
                    <span>{localVaga(vaga)}</span>
                    {vaga.projeto_nome && (
                        <>
                            <span aria-hidden>·</span>
                            <span>Projeto — {vaga.projeto_nome}</span>
                        </>
                    )}
                </div>

                {/* px-8 precisa ser repetido no `has-data-[icon=inline-end]:pr-8`: o size="lg"
                    traz um `pr-2` sob esse mesmo modificador e ele vence o `px-8` na cascata,
                    deixando a seta colada na borda direita. */}
                <Button
                    asChild
                    size="lg"
                    className="mt-4 h-11 w-full gap-2 px-8 text-base font-semibold has-data-[icon=inline-end]:pr-8 sm:w-auto sm:min-w-52"
                >
                    <Link href={route('inscricao.create', vaga.id)}>
                        Candidatar-se <ArrowRight data-icon="inline-end" />
                    </Link>
                </Button>
            </div>

            <div className="max-w-4xl px-5 pb-6 pt-5">
                {/* Resumo rápido — as respostas de triagem antes do texto longo */}
                <div className="grid grid-cols-2 gap-x-4 gap-y-3.5 rounded-xl bg-muted/50 p-4 sm:grid-cols-4">
                    <Fato icon={Wallet} label="Remuneração">
                        {faixaSalarial(vaga)}
                    </Fato>
                    {vaga.carga_horaria && (
                        <Fato icon={Clock} label="Carga horária">
                            {vaga.carga_horaria}h/semana
                        </Fato>
                    )}
                    <Fato icon={MapPin} label="Modalidade">
                        {modalidadesLabel[vaga.modalidade] ?? vaga.modalidade}
                    </Fato>
                    <Fato icon={CalendarDays} label="Inscrições até">
                        <span className={cn(urgente && 'text-primary')}>
                            {formatDate(vaga.data_encerramento)}
                        </span>
                    </Fato>
                </div>

                <div className="mt-6 flex flex-col gap-6">
                    {vaga.descricao && <Secao titulo="Sobre a vaga">{vaga.descricao}</Secao>}
                    {vaga.requisitos && <Secao titulo="Requisitos">{vaga.requisitos}</Secao>}
                    {vaga.requisitos_desejaveis && (
                        <Secao titulo="Diferenciais">{vaga.requisitos_desejaveis}</Secao>
                    )}
                    {vaga.beneficios && <Secao titulo="Benefícios">{vaga.beneficios}</Secao>}

                    {cursos.length > 0 && (
                        <section>
                            <h3 className="text-sm font-bold uppercase tracking-wider text-muted-foreground">
                                Cursos desejados
                            </h3>
                            <div className="mt-2.5 flex flex-wrap gap-1.5">
                                {cursos.map((curso) => (
                                    <Badge key={curso} variant="secondary">
                                        {curso}
                                    </Badge>
                                ))}
                            </div>
                        </section>
                    )}

                    {mostrarEndereco && <Secao titulo="Local de trabalho">{vaga.endereco_completo}</Secao>}
                </div>
            </div>
        </article>
    );
}
