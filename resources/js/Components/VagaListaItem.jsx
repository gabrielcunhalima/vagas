import { Briefcase, CalendarDays, MapPin } from 'lucide-react';
import { diasRestantes, localVaga, prazoInscricao } from '@/lib/format';
import { cn } from '@/lib/utils';

/*
 * Item da lista compacta da listagem pública (split view).
 * Mostra só o que serve para varrer a lista — cargo, localização, projeto e prazo;
 * todo o resto é responsabilidade do VagaDetalhePainel.
 *
 * É um <button>, não um <Link>: clicar seleciona a vaga, nunca navega.
 * Seleção é marcada por fundo + cor do título — sem filete lateral (DESIGN_SYSTEM §9.10)
 * e sem translate no hover (§9.8).
 */
export default function VagaListaItem({ vaga, selecionada, onSelect }) {
    const dias = diasRestantes(vaga.data_encerramento);
    const urgente = dias <= 5;

    return (
        <button
            type="button"
            onClick={() => onSelect(vaga.id)}
            aria-current={selecionada ? 'true' : undefined}
            className={cn(
                'w-full cursor-pointer px-4 py-3.5 text-left transition-colors outline-none',
                /* ring interno: o container da lista tem overflow-hidden e recortaria um ring externo */
                'focus-visible:inset-ring-2 focus-visible:inset-ring-ring',
                selecionada ? 'bg-accent' : 'hover:bg-muted/60',
            )}
        >
            <h3
                className={cn(
                    'text-sm font-semibold leading-snug tracking-tight',
                    selecionada && 'text-primary',
                )}
            >
                {vaga.titulo}
            </h3>

            <div className="mt-1.5 flex flex-col gap-1 text-xs text-muted-foreground">
                <span className="inline-flex items-center gap-1.5">
                    <MapPin className="size-3.5 shrink-0" />
                    {localVaga(vaga)}
                </span>

                {vaga.projeto_nome && (
                    <span className="inline-flex items-center gap-1.5">
                        <Briefcase className="size-3.5 shrink-0" />
                        <span className="line-clamp-1">{vaga.projeto_nome}</span>
                    </span>
                )}

                <span
                    className={cn(
                        'inline-flex items-center gap-1.5',
                        urgente && 'font-semibold text-primary',
                    )}
                >
                    <CalendarDays className="size-3.5 shrink-0" />
                    {prazoInscricao(vaga.data_encerramento)}
                </span>
            </div>
        </button>
    );
}
