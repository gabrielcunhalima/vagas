import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { CalendarDays, FileText, Loader2, MapPin, Send, UserCheck } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import CurriculoDropzone from '@/components/CurriculoDropzone';
import Field from '@/components/Field';
import { ModalidadeBadge, TipoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { maskCep, maskCpf, maskTelefone, onlyDigits, validarCpf } from '@/lib/cpf';
import { disponibilidades, ufs } from '@/lib/enums';
import { diasRestantes, faixaSalarial, prazoInscricao } from '@/lib/format';

function Secao({ titulo, descricao, children }) {
    return (
        <section className="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
            <h2 className="text-sm font-bold tracking-tight">{titulo}</h2>
            {descricao && <p className="mt-0.5 text-xs text-muted-foreground">{descricao}</p>}
            <div className="mt-4">{children}</div>
        </section>
    );
}

export default function Candidatura({ vaga, candidato, prefill = {} }) {
    const { data, setData, post, processing, errors } = useForm({
        _honeypot: '',
        nome: prefill.nome ?? '',
        email: prefill.email ?? '',
        cpf: prefill.cpf ? maskCpf(prefill.cpf) : '',
        telefone: prefill.telefone ? maskTelefone(prefill.telefone) : '',
        curso: prefill.curso ?? '',
        instituicao: prefill.instituicao ?? '',
        semestre: prefill.semestre ?? '',
        previsao_conclusao: prefill.previsao_conclusao ?? '',
        carta_apresentacao: '',
        cep: prefill.cep ? maskCep(prefill.cep) : '',
        logradouro: prefill.logradouro ?? '',
        numero: prefill.numero ?? '',
        complemento: prefill.complemento ?? '',
        bairro: prefill.bairro ?? '',
        cidade: prefill.cidade ?? '',
        estado: prefill.estado ?? '',
        linkedin: prefill.linkedin ?? '',
        pretensao_salarial: prefill.pretensao_salarial ?? '',
        disponibilidade: prefill.disponibilidade ?? '',
        pcd: prefill.pcd === '1',
        pcd_tipo: prefill.pcd_tipo ?? '',
        curriculo: null,
        lgpd_consentimento: false,
    });

    const [cpfInvalido, setCpfInvalido] = useState(false);
    const [buscandoCep, setBuscandoCep] = useState(false);
    const [usarCurriculoPerfil, setUsarCurriculoPerfil] = useState(Boolean(candidato?.tem_curriculo));

    const cpfBloqueado = Boolean(candidato && prefill.cpf);
    const dias = diasRestantes(vaga.data_encerramento);
    const prazo = prazoInscricao(vaga.data_encerramento);

    function validarCpfLocal() {
        const digits = onlyDigits(data.cpf);
        setCpfInvalido(digits.length > 0 && !validarCpf(digits));
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

    function submit(e) {
        e.preventDefault();
        post(route('inscricao.store', vaga.id), { forceFormData: true });
    }

    return (
        <PublicLayout title={`Candidatura: ${vaga.titulo}`}>
            <div className="mx-auto w-full max-w-6xl px-4 pt-8">
                <h1 className="text-2xl font-bold tracking-tight">Candidatar-se</h1>
                <p className="mt-1 text-sm text-muted-foreground">
                    Preencha seus dados para concorrer à vaga. Campos com <span className="text-destructive">*</span> são
                    obrigatórios.
                </p>

                <div className="mt-6 grid items-start gap-6 lg:grid-cols-[1fr_320px]">
                    <form onSubmit={submit} className="flex min-w-0 flex-col gap-5">
                        {/* Honeypot anti-spam: invisível para humanos */}
                        <input
                            type="text"
                            name="_honeypot"
                            value={data._honeypot}
                            onChange={(e) => setData('_honeypot', e.target.value)}
                            className="absolute -left-[9999px] size-px opacity-0"
                            tabIndex={-1}
                            autoComplete="off"
                            aria-hidden="true"
                        />

                        {candidato ? (
                            <div className="flex items-center gap-3 rounded-xl bg-accent/60 p-4 text-sm ring-1 ring-primary/20">
                                <UserCheck className="size-5 shrink-0 text-primary" />
                                <p>
                                    Olá, <span className="font-semibold">{candidato.nome.split(' ')[0]}</span>! Seus dados do
                                    perfil já foram preenchidos, revise e envie.
                                </p>
                            </div>
                        ) : (
                            <div className="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-card p-4 text-sm ring-1 ring-foreground/10">
                                <p className="text-muted-foreground">
                                    Já tem conta? Entre para preencher tudo automaticamente.
                                </p>
                                <Button asChild variant="outline" size="sm">
                                    <Link href={route('candidato.login', { redirect: `/candidatura/${vaga.id}` })}>
                                        Entrar
                                    </Link>
                                </Button>
                            </div>
                        )}

                        <Secao titulo="Dados pessoais">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <Field label="Nome completo" htmlFor="nome" required error={errors.nome} className="sm:col-span-2">
                                    <Input id="nome" value={data.nome} onChange={(e) => setData('nome', e.target.value)} required />
                                </Field>
                                <Field label="E-mail" htmlFor="email" required error={errors.email}>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                    />
                                </Field>
                                <Field
                                    label="CPF"
                                    htmlFor="cpf"
                                    required
                                    error={errors.cpf ?? (cpfInvalido ? 'CPF inválido.' : undefined)}
                                >
                                    <Input
                                        id="cpf"
                                        inputMode="numeric"
                                        value={data.cpf}
                                        onChange={(e) => {
                                            setData('cpf', maskCpf(e.target.value));
                                            setCpfInvalido(false);
                                        }}
                                        onBlur={validarCpfLocal}
                                        placeholder="000.000.000-00"
                                        readOnly={cpfBloqueado}
                                        className={cpfBloqueado ? 'bg-muted/60' : undefined}
                                        required
                                    />
                                </Field>
                                <Field label="Telefone" htmlFor="telefone" error={errors.telefone} className="sm:col-span-2">
                                    <Input
                                        id="telefone"
                                        inputMode="numeric"
                                        value={data.telefone}
                                        onChange={(e) => setData('telefone', maskTelefone(e.target.value))}
                                        placeholder="(48) 99999-9999"
                                    />
                                </Field>
                            </div>
                        </Secao>

                        <Secao titulo="Formação">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <Field label="Curso" htmlFor="curso" required error={errors.curso}>
                                    <Input id="curso" value={data.curso} onChange={(e) => setData('curso', e.target.value)} required />
                                </Field>
                                <Field label="Instituição de ensino" htmlFor="instituicao" required error={errors.instituicao}>
                                    <Input
                                        id="instituicao"
                                        value={data.instituicao}
                                        onChange={(e) => setData('instituicao', e.target.value)}
                                        required
                                    />
                                </Field>
                                <Field label="Semestre atual" htmlFor="semestre" error={errors.semestre}>
                                    <Input
                                        id="semestre"
                                        value={data.semestre}
                                        onChange={(e) => setData('semestre', e.target.value)}
                                        placeholder="Ex.: 5º"
                                    />
                                </Field>
                                <Field label="Previsão de conclusão" htmlFor="previsao_conclusao" error={errors.previsao_conclusao}>
                                    <Input
                                        id="previsao_conclusao"
                                        type="date"
                                        value={data.previsao_conclusao}
                                        onChange={(e) => setData('previsao_conclusao', e.target.value)}
                                    />
                                </Field>
                            </div>
                        </Secao>

                        <Secao titulo="Endereço" descricao="Opcional, informe o CEP para preenchimento automático.">
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
                                <Field label="Bairro" htmlFor="bairro" error={errors.bairro} className="sm:col-span-3">
                                    <Input id="bairro" value={data.bairro} onChange={(e) => setData('bairro', e.target.value)} />
                                </Field>
                                <Field label="Logradouro" htmlFor="logradouro" error={errors.logradouro} className="sm:col-span-3">
                                    <Input
                                        id="logradouro"
                                        value={data.logradouro}
                                        onChange={(e) => setData('logradouro', e.target.value)}
                                    />
                                </Field>
                                <Field label="Número" htmlFor="numero" error={errors.numero} className="sm:col-span-2">
                                    <Input id="numero" value={data.numero} onChange={(e) => setData('numero', e.target.value)} />
                                </Field>
                                <Field label="Complemento" htmlFor="complemento" error={errors.complemento} className="sm:col-span-4">
                                    <Input
                                        id="complemento"
                                        value={data.complemento}
                                        onChange={(e) => setData('complemento', e.target.value)}
                                    />
                                </Field>
                            </div>
                        </Secao>

                        <Secao titulo="Informações adicionais" descricao="Opcionais, mas ajudam o coordenador a te conhecer melhor.">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <Field label="LinkedIn" htmlFor="linkedin" error={errors.linkedin} className="sm:col-span-2">
                                    <Input
                                        id="linkedin"
                                        type="url"
                                        value={data.linkedin}
                                        onChange={(e) => setData('linkedin', e.target.value)}
                                        placeholder="https://linkedin.com/in/voce"
                                    />
                                </Field>
                                <Field label="Pretensão salarial (R$)" htmlFor="pretensao_salarial" error={errors.pretensao_salarial}>
                                    <Input
                                        id="pretensao_salarial"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={data.pretensao_salarial}
                                        onChange={(e) => setData('pretensao_salarial', e.target.value)}
                                    />
                                </Field>
                                <Field label="Disponibilidade para início" htmlFor="disponibilidade" error={errors.disponibilidade}>
                                    <Select
                                        value={data.disponibilidade || undefined}
                                        onValueChange={(v) => setData('disponibilidade', v)}
                                    >
                                        <SelectTrigger id="disponibilidade" className="w-full">
                                            <SelectValue placeholder="Selecione" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {disponibilidades.map((d) => (
                                                <SelectItem key={d} value={d}>
                                                    {d}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </Field>
                                <div className="flex flex-col gap-3 sm:col-span-2">
                                    <label className="flex items-center gap-3">
                                        <Switch checked={data.pcd} onCheckedChange={(v) => setData('pcd', Boolean(v))} />
                                        <span className="text-sm font-medium">Sou pessoa com deficiência (PcD)</span>
                                    </label>
                                    {data.pcd && (
                                        <Field label="Tipo de deficiência" htmlFor="pcd_tipo" error={errors.pcd_tipo}>
                                            <Input
                                                id="pcd_tipo"
                                                value={data.pcd_tipo}
                                                onChange={(e) => setData('pcd_tipo', e.target.value)}
                                            />
                                        </Field>
                                    )}
                                </div>
                            </div>
                        </Secao>

                        <Secao titulo="Carta de apresentação" descricao="Opcional, conte por que você é a pessoa certa para esta vaga.">
                            <Field error={errors.carta_apresentacao}>
                                <Textarea
                                    rows={5}
                                    maxLength={5000}
                                    value={data.carta_apresentacao}
                                    onChange={(e) => setData('carta_apresentacao', e.target.value)}
                                    placeholder="Escreva aqui…"
                                />
                            </Field>
                        </Secao>

                        <Secao titulo="Currículo">
                            {candidato?.tem_curriculo && (
                                <label className="mb-4 flex items-center gap-3 rounded-lg bg-muted/50 px-3 py-2.5 ring-1 ring-foreground/10">
                                    <Checkbox
                                        checked={usarCurriculoPerfil}
                                        onCheckedChange={(v) => {
                                            setUsarCurriculoPerfil(Boolean(v));
                                            if (v) setData('curriculo', null);
                                        }}
                                    />
                                    <span className="inline-flex items-center gap-2 text-sm">
                                        <FileText className="size-4 text-primary" />
                                        Usar currículo do perfil
                                        <span className="text-muted-foreground">({candidato.curriculo_nome})</span>
                                    </span>
                                </label>
                            )}

                            {!usarCurriculoPerfil && (
                                <Field
                                    error={errors.curriculo}
                                    hint={!candidato ? 'Obrigatório para visitantes.' : undefined}
                                >
                                    <CurriculoDropzone
                                        file={data.curriculo}
                                        onChange={(f) => setData('curriculo', f)}
                                        error={errors.curriculo}
                                    />
                                </Field>
                            )}
                        </Secao>

                        {!candidato && (
                            <div className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                                <label className="flex items-start gap-2.5">
                                    <Checkbox
                                        checked={data.lgpd_consentimento}
                                        onCheckedChange={(v) => setData('lgpd_consentimento', Boolean(v))}
                                        className="mt-0.5"
                                    />
                                    <span className="text-sm leading-relaxed">
                                        Autorizo o uso dos meus dados pessoais para este processo seletivo, conforme a{' '}
                                        <Link href={route('politica.privacidade')} className="font-semibold text-primary hover:underline">
                                            Política de Privacidade
                                        </Link>{' '}
                                        (LGPD). <span className="text-destructive">*</span>
                                    </span>
                                </label>
                                {errors.lgpd_consentimento && (
                                    <p className="mt-2 text-xs font-medium text-destructive">{errors.lgpd_consentimento}</p>
                                )}
                            </div>
                        )}

                        <Button
                            type="submit"
                            size="lg"
                            className="h-11 w-full gap-2 px-6 sm:w-auto sm:self-end pr-[27px]"
                            disabled={processing}
                        >
                            {processing ? (
                                <Loader2 className="size-4 animate-spin" />
                            ) : (
                                <Send className="size-4" />
                            )}
                            Enviar candidatura
                        </Button>
                    </form>

                    {/* Resumo da vaga */}
                    <aside className="order-first lg:order-none lg:sticky lg:top-20">
                        <div className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                            <div className="flex flex-wrap gap-2">
                                <TipoBadge tipo={vaga.tipo} />
                                <ModalidadeBadge modalidade={vaga.modalidade} />
                            </div>
                            <h2 className="mt-3 font-semibold leading-snug">{vaga.titulo}</h2>
                            <div className="mt-3 flex flex-col gap-2 text-sm text-muted-foreground">
                                <span className="inline-flex items-center gap-2">
                                    <MapPin className="size-4" />
                                    {vaga.modalidade === 'remoto' ? 'Remoto' : vaga.cidade ? `${vaga.cidade}/${vaga.estado}` : 'A definir'}
                                </span>
                                <span className="inline-flex items-center gap-2">
                                    <CalendarDays className="size-4" />
                                    {dias <= 5 ? (
                                        <span className="font-semibold text-primary">{prazo}</span>
                                    ) : (
                                        prazo
                                    )}
                                </span>
                                <span className="font-medium text-foreground">{faixaSalarial(vaga)}</span>
                            </div>
                            <Link
                                href={route('vagas.publicas.show', vaga.id)}
                                className="mt-4 inline-block text-xs font-medium text-primary hover:underline"
                            >
                                Ver descrição completa da vaga
                            </Link>
                        </div>
                    </aside>
                </div>
            </div>
        </PublicLayout>
    );
}
