import { Sparkles } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import {
    modalidadesLabel,
    statusCandidaturaLabel,
    statusVagaLabel,
    tiposLabel,
} from '@/lib/enums';

const tipoClasses = {
    estagio: 'bg-violet-500/12 text-violet-700 dark:bg-violet-400/10 dark:text-violet-300',
    emprego: 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
    bolsa: 'bg-emerald-500/12 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
};

const statusVagaClasses = {
    rascunho: 'bg-muted text-muted-foreground',
    aguardando_autorizacao: 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
    ativa: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
    encerrada: 'bg-slate-500/15 text-slate-700 dark:bg-slate-400/10 dark:text-slate-300',
    recusada: 'bg-red-500/12 text-red-700 dark:bg-red-400/10 dark:text-red-300',
    inativa: 'bg-muted text-muted-foreground',
};

const statusCandidaturaClasses = {
    recebida: 'bg-sky-500/15 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300',
    em_analise: 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
    entrevista: 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
    aprovado: 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
    reprovado: 'bg-red-500/12 text-red-700 dark:bg-red-400/10 dark:text-red-300',
};

export function TipoBadge({ tipo }) {
    return <Badge className={tipoClasses[tipo]}>{tiposLabel[tipo] ?? tipo}</Badge>;
}

export function ModalidadeBadge({ modalidade }) {
    return <Badge variant="secondary">{modalidadesLabel[modalidade] ?? modalidade}</Badge>;
}

export function NovaBadge() {
    return (
        <Badge className="gap-1 bg-primary text-primary-foreground">
            <Sparkles /> Nova
        </Badge>
    );
}

export function StatusVagaBadge({ status }) {
    return (
        <Badge className={statusVagaClasses[status] ?? 'bg-muted text-muted-foreground'}>
            {statusVagaLabel[status] ?? status}
        </Badge>
    );
}

export function StatusCandidaturaBadge({ status }) {
    return (
        <Badge className={statusCandidaturaClasses[status] ?? 'bg-muted text-muted-foreground'}>
            {statusCandidaturaLabel[status] ?? status}
        </Badge>
    );
}
