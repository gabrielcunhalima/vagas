import { Link } from '@inertiajs/react';
import { ArrowRight, Building2, CalendarDays, Clock, Laptop, MapPin, Shuffle, Wallet } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { NovaBadge, TipoBadge } from '@/components/badges';
import { modalidadesLabel } from '@/lib/enums';
import { diasRestantes, faixaSalarial, isNova, localVaga, prazoInscricao } from '@/lib/format';
import { cn } from '@/lib/utils';

/*
 * Identidade cromática por tipo — sem ícone e sem filete/borda colorida (regras do dono do produto):
 * estágio → violeta · CLT → azul · bolsa → esmeralda (mesmo mapa dos badges, DESIGN_SYSTEM.md §5).
 * A cor aparece no gradiente-wash e nos estados de hover; o card flutua com sombra suave.
 */
const tipoVisual = {
    estagio: {
        wash: 'from-violet-500/10 dark:from-violet-400/10',
        ringHover: 'hover:ring-violet-500/40 dark:hover:ring-violet-400/35',
        shadowHover: 'hover:shadow-violet-500/15 dark:hover:shadow-violet-400/20',
        tituloHover: 'group-hover:text-violet-700 dark:group-hover:text-violet-300',
    },
    emprego: {
        wash: 'from-blue-500/10 dark:from-blue-400/10',
        ringHover: 'hover:ring-blue-500/40 dark:hover:ring-blue-400/35',
        shadowHover: 'hover:shadow-blue-500/15 dark:hover:shadow-blue-400/20',
        tituloHover: 'group-hover:text-blue-700 dark:group-hover:text-blue-300',
    },
    bolsa: {
        wash: 'from-emerald-500/10 dark:from-emerald-400/10',
        ringHover: 'hover:ring-emerald-500/40 dark:hover:ring-emerald-400/35',
        shadowHover: 'hover:shadow-emerald-500/15 dark:hover:shadow-emerald-400/20',
        tituloHover: 'group-hover:text-emerald-700 dark:group-hover:text-emerald-300',
    },
};

const modalidadeIcone = {
    presencial: Building2,
    remoto: Laptop,
    hibrido: Shuffle,
};

function Chip({ icon: Icon, accent = false, children }) {
    return (
        <span
            className={cn(
                'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium',
                accent ? 'bg-primary/10 font-semibold text-primary dark:bg-primary/15' : 'bg-muted text-muted-foreground',
            )}
        >
            <Icon className="size-3.5 shrink-0" />
            {children}
        </span>
    );
}

export default function VagaCard({ vaga }) {
    const dias = diasRestantes(vaga.data_encerramento);
    const urgente = dias <= 5;
    const visual = tipoVisual[vaga.tipo] ?? tipoVisual.estagio;
    const ModalidadeIcon = modalidadeIcone[vaga.modalidade] ?? Building2;

    return (
        <article
            className={cn(
                'group relative overflow-hidden rounded-xl bg-card/55 shadow-lg shadow-black/[0.07] backdrop-blur-xl backdrop-saturate-150 transition-shadow duration-200 hover:shadow-xl dark:bg-card/35 dark:shadow-black/45',
                visual.ringHover,
                visual.shadowHover,
            )}
        >
            {/* Wash de gradiente na cor do tipo, dissolvendo antes do meio do card */}
            <div
                aria-hidden
                className={cn('pointer-events-none absolute inset-0 bg-gradient-to-br via-transparent to-transparent', visual.wash)}
            />

            {/* Realce de vidro: borda de luz superior, simula pouca luz atravessando o topo */}
            <div
                aria-hidden
                className="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/70 to-transparent dark:via-white/20"
            />

            {/* Card inteiro clicável (stretched link); ações reais ficam acima com z-index */}
            <Link
                href={route('vagas.publicas.show', vaga.id)}
                className="absolute inset-0 z-[1]"
                aria-label={`Ver vaga: ${vaga.titulo}`}
            />

            <div className="relative p-5">
                <div className="flex flex-wrap items-center gap-x-2 gap-y-1.5">
                    <TipoBadge tipo={vaga.tipo} />
                    {isNova(vaga) && <NovaBadge />}
                    <span className="text-xs font-medium uppercase tracking-wide text-muted-foreground">{vaga.area}</span>
                </div>

                <h3 className={cn('mt-3 text-lg font-semibold leading-snug tracking-tight transition-colors', visual.tituloHover)}>
                    {vaga.titulo}
                </h3>

                <p className="mt-2 line-clamp-2 text-sm leading-relaxed text-muted-foreground">{vaga.descricao}</p>

                <div className="mt-4 flex flex-wrap items-center gap-1.5">
                    <Chip icon={Wallet} accent>
                        {faixaSalarial(vaga)}
                    </Chip>
                    <Chip icon={MapPin}>{localVaga(vaga)}</Chip>
                    {vaga.carga_horaria && <Chip icon={Clock}>{vaga.carga_horaria}h/sem</Chip>}
                    <Chip icon={ModalidadeIcon}>{modalidadesLabel[vaga.modalidade] ?? vaga.modalidade}</Chip>
                </div>

                <div className="mt-4 flex items-center justify-between gap-3 border-t pt-4">
                    <span
                        className={cn(
                            'inline-flex items-center gap-1.5 text-xs',
                            urgente ? 'font-bold text-primary' : 'text-muted-foreground',
                        )}
                    >
                        <CalendarDays className="size-3.5" />
                        {prazoInscricao(vaga.data_encerramento)}
                    </span>

                    <Button asChild size="sm" className="relative z-[2]">
                        <Link href={route('inscricao.create', vaga.id)}>
                            Candidatar-se <ArrowRight data-icon="inline-end" />
                        </Link>
                    </Button>
                </div>
            </div>
        </article>
    );
}
