import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import {
    Bell,
    BellOff,
    Briefcase,
    MoreHorizontal,
    Pencil,
    PlusCircle,
    Power,
    Search,
    SendHorizonal,
    Trash2,
    Users,
} from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import EmptyState from '@/components/EmptyState';
import Pagination from '@/components/Pagination';
import { StatusVagaBadge, TipoBadge } from '@/components/badges';
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { statusVagaLabel, tiposLabel } from '@/lib/enums';
import { formatDate } from '@/lib/format';

const TODOS = '__todos__';

function limparParams(params) {
    return Object.fromEntries(
        Object.entries(params).filter(([, v]) => v !== '' && v !== null && v !== undefined && v !== TODOS),
    );
}

export default function Index({ vagas, areas, filtros = {} }) {
    const [f, setF] = useState({
        busca: filtros.busca ?? '',
        status: filtros.status ?? '',
        tipo: filtros.tipo ?? '',
        area: filtros.area ?? '',
    });
    const [excluir, setExcluir] = useState(null);

    function aplicar(extra = {}) {
        router.get(route('coord.vagas.index'), limparParams({ ...f, ...extra }), { preserveState: true });
    }

    function acao(rota, vagaId) {
        router.patch(route(rota, vagaId), {}, { preserveScroll: true });
    }

    function confirmarExclusao() {
        router.delete(route('coord.vagas.destroy', excluir.id), {
            preserveScroll: true,
            onFinish: () => setExcluir(null),
        });
    }

    const podeEditar = (v) => !['ativa', 'encerrada'].includes(v.status);

    return (
        <InternalLayout
            title="Minhas vagas"
            pageTitle="Minhas vagas"
            breadcrumb="Coordenador"
            topbarActions={
                <Button asChild size="sm">
                    <Link href={route('coord.vagas.create')}>
                        <PlusCircle data-icon="inline-start" /> Nova vaga
                    </Link>
                </Button>
            }
        >
            {/* Filtros */}
            <div className="flex flex-wrap items-center gap-2 rounded-xl bg-card p-3 ring-1 ring-foreground/10">
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        aplicar();
                    }}
                    className="relative min-w-52 flex-1"
                >
                    <Search className="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        value={f.busca}
                        onChange={(e) => setF((p) => ({ ...p, busca: e.target.value }))}
                        placeholder="Buscar por título, descrição, projeto…"
                        className="pl-8"
                    />
                </form>

                <Select
                    value={f.status || TODOS}
                    onValueChange={(v) => {
                        const valor = v === TODOS ? '' : v;
                        setF((p) => ({ ...p, status: valor }));
                        aplicar({ status: valor });
                    }}
                >
                    <SelectTrigger className="w-44">
                        <SelectValue placeholder="Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={TODOS}>Todos os status</SelectItem>
                        {Object.entries(statusVagaLabel).map(([v, l]) => (
                            <SelectItem key={v} value={v}>
                                {l}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>

                <Select
                    value={f.tipo || TODOS}
                    onValueChange={(v) => {
                        const valor = v === TODOS ? '' : v;
                        setF((p) => ({ ...p, tipo: valor }));
                        aplicar({ tipo: valor });
                    }}
                >
                    <SelectTrigger className="w-40">
                        <SelectValue placeholder="Tipo" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={TODOS}>Todos os tipos</SelectItem>
                        {Object.entries(tiposLabel).map(([v, l]) => (
                            <SelectItem key={v} value={v}>
                                {l}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>

                <Select
                    value={f.area || TODOS}
                    onValueChange={(v) => {
                        const valor = v === TODOS ? '' : v;
                        setF((p) => ({ ...p, area: valor }));
                        aplicar({ area: valor });
                    }}
                >
                    <SelectTrigger className="w-48">
                        <SelectValue placeholder="Área" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={TODOS}>Todas as áreas</SelectItem>
                        {areas.map((a) => (
                            <SelectItem key={a} value={a}>
                                {a}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            {/* Tabela */}
            <div className="mt-4 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
                {vagas.data.length === 0 ? (
                    <EmptyState
                        icon={Briefcase}
                        title="Nenhuma vaga encontrada"
                        description="Ajuste os filtros ou crie uma nova vaga."
                    >
                        <Button asChild size="sm">
                            <Link href={route('coord.vagas.create')}>
                                <PlusCircle data-icon="inline-start" /> Nova vaga
                            </Link>
                        </Button>
                    </EmptyState>
                ) : (
                    <div className="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Vaga</TableHead>
                                    <TableHead>Área</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Encerramento</TableHead>
                                    <TableHead className="text-center">Candidaturas</TableHead>
                                    <TableHead className="w-12" />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {vagas.data.map((v) => (
                                    <TableRow key={v.id}>
                                        <TableCell className="max-w-72">
                                            <div className="flex items-center gap-2">
                                                <TipoBadge tipo={v.tipo} />
                                                {podeEditar(v) ? (
                                                    <Link
                                                        href={route('coord.vagas.edit', v.id)}
                                                        className="truncate font-medium hover:text-primary"
                                                    >
                                                        {v.titulo}
                                                    </Link>
                                                ) : (
                                                    <span className="truncate font-medium">{v.titulo}</span>
                                                )}
                                            </div>
                                        </TableCell>
                                        <TableCell className="text-muted-foreground">{v.area}</TableCell>
                                        <TableCell>
                                            <StatusVagaBadge status={v.status} />
                                        </TableCell>
                                        <TableCell className="text-muted-foreground">{formatDate(v.data_encerramento)}</TableCell>
                                        <TableCell className="text-center">
                                            <Link
                                                href={route('coord.candidaturas.index', v.id)}
                                                className="font-semibold text-primary hover:underline"
                                            >
                                                {v.candidaturas_count}
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button variant="ghost" size="icon-sm" aria-label="Ações">
                                                        <MoreHorizontal />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end" className="min-w-52">
                                                    <DropdownMenuItem asChild>
                                                        <Link href={route('coord.candidaturas.index', v.id)}>
                                                            <Users /> Ver candidaturas
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    {podeEditar(v) && (
                                                        <DropdownMenuItem asChild>
                                                            <Link href={route('coord.vagas.edit', v.id)}>
                                                                <Pencil /> Editar
                                                            </Link>
                                                        </DropdownMenuItem>
                                                    )}
                                                    {v.status === 'rascunho' && (
                                                        <DropdownMenuItem onSelect={() => acao('coord.vagas.submeter', v.id)}>
                                                            <SendHorizonal /> Enviar para autorização
                                                        </DropdownMenuItem>
                                                    )}
                                                    {v.status === 'ativa' && (
                                                        <DropdownMenuItem onSelect={() => acao('coord.vagas.desativar', v.id)}>
                                                            <Power /> Desativar
                                                        </DropdownMenuItem>
                                                    )}
                                                    {v.status === 'inativa' && (
                                                        <DropdownMenuItem onSelect={() => acao('coord.vagas.reativar', v.id)}>
                                                            <Power /> Reativar
                                                        </DropdownMenuItem>
                                                    )}
                                                    <DropdownMenuItem onSelect={() => acao('coord.vagas.notificacao', v.id)}>
                                                        {v.notificar_email ? <BellOff /> : <Bell />}
                                                        {v.notificar_email ? 'Silenciar notificações' : 'Ativar notificações'}
                                                    </DropdownMenuItem>
                                                    <DropdownMenuSeparator />
                                                    <DropdownMenuItem variant="destructive" onSelect={() => setExcluir(v)}>
                                                        <Trash2 /> Excluir
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                )}
            </div>

            <Pagination paginator={vagas} className="mt-4" />

            {/* Confirmação de exclusão */}
            <AlertDialog open={Boolean(excluir)} onOpenChange={(open) => !open && setExcluir(null)}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Excluir vaga?</AlertDialogTitle>
                        <AlertDialogDescription>
                            A vaga <span className="font-semibold text-foreground">“{excluir?.titulo}”</span> será
                            removida do portal. Esta ação não pode ser desfeita.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancelar</AlertDialogCancel>
                        <Button variant="destructive" onClick={confirmarExclusao}>
                            Excluir
                        </Button>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </InternalLayout>
    );
}
