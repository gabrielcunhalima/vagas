import { Link } from '@inertiajs/react';
import { Accessibility, ArrowRight, Users } from 'lucide-react';
import EmptyState from '@/components/EmptyState';
import { StatusCandidaturaBadge } from '@/components/badges';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { statusCandidaturaLabel } from '@/lib/enums';
import { formatDate } from '@/lib/format';
import { cn } from '@/lib/utils';

export function ContadoresStatus({ contadores, ativo, onSelect }) {
    const opcoes = [['', `Todas (${contadores.todos})`]].concat(
        Object.entries(statusCandidaturaLabel).map(([v, l]) => [v, `${l} (${contadores[v] ?? 0})`]),
    );

    return (
        <div className="flex flex-wrap gap-1.5">
            {opcoes.map(([valor, label]) => (
                <button
                    key={valor || 'todas'}
                    type="button"
                    onClick={() => onSelect(valor)}
                    className={cn(
                        'cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors',
                        (ativo ?? '') === valor
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-card text-muted-foreground ring-1 ring-foreground/10 hover:text-foreground',
                    )}
                >
                    {label}
                </button>
            ))}
        </div>
    );
}

export function CandidaturasTabela({ candidaturas, mostrarVaga = false }) {
    if (candidaturas.length === 0) {
        return (
            <EmptyState
                icon={Users}
                title="Nenhuma candidatura encontrada"
                description="Ajuste os filtros ou aguarde novas candidaturas."
            />
        );
    }

    return (
        <div className="overflow-x-auto">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Candidato</TableHead>
                        <TableHead>Curso</TableHead>
                        {mostrarVaga && <TableHead>Vaga</TableHead>}
                        <TableHead>Status</TableHead>
                        <TableHead>Recebida em</TableHead>
                        <TableHead className="w-12" />
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {candidaturas.map((c) => (
                        <TableRow key={c.id}>
                            <TableCell className="max-w-56">
                                <Link
                                    href={route('coord.candidaturas.show', [c.vaga_id, c.id])}
                                    className="block truncate font-medium hover:text-primary"
                                >
                                    {c.nome}
                                </Link>
                                <div className="flex items-center gap-1.5">
                                    <span className="truncate text-xs text-muted-foreground">{c.email}</span>
                                    {c.pcd && (
                                        <Badge variant="secondary" className="h-4 gap-0.5 px-1.5 text-[0.6rem]">
                                            <Accessibility className="size-2.5!" /> PcD
                                        </Badge>
                                    )}
                                </div>
                            </TableCell>
                            <TableCell className="max-w-40 truncate text-muted-foreground">{c.curso}</TableCell>
                            {mostrarVaga && (
                                <TableCell className="max-w-48 truncate text-muted-foreground">
                                    {c.vaga?.titulo ?? 'N/A'}
                                </TableCell>
                            )}
                            <TableCell>
                                <StatusCandidaturaBadge status={c.status} />
                            </TableCell>
                            <TableCell className="text-muted-foreground">{formatDate(c.created_at)}</TableCell>
                            <TableCell>
                                <Link
                                    href={route('coord.candidaturas.show', [c.vaga_id, c.id])}
                                    className="inline-flex size-7 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                    aria-label="Ver candidatura"
                                >
                                    <ArrowRight className="size-4" />
                                </Link>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}
