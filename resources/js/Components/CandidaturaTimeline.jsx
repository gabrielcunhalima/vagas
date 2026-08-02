import { Check, X } from 'lucide-react';
import { cn } from '@/lib/utils';

/* Etapas visuais: Recebida → Em análise → Entrevista → Resultado */
const indicePorStatus = {
    recebida: 0,
    em_analise: 1,
    entrevista: 2,
    aprovado: 3,
    reprovado: 3,
};

export default function CandidaturaTimeline({ status, className }) {
    const atual = indicePorStatus[status] ?? 0;
    const reprovado = status === 'reprovado';
    const aprovado = status === 'aprovado';

    const etapas = [
        'Recebida',
        'Em análise',
        'Entrevista',
        reprovado ? 'Não selecionado' : aprovado ? 'Aprovado' : 'Resultado',
    ];

    return (
        <ol className={cn('flex w-full items-start', className)}>
            {etapas.map((etapa, i) => {
                const concluida = i < atual || aprovado;
                const corrente = i === atual && !aprovado;
                const finalNegativo = reprovado && i === 3;

                return (
                    <li key={etapa} className={cn('flex items-start', i > 0 && 'flex-1')}>
                        {i > 0 && (
                            <span
                                className={cn(
                                    'mx-1.5 mt-3 h-px flex-1',
                                    i <= atual ? (finalNegativo && i === 3 ? 'bg-destructive/50' : 'bg-primary') : 'bg-border',
                                )}
                            />
                        )}
                        <span className="flex w-14 flex-col items-center gap-1.5 sm:w-20">
                            <span
                                className={cn(
                                    'flex size-6 items-center justify-center rounded-full text-[0.65rem] font-bold transition-colors',
                                    finalNegativo
                                        ? 'bg-destructive/15 text-destructive'
                                        : concluida
                                          ? 'bg-primary text-primary-foreground'
                                          : corrente
                                            ? 'bg-primary/15 text-primary ring-2 ring-primary/40'
                                            : 'bg-muted text-muted-foreground',
                                )}
                            >
                                {finalNegativo ? (
                                    <X className="size-3.5" />
                                ) : concluida ? (
                                    <Check className="size-3.5" />
                                ) : (
                                    i + 1
                                )}
                            </span>
                            <span
                                className={cn(
                                    'text-center text-[0.65rem] leading-tight sm:text-xs',
                                    finalNegativo
                                        ? 'font-semibold text-destructive'
                                        : corrente || concluida
                                          ? 'font-semibold text-foreground'
                                          : 'text-muted-foreground',
                                )}
                            >
                                {etapa}
                            </span>
                        </span>
                    </li>
                );
            })}
        </ol>
    );
}
