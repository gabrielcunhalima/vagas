import { Check } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

/*
 * Andamento de uma inscrição no DRHFlow.
 *
 * São três etapas, e só três: inscrição recebida, entrevista marcada, avaliação
 * concluída. Cada uma corresponde a uma coluna que o RH preenche em
 * EN_CANDIDATO_VAGA_EMPREGO.
 *
 * Não existe etapa de aprovação ou reprovação. Nenhuma coluna documentada
 * determina o desfecho — `FG_SEL` existe mas ninguém sabe dizer o que significa,
 * e adivinhar significaria dizer a alguém que foi reprovado. Enquanto a origem
 * não expuser esse campo, a última etapa é "avaliação concluída".
 *
 * O CandidaturaTimeline segue existindo para o coordenador, sobre as vagas do
 * caminho legado, que têm status próprio.
 */
const ETAPAS = [
    { chave: 'recebida', rotulo: 'Recebida' },
    { chave: 'entrevista_marcada', rotulo: 'Entrevista' },
    { chave: 'avaliacao_concluida', rotulo: 'Avaliação' },
];

const CLASSES_BADGE = {
    recebida: 'bg-sky-500/15 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300',
    entrevista_marcada: 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
    avaliacao_concluida: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
};

export function AndamentoBadge({ andamento, rotulo }) {
    return (
        <Badge className={CLASSES_BADGE[andamento] ?? 'bg-muted text-muted-foreground'}>
            {rotulo ?? andamento}
        </Badge>
    );
}

export default function AndamentoInscricao({ andamento, className }) {
    const atual = Math.max(
        0,
        ETAPAS.findIndex((e) => e.chave === andamento),
    );

    return (
        <ol className={cn('flex w-full items-start', className)}>
            {ETAPAS.map((etapa, i) => {
                const concluida = i < atual;
                const corrente = i === atual;

                return (
                    <li key={etapa.chave} className={cn('flex items-start', i > 0 && 'flex-1')}>
                        {i > 0 && (
                            <span
                                className={cn('mx-1.5 mt-3 h-px flex-1', i <= atual ? 'bg-primary' : 'bg-border')}
                            />
                        )}
                        <span className="flex w-16 flex-col items-center gap-1.5 sm:w-24">
                            <span
                                className={cn(
                                    'flex size-6 items-center justify-center rounded-full text-[0.775rem] font-bold transition-colors',
                                    concluida
                                        ? 'bg-primary text-primary-foreground'
                                        : corrente
                                          ? 'bg-primary/15 text-primary ring-2 ring-primary/40'
                                          : 'bg-muted text-muted-foreground',
                                )}
                            >
                                {concluida ? <Check className="size-3.5" /> : i + 1}
                            </span>
                            <span
                                className={cn(
                                    'text-center text-[0.775rem] leading-tight sm:text-xs',
                                    corrente || concluida
                                        ? 'font-semibold text-foreground'
                                        : 'text-muted-foreground',
                                )}
                            >
                                {etapa.rotulo}
                            </span>
                        </span>
                    </li>
                );
            })}
        </ol>
    );
}
