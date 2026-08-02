import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { ArrowLeft, Search } from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import Pagination from '@/components/Pagination';
import { CandidaturasTabela, ContadoresStatus } from '@/components/candidaturas-list';
import { StatusVagaBadge } from '@/components/badges';
import { Input } from '@/components/ui/input';
import { formatDate } from '@/lib/format';

export default function Index({ vaga, candidaturas, contadores, filtros = {} }) {
    const [busca, setBusca] = useState(filtros.busca ?? '');

    function aplicar(extra = {}) {
        const params = { busca, status: filtros.status, ...extra };
        router.get(
            route('coord.candidaturas.index', vaga.id),
            Object.fromEntries(Object.entries(params).filter(([, v]) => v)),
            { preserveState: true },
        );
    }

    return (
        <InternalLayout title={`Candidaturas: ${vaga.titulo}`} pageTitle="Candidaturas da vaga" breadcrumb="Coordenador / Vagas">
            <Link
                href={route('coord.vagas.index')}
                className="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <ArrowLeft className="size-4" /> Minhas vagas
            </Link>

            <div className="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <div className="min-w-0">
                    <h1 className="truncate text-lg font-bold tracking-tight">{vaga.titulo}</h1>
                    <p className="mt-0.5 text-xs text-muted-foreground">
                        Inscrições até {formatDate(vaga.data_encerramento)}
                    </p>
                </div>
                <StatusVagaBadge status={vaga.status} />
            </div>

            <div className="mt-5">
                <ContadoresStatus contadores={contadores} ativo={filtros.status} onSelect={(status) => aplicar({ status })} />
            </div>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    aplicar();
                }}
                className="relative mt-4"
            >
                <Search className="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    value={busca}
                    onChange={(e) => setBusca(e.target.value)}
                    placeholder="Buscar por nome, e-mail, CPF ou curso…"
                    className="bg-card pl-8"
                />
            </form>

            <div className="mt-4 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
                <CandidaturasTabela candidaturas={candidaturas.data} />
            </div>

            <Pagination paginator={candidaturas} className="mt-4" />
        </InternalLayout>
    );
}
