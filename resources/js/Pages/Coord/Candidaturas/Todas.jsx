import { useState } from 'react';
import { router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import Pagination from '@/components/Pagination';
import { CandidaturasTabela, ContadoresStatus } from '@/components/candidaturas-list';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const TODAS = '__todas__';

export default function Todas({ candidaturas, vagas, contadores, filtros = {} }) {
    const [busca, setBusca] = useState(filtros.busca ?? '');

    function aplicar(extra = {}) {
        const params = { busca, status: filtros.status, vaga_id: filtros.vaga_id, ...extra };
        router.get(
            route('coord.candidaturas.todas'),
            Object.fromEntries(Object.entries(params).filter(([, v]) => v)),
            { preserveState: true },
        );
    }

    return (
        <InternalLayout title="Candidaturas" pageTitle="Candidaturas" breadcrumb="Coordenador">
            <ContadoresStatus contadores={contadores} ativo={filtros.status} onSelect={(status) => aplicar({ status })} />

            <div className="mt-4 flex flex-wrap items-center gap-2 rounded-xl bg-card p-3 ring-1 ring-foreground/10">
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        aplicar();
                    }}
                    className="relative min-w-52 flex-1"
                >
                    <Search className="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        value={busca}
                        onChange={(e) => setBusca(e.target.value)}
                        placeholder="Buscar por nome, e-mail, CPF ou curso…"
                        className="pl-8"
                    />
                </form>

                <Select
                    value={filtros.vaga_id ? String(filtros.vaga_id) : TODAS}
                    onValueChange={(v) => aplicar({ vaga_id: v === TODAS ? '' : v })}
                >
                    <SelectTrigger className="w-64">
                        <SelectValue placeholder="Todas as vagas" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={TODAS}>Todas as vagas</SelectItem>
                        {vagas.map((v) => (
                            <SelectItem key={v.id} value={String(v.id)}>
                                {v.titulo}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            <div className="mt-4 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
                <CandidaturasTabela candidaturas={candidaturas.data} mostrarVaga />
            </div>

            <Pagination paginator={candidaturas} className="mt-4" />
        </InternalLayout>
    );
}
