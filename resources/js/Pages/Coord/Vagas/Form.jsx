import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Check, ChevronsUpDown, Loader2, Save, SendHorizonal, X } from 'lucide-react';
import InternalLayout from '@/Layouts/InternalLayout';
import Field from '@/components/Field';
import { StatusVagaBadge } from '@/components/badges';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { maskCep, onlyDigits } from '@/lib/cpf';
import { modalidadesLabel, tiposLabel, ufs } from '@/lib/enums';
import { toDateInput } from '@/lib/format';
import { cn } from '@/lib/utils';

function Secao({ titulo, descricao, children }) {
    return (
        <section className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
            <h2 className="text-sm font-bold tracking-tight">{titulo}</h2>
            {descricao && <p className="mt-0.5 text-xs text-muted-foreground">{descricao}</p>}
            <div className="mt-4">{children}</div>
        </section>
    );
}

function CursosMultiSelect({ cursos, value, onChange }) {
    const [open, setOpen] = useState(false);

    function toggle(curso) {
        onChange(value.includes(curso) ? value.filter((c) => c !== curso) : [...value, curso]);
    }

    return (
        <div className="flex flex-col gap-2">
            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <Button type="button" variant="outline" className="w-full justify-between font-normal">
                        {value.length > 0
                            ? `${value.length} ${value.length === 1 ? 'curso selecionado' : 'cursos selecionados'}`
                            : 'Qualquer curso'}
                        <ChevronsUpDown className="size-3.5 text-muted-foreground" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent className="w-72 p-0" align="start">
                    <Command>
                        <CommandInput placeholder="Buscar curso…" />
                        <CommandList>
                            <CommandEmpty>Nenhum curso encontrado.</CommandEmpty>
                            <CommandGroup>
                                {cursos.map((curso) => (
                                    <CommandItem key={curso} value={curso} onSelect={() => toggle(curso)}>
                                        <Check className={cn('size-4', value.includes(curso) ? 'opacity-100' : 'opacity-0')} />
                                        {curso}
                                    </CommandItem>
                                ))}
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>

            {value.length > 0 && (
                <div className="flex flex-wrap gap-1.5">
                    {value.map((curso) => (
                        <Badge key={curso} variant="secondary" className="gap-1 pr-1">
                            {curso}
                            <button
                                type="button"
                                onClick={() => toggle(curso)}
                                className="cursor-pointer rounded-full p-0.5 hover:bg-foreground/10"
                                aria-label={`Remover ${curso}`}
                            >
                                <X className="size-3" />
                            </button>
                        </Badge>
                    ))}
                </div>
            )}
        </div>
    );
}

export default function Form({ vaga, areas, cursos }) {
    const editando = Boolean(vaga);

    const { data, setData, post, put, processing, errors, transform } = useForm({
        titulo: vaga?.titulo ?? '',
        descricao: vaga?.descricao ?? '',
        requisitos: vaga?.requisitos ?? '',
        requisitos_desejaveis: vaga?.requisitos_desejaveis ?? '',
        beneficios: vaga?.beneficios ?? '',
        tipo: vaga?.tipo ?? '',
        area: vaga?.area ?? '',
        curso_desejado: vaga?.curso_desejado ?? [],
        remuneracao: vaga?.remuneracao ?? '',
        remuneracao_max: vaga?.remuneracao_max ?? '',
        carga_horaria: vaga?.carga_horaria ?? '',
        modalidade: vaga?.modalidade ?? 'presencial',
        cep: vaga?.cep ? maskCep(vaga.cep) : '',
        logradouro: vaga?.logradouro ?? '',
        numero: vaga?.numero ?? '',
        complemento: vaga?.complemento ?? '',
        bairro: vaga?.bairro ?? '',
        cidade: vaga?.cidade ?? '',
        estado: vaga?.estado ?? '',
        local_trabalho: vaga?.local_trabalho ?? '',
        data_encerramento: toDateInput(vaga?.data_encerramento),
        notificar_email: vaga?.notificar_email ?? true,
        projeto_nome: vaga?.projeto_nome ?? '',
        projeto_codigo: vaga?.projeto_codigo ?? '',
    });

    const [buscandoCep, setBuscandoCep] = useState(false);

    function salvar(acao) {
        transform((d) => ({ ...d, acao }));
        if (editando) {
            put(route('coord.vagas.update', vaga.id), { preserveScroll: true });
        } else {
            post(route('coord.vagas.store'), { preserveScroll: true });
        }
    }

    async function buscarCep() {
        const digits = onlyDigits(data.cep);
        if (digits.length !== 8) return;
        setBuscandoCep(true);
        try {
            const res = await fetch(route('api.cep', { cep: digits }));
            if (res.ok) {
                const d = await res.json();
                if (d && !d.erro) {
                    setData((prev) => ({
                        ...prev,
                        logradouro: d.logradouro || prev.logradouro,
                        bairro: d.bairro || prev.bairro,
                        cidade: d.cidade || prev.cidade,
                        estado: d.estado || prev.estado,
                    }));
                }
            }
        } catch {
            /* preenchimento manual segue disponível */
        } finally {
            setBuscandoCep(false);
        }
    }

    return (
        <InternalLayout
            title={editando ? 'Editar vaga' : 'Nova vaga'}
            pageTitle={editando ? 'Editar vaga' : 'Nova vaga'}
            breadcrumb="Coordenador / Vagas"
        >
            <Link
                href={route('coord.vagas.index')}
                className="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <ArrowLeft className="size-4" /> Minhas vagas
            </Link>

            {editando && vaga.status === 'recusada' && vaga.motivo_recusa && (
                <div className="mt-4 rounded-xl border border-destructive/30 bg-destructive/5 p-4">
                    <p className="text-sm font-semibold text-destructive">Vaga recusada pelo gestor</p>
                    <p className="mt-1 text-sm text-muted-foreground">{vaga.motivo_recusa}</p>
                    <p className="mt-1.5 text-xs text-muted-foreground">
                        Ajuste a vaga e envie novamente para autorização.
                    </p>
                </div>
            )}

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    salvar('publicar');
                }}
                className="mt-4 grid items-start gap-5 xl:grid-cols-[1fr_320px]"
            >
                <div className="flex min-w-0 flex-col gap-5">
                    <Secao titulo="Identificação">
                        <div className="flex flex-col gap-4">
                            <Field label="Título da vaga" htmlFor="titulo" required error={errors.titulo}>
                                <Input
                                    id="titulo"
                                    value={data.titulo}
                                    onChange={(e) => setData('titulo', e.target.value)}
                                    placeholder="Ex.: Estágio em Administração, Projeto X"
                                    autoFocus={!editando}
                                />
                            </Field>
                            <Field label="Descrição" htmlFor="descricao" required error={errors.descricao}>
                                <Textarea
                                    id="descricao"
                                    rows={5}
                                    value={data.descricao}
                                    onChange={(e) => setData('descricao', e.target.value)}
                                    placeholder="Atividades, contexto do projeto, o que a pessoa vai fazer…"
                                />
                            </Field>
                            <Field label="Requisitos" htmlFor="requisitos" required error={errors.requisitos}>
                                <Textarea
                                    id="requisitos"
                                    rows={4}
                                    value={data.requisitos}
                                    onChange={(e) => setData('requisitos', e.target.value)}
                                    placeholder="Um requisito por linha…"
                                />
                            </Field>
                            <Field
                                label="Diferenciais (desejáveis)"
                                htmlFor="requisitos_desejaveis"
                                error={errors.requisitos_desejaveis}
                            >
                                <Textarea
                                    id="requisitos_desejaveis"
                                    rows={3}
                                    value={data.requisitos_desejaveis}
                                    onChange={(e) => setData('requisitos_desejaveis', e.target.value)}
                                />
                            </Field>
                            <Field label="Benefícios" htmlFor="beneficios" error={errors.beneficios}>
                                <Textarea
                                    id="beneficios"
                                    rows={3}
                                    value={data.beneficios}
                                    onChange={(e) => setData('beneficios', e.target.value)}
                                    placeholder="Ex.: Vale-transporte, auxílio-alimentação…"
                                />
                            </Field>
                        </div>
                    </Secao>

                    <Secao titulo="Projeto">
                        <div className="grid gap-4 sm:grid-cols-[1fr_200px]">
                            <Field label="Nome do projeto" htmlFor="projeto_nome" error={errors.projeto_nome}>
                                <Input
                                    id="projeto_nome"
                                    value={data.projeto_nome}
                                    onChange={(e) => setData('projeto_nome', e.target.value)}
                                />
                            </Field>
                            <Field label="Código" htmlFor="projeto_codigo" error={errors.projeto_codigo}>
                                <Input
                                    id="projeto_codigo"
                                    value={data.projeto_codigo}
                                    onChange={(e) => setData('projeto_codigo', e.target.value)}
                                />
                            </Field>
                        </div>
                    </Secao>

                    <Secao titulo="Local de trabalho" descricao="Para vagas remotas, o endereço é opcional.">
                        <div className="grid gap-4 sm:grid-cols-6">
                            <Field
                                label="CEP"
                                htmlFor="cep"
                                error={errors.cep}
                                className="sm:col-span-2"
                                hint={buscandoCep ? 'Buscando endereço…' : undefined}
                            >
                                <Input
                                    id="cep"
                                    inputMode="numeric"
                                    value={data.cep}
                                    onChange={(e) => setData('cep', maskCep(e.target.value))}
                                    onBlur={buscarCep}
                                    placeholder="00000-000"
                                />
                            </Field>
                            <Field label="Cidade" htmlFor="cidade" error={errors.cidade} className="sm:col-span-3">
                                <Input id="cidade" value={data.cidade} onChange={(e) => setData('cidade', e.target.value)} />
                            </Field>
                            <Field label="UF" htmlFor="estado" error={errors.estado} className="sm:col-span-1">
                                <Select value={data.estado || undefined} onValueChange={(v) => setData('estado', v)}>
                                    <SelectTrigger id="estado" className="w-full">
                                        <SelectValue placeholder="UF" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {ufs.map((uf) => (
                                            <SelectItem key={uf} value={uf}>
                                                {uf}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field label="Bairro" htmlFor="bairro" error={errors.bairro} className="sm:col-span-2">
                                <Input id="bairro" value={data.bairro} onChange={(e) => setData('bairro', e.target.value)} />
                            </Field>
                            <Field label="Logradouro" htmlFor="logradouro" error={errors.logradouro} className="sm:col-span-2">
                                <Input
                                    id="logradouro"
                                    value={data.logradouro}
                                    onChange={(e) => setData('logradouro', e.target.value)}
                                />
                            </Field>
                            <Field label="Número" htmlFor="numero" error={errors.numero} className="sm:col-span-2">
                                <Input id="numero" value={data.numero} onChange={(e) => setData('numero', e.target.value)} />
                            </Field>
                            <Field label="Complemento" htmlFor="complemento" error={errors.complemento} className="sm:col-span-3">
                                <Input
                                    id="complemento"
                                    value={data.complemento}
                                    onChange={(e) => setData('complemento', e.target.value)}
                                />
                            </Field>
                            <Field
                                label="Referência do local"
                                htmlFor="local_trabalho"
                                error={errors.local_trabalho}
                                className="sm:col-span-3"
                            >
                                <Input
                                    id="local_trabalho"
                                    value={data.local_trabalho}
                                    onChange={(e) => setData('local_trabalho', e.target.value)}
                                    placeholder="Ex.: Campus UFSC, Trindade"
                                />
                            </Field>
                        </div>
                    </Secao>
                </div>

                {/* Lateral */}
                <div className="flex flex-col gap-5 xl:sticky xl:top-20">
                    <Secao titulo="Classificação">
                        <div className="flex flex-col gap-4">
                            {editando && (
                                <div className="flex items-center justify-between text-sm">
                                    <span className="text-muted-foreground">Status atual</span>
                                    <StatusVagaBadge status={vaga.status} />
                                </div>
                            )}
                            <Field label="Tipo" required error={errors.tipo}>
                                <Select value={data.tipo || undefined} onValueChange={(v) => setData('tipo', v)}>
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(tiposLabel).map(([v, l]) => (
                                            <SelectItem key={v} value={v}>
                                                {l}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field label="Área" required error={errors.area}>
                                <Select value={data.area || undefined} onValueChange={(v) => setData('area', v)}>
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {areas.map((a) => (
                                            <SelectItem key={a} value={a}>
                                                {a}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field label="Modalidade" required error={errors.modalidade}>
                                <Select value={data.modalidade} onValueChange={(v) => setData('modalidade', v)}>
                                    <SelectTrigger className="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(modalidadesLabel).map(([v, l]) => (
                                            <SelectItem key={v} value={v}>
                                                {l}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field label="Cursos desejados" error={errors.curso_desejado} hint="Vazio = aberto a qualquer curso.">
                                <CursosMultiSelect
                                    cursos={cursos}
                                    value={data.curso_desejado}
                                    onChange={(v) => setData('curso_desejado', v)}
                                />
                            </Field>
                        </div>
                    </Secao>

                    <Secao titulo="Condições">
                        <div className="flex flex-col gap-4">
                            <div className="grid grid-cols-2 items-end gap-3">
                                <Field label="Remuneração (R$)" htmlFor="remuneracao" error={errors.remuneracao}>
                                    <Input
                                        id="remuneracao"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={data.remuneracao}
                                        onChange={(e) => setData('remuneracao', e.target.value)}
                                        placeholder="Mín."
                                    />
                                </Field>
                                <Field label="Até (R$)" htmlFor="remuneracao_max" error={errors.remuneracao_max}>
                                    <Input
                                        id="remuneracao_max"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={data.remuneracao_max}
                                        onChange={(e) => setData('remuneracao_max', e.target.value)}
                                        placeholder="Máx."
                                    />
                                </Field>
                            </div>
                            <Field label="Carga horária semanal" htmlFor="carga_horaria" error={errors.carga_horaria}>
                                <Input
                                    id="carga_horaria"
                                    type="number"
                                    min="1"
                                    max="44"
                                    value={data.carga_horaria}
                                    onChange={(e) => setData('carga_horaria', e.target.value)}
                                    placeholder="Ex.: 20"
                                />
                            </Field>
                            <Field
                                label="Inscrições até"
                                htmlFor="data_encerramento"
                                required
                                error={errors.data_encerramento}
                            >
                                <Input
                                    id="data_encerramento"
                                    type="date"
                                    value={data.data_encerramento}
                                    onChange={(e) => setData('data_encerramento', e.target.value)}
                                />
                            </Field>
                            <label className="flex items-center justify-between gap-3 text-sm">
                                <span className="font-medium">Notificar novas candidaturas por e-mail</span>
                                <Switch
                                    checked={data.notificar_email}
                                    onCheckedChange={(v) => setData('notificar_email', Boolean(v))}
                                />
                            </label>
                        </div>
                    </Secao>

                    <div className="flex flex-col gap-2">
                        <Button type="submit" className="h-10 w-full" disabled={processing}>
                            {processing ? (
                                <Loader2 className="animate-spin" data-icon="inline-start" />
                            ) : (
                                <SendHorizonal data-icon="inline-start" />
                            )}
                            Enviar para autorização
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            className="h-10 w-full"
                            disabled={processing}
                            onClick={() => salvar('rascunho')}
                        >
                            <Save data-icon="inline-start" /> Salvar como rascunho
                        </Button>
                        <p className="text-center text-xs text-muted-foreground">
                            A vaga só aparece no portal após autorização do gestor.
                        </p>
                    </div>
                </div>
            </form>
        </InternalLayout>
    );
}
