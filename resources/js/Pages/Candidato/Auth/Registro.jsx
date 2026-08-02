import { useMemo, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, Check, Eye, EyeOff, Loader2 } from 'lucide-react';
import AuthLayout from '@/Layouts/AuthLayout';
import CurriculoDropzone from '@/components/CurriculoDropzone';
import Field from '@/components/Field';
import PasswordStrengthMeter, { REGRAS_SENHA } from '@/components/PasswordStrengthMeter';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { maskCep, maskCpf, maskTelefone, onlyDigits, validarCpf } from '@/lib/cpf';
import { niveisEscolaridade, ufs } from '@/lib/enums';
import { cn } from '@/lib/utils';

const ETAPAS = [
    { label: 'Conta', campos: ['cpf', 'email', 'password', 'password_confirmation'] },
    { label: 'Dados pessoais', campos: ['nome', 'nome_social', 'nacionalidade', 'telefone'] },
    { label: 'Endereço', campos: ['cep', 'estado', 'cidade', 'bairro', 'logradouro', 'numero', 'complemento'] },
    { label: 'Formação', campos: ['nivel_escolaridade', 'situacao_curso', 'curso', 'instituicao', 'semestre', 'previsao_conclusao', 'curriculo'] },
    { label: 'Acessibilidade', campos: ['possui_acessibilidade', 'acessibilidade_detalhe'] },
    { label: 'Questionário', campos: ['conflito_interesse', 'conflito_interesse_detalhe', 'codigo_conduta_aceite', 'lgpd_consentimento'] },
];

function SimNao({ value, onChange, idPrefix }) {
    return (
        <RadioGroup value={value} onValueChange={onChange} className="grid grid-cols-2 gap-3">
            {[
                ['0', 'Não'],
                ['1', 'Sim'],
            ].map(([v, l]) => (
                <Label
                    key={v}
                    htmlFor={`${idPrefix}-${v}`}
                    className={cn(
                        'flex cursor-pointer items-center gap-2.5 rounded-lg border p-3.5 text-sm font-medium transition-colors',
                        value === v ? 'border-primary bg-accent/50' : 'border-input hover:bg-muted/50',
                    )}
                >
                    <RadioGroupItem id={`${idPrefix}-${v}`} value={v} />
                    {l}
                </Label>
            ))}
        </RadioGroup>
    );
}

function Stepper({ etapa, irPara }) {
    return (
        <>
            {/* Desktop */}
            <ol className="hidden select-none items-start sm:flex">
                {ETAPAS.map((et, i) => {
                    const concluida = i < etapa;
                    const corrente = i === etapa;
                    return (
                        <li key={et.label} className={cn('flex items-start', i > 0 && 'flex-1')}>
                            {i > 0 && (
                                <span className={cn('mx-1 mt-3.5 h-px flex-1', i <= etapa ? 'bg-primary' : 'bg-border')} />
                            )}
                            <button
                                type="button"
                                onClick={() => concluida && irPara(i)}
                                className={cn('flex w-16 flex-col items-center gap-1.5', concluida && 'cursor-pointer')}
                                disabled={!concluida}
                            >
                                <span
                                    className={cn(
                                        'flex size-7 items-center justify-center rounded-full text-xs font-bold transition-colors',
                                        concluida
                                            ? 'bg-primary text-primary-foreground'
                                            : corrente
                                              ? 'bg-primary/15 text-primary ring-2 ring-primary/40'
                                              : 'bg-muted text-muted-foreground',
                                    )}
                                >
                                    {concluida ? <Check className="size-4" /> : i + 1}
                                </span>
                                <span
                                    className={cn(
                                        'text-center text-[0.68rem] leading-tight',
                                        corrente ? 'font-semibold text-foreground' : 'text-muted-foreground',
                                    )}
                                >
                                    {et.label}
                                </span>
                            </button>
                        </li>
                    );
                })}
            </ol>

            {/* Mobile */}
            <div className="sm:hidden">
                <div className="flex items-center justify-between text-xs">
                    <span className="font-semibold">{ETAPAS[etapa].label}</span>
                    <span className="text-muted-foreground">
                        Etapa {etapa + 1} de {ETAPAS.length}
                    </span>
                </div>
                <Progress value={((etapa + 1) / ETAPAS.length) * 100} className="mt-2 h-1.5" />
            </div>
        </>
    );
}

export default function Registro() {
    const { data, setData, post, processing, errors: serverErrors } = useForm({
        cpf: '',
        email: '',
        password: '',
        password_confirmation: '',
        nome: '',
        nome_social: '',
        nacionalidade: 'Brasileira',
        telefone: '',
        cep: '',
        estado: '',
        cidade: '',
        bairro: '',
        logradouro: '',
        numero: '',
        complemento: '',
        nivel_escolaridade: '',
        situacao_curso: 'cursando',
        curso: '',
        instituicao: '',
        semestre: '',
        previsao_conclusao: '',
        curriculo: null,
        possui_acessibilidade: '0',
        acessibilidade_detalhe: '',
        conflito_interesse: '0',
        conflito_interesse_detalhe: '',
        codigo_conduta_aceite: false,
        lgpd_consentimento: false,
    });

    const [etapa, setEtapa] = useState(0);
    const [tentou, setTentou] = useState({});
    const [verSenha, setVerSenha] = useState(false);
    const [cpfStatus, setCpfStatus] = useState('idle'); // idle | checking | ok | invalido | duplicado
    const [buscandoCep, setBuscandoCep] = useState(false);

    const cursando = data.situacao_curso === 'cursando';
    const forcaSenha = REGRAS_SENHA.filter((r) => r.re.test(data.password)).length;

    function validarEtapa(i) {
        const e = {};
        if (i === 0) {
            if (!validarCpf(data.cpf)) e.cpf = 'Informe um CPF válido.';
            else if (cpfStatus === 'duplicado') e.cpf = 'Este CPF já está cadastrado.';
            if (!/^\S+@\S+\.\S+$/.test(data.email)) e.email = 'Informe um e-mail válido.';
            if (forcaSenha < REGRAS_SENHA.length) e.password = 'A senha não atende a todos os requisitos.';
            if (!data.password_confirmation || data.password !== data.password_confirmation)
                e.password_confirmation = 'As senhas não coincidem.';
        }
        if (i === 1) {
            if (!data.nome.trim()) e.nome = 'Informe seu nome completo.';
            if (!data.nacionalidade.trim()) e.nacionalidade = 'Informe sua nacionalidade.';
        }
        if (i === 3) {
            if (!data.nivel_escolaridade) e.nivel_escolaridade = 'Selecione seu nível de escolaridade.';
            if (!data.curso.trim()) e.curso = 'Informe seu curso.';
            if (!data.instituicao.trim()) e.instituicao = 'Informe a instituição de ensino.';
            if (cursando && !data.semestre.trim()) e.semestre = 'Informe o semestre atual.';
            if (!data.previsao_conclusao) e.previsao_conclusao = 'Informe a data.';
        }
        if (i === 4) {
            if (data.possui_acessibilidade === '1' && !data.acessibilidade_detalhe.trim())
                e.acessibilidade_detalhe = 'Descreva a acessibilidade necessária.';
        }
        if (i === 5) {
            if (data.conflito_interesse === '1' && !data.conflito_interesse_detalhe.trim())
                e.conflito_interesse_detalhe = 'Detalhe a relação informada.';
            if (!data.codigo_conduta_aceite) e.codigo_conduta_aceite = 'Aceite o Código de Conduta para continuar.';
            if (!data.lgpd_consentimento) e.lgpd_consentimento = 'É necessário autorizar o tratamento dos dados.';
        }
        return e;
    }

    const locais = useMemo(
        () => validarEtapa(etapa),
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [data, etapa, cpfStatus],
    );

    function erro(campo) {
        return serverErrors[campo] ?? (tentou[etapa] ? locais[campo] : undefined);
    }

    function avancar() {
        if (Object.keys(locais).length > 0 || cpfStatus === 'checking') {
            setTentou((t) => ({ ...t, [etapa]: true }));
            return;
        }
        if (etapa === ETAPAS.length - 1) {
            enviar();
            return;
        }
        setEtapa((e) => e + 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function enviar() {
        post(route('candidato.registro.post'), {
            forceFormData: true,
            onError: (errs) => {
                const idx = ETAPAS.findIndex((et) => et.campos.some((c) => errs[c]));
                if (idx >= 0) setEtapa(idx);
            },
        });
    }

    async function verificarCpfDisponivel() {
        const digits = onlyDigits(data.cpf);
        if (!digits) return setCpfStatus('idle');
        if (!validarCpf(digits)) return setCpfStatus('invalido');
        setCpfStatus('checking');
        try {
            const res = await fetch(route('candidato.registro.verificar-cpf', { cpf: digits }));
            const d = await res.json();
            setCpfStatus(d.existe ? 'duplicado' : 'ok');
        } catch {
            setCpfStatus('ok'); // servidor revalida no envio
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
        <AuthLayout
            title="Criar conta"
            wide
            headline="Sua próxima oportunidade de emprego está aqui."
            sub="Preencha seu perfil uma única vez e use em todas as candidaturas."
        >
            <h1 className="text-2xl font-bold tracking-tight">Criar conta</h1>
            <p className="mt-1 text-sm text-muted-foreground">
                Já tem conta?{' '}
                <Link href={route('candidato.login')} className="font-semibold text-primary hover:underline">
                    Entrar
                </Link>
            </p>

            <div className="mt-7">
                <Stepper etapa={etapa} irPara={setEtapa} />
            </div>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    avancar();
                }}
                className="mt-6"
            >
                <div className="rounded-xl bg-card p-6 ring-1 ring-foreground/10">
                    {/* ── Etapa 1: Conta ── */}
                    {etapa === 0 && (
                        <div className="flex flex-col gap-5">
                            <Field
                                label="CPF"
                                htmlFor="cpf"
                                required
                                error={erro('cpf')}
                                hint={
                                    cpfStatus === 'checking'
                                        ? 'Verificando CPF…'
                                        : cpfStatus === 'ok'
                                          ? 'CPF disponível.'
                                          : undefined
                                }
                            >
                                <Input
                                    id="cpf"
                                    inputMode="numeric"
                                    className="h-10"
                                    value={data.cpf}
                                    onChange={(e) => {
                                        setData('cpf', maskCpf(e.target.value));
                                        setCpfStatus('idle');
                                    }}
                                    onBlur={verificarCpfDisponivel}
                                    placeholder="000.000.000-00"
                                    autoFocus
                                />
                            </Field>

                            <Field label="E-mail" htmlFor="email" required error={erro('email')}>
                                <Input
                                    id="email"
                                    type="email"
                                    className="h-10"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    placeholder="seu@email.com"
                                    autoComplete="username"
                                />
                            </Field>

                            <Field label="Senha" htmlFor="password" required error={erro('password')}>
                                <div className="relative">
                                    <Input
                                        id="password"
                                        type={verSenha ? 'text' : 'password'}
                                        className="h-10 pr-10"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setVerSenha((v) => !v)}
                                        className="absolute inset-y-0 right-0 flex w-10 cursor-pointer items-center justify-center text-muted-foreground hover:text-foreground"
                                        aria-label="Mostrar/ocultar senha"
                                    >
                                        {verSenha ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
                                    </button>
                                </div>
                                <PasswordStrengthMeter password={data.password} />
                            </Field>

                            <Field
                                label="Confirmar senha"
                                htmlFor="password_confirmation"
                                required
                                error={erro('password_confirmation')}
                                hint={
                                    data.password_confirmation && data.password === data.password_confirmation
                                        ? 'As senhas coincidem.'
                                        : undefined
                                }
                            >
                                <Input
                                    id="password_confirmation"
                                    type={verSenha ? 'text' : 'password'}
                                    className="h-10"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                    autoComplete="new-password"
                                />
                            </Field>
                        </div>
                    )}

                    {/* ── Etapa 2: Dados pessoais ── */}
                    {etapa === 1 && (
                        <div className="grid gap-5 sm:grid-cols-2">
                            <Field label="Nome completo" htmlFor="nome" required error={erro('nome')} className="sm:col-span-2">
                                <Input
                                    id="nome"
                                    className="h-10"
                                    value={data.nome}
                                    onChange={(e) => setData('nome', e.target.value)}
                                    autoFocus
                                />
                            </Field>
                            <Field
                                label="Nome social"
                                htmlFor="nome_social"
                                error={erro('nome_social')}
                                hint="Opcional, como prefere ser chamado(a)."
                            >
                                <Input
                                    id="nome_social"
                                    className="h-10"
                                    value={data.nome_social}
                                    onChange={(e) => setData('nome_social', e.target.value)}
                                />
                            </Field>
                            <Field label="Nacionalidade" htmlFor="nacionalidade" required error={erro('nacionalidade')}>
                                <Input
                                    id="nacionalidade"
                                    className="h-10"
                                    value={data.nacionalidade}
                                    onChange={(e) => setData('nacionalidade', e.target.value)}
                                />
                            </Field>
                            <Field label="Telefone" htmlFor="telefone" error={erro('telefone')} className="sm:col-span-2">
                                <Input
                                    id="telefone"
                                    inputMode="numeric"
                                    className="h-10"
                                    value={data.telefone}
                                    onChange={(e) => setData('telefone', maskTelefone(e.target.value))}
                                    placeholder="(48) 99999-9999"
                                />
                            </Field>
                        </div>
                    )}

                    {/* ── Etapa 3: Endereço ── */}
                    {etapa === 2 && (
                        <div className="grid gap-5 sm:grid-cols-6">
                            <Field
                                label="CEP"
                                htmlFor="cep"
                                error={erro('cep')}
                                className="sm:col-span-2"
                                hint={buscandoCep ? 'Buscando endereço…' : undefined}
                            >
                                <Input
                                    id="cep"
                                    inputMode="numeric"
                                    className="h-10"
                                    value={data.cep}
                                    onChange={(e) => setData('cep', maskCep(e.target.value))}
                                    onBlur={buscarCep}
                                    placeholder="00000-000"
                                    autoFocus
                                />
                            </Field>
                            <Field label="Estado" htmlFor="estado" error={erro('estado')} className="sm:col-span-1">
                                <Select value={data.estado || undefined} onValueChange={(v) => setData('estado', v)}>
                                    <SelectTrigger id="estado" className="h-10 w-full">
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
                            <Field label="Cidade" htmlFor="cidade" error={erro('cidade')} className="sm:col-span-3">
                                <Input
                                    id="cidade"
                                    className="h-10"
                                    value={data.cidade}
                                    onChange={(e) => setData('cidade', e.target.value)}
                                />
                            </Field>
                            <Field label="Bairro" htmlFor="bairro" error={erro('bairro')} className="sm:col-span-3">
                                <Input
                                    id="bairro"
                                    className="h-10"
                                    value={data.bairro}
                                    onChange={(e) => setData('bairro', e.target.value)}
                                />
                            </Field>
                            <Field label="Logradouro" htmlFor="logradouro" error={erro('logradouro')} className="sm:col-span-3">
                                <Input
                                    id="logradouro"
                                    className="h-10"
                                    value={data.logradouro}
                                    onChange={(e) => setData('logradouro', e.target.value)}
                                />
                            </Field>
                            <Field label="Número" htmlFor="numero" error={erro('numero')} className="sm:col-span-2">
                                <Input
                                    id="numero"
                                    className="h-10"
                                    value={data.numero}
                                    onChange={(e) => setData('numero', e.target.value)}
                                />
                            </Field>
                            <Field label="Complemento" htmlFor="complemento" error={erro('complemento')} className="sm:col-span-4">
                                <Input
                                    id="complemento"
                                    className="h-10"
                                    value={data.complemento}
                                    onChange={(e) => setData('complemento', e.target.value)}
                                />
                            </Field>
                        </div>
                    )}

                    {/* ── Etapa 4: Formação ── */}
                    {etapa === 3 && (
                        <div className="grid gap-5 sm:grid-cols-2">
                            <Field label="Nível de escolaridade" htmlFor="nivel_escolaridade" required error={erro('nivel_escolaridade')}>
                                <Select
                                    value={data.nivel_escolaridade || undefined}
                                    onValueChange={(v) => setData('nivel_escolaridade', v)}
                                >
                                    <SelectTrigger id="nivel_escolaridade" className="h-10 w-full">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(niveisEscolaridade).map(([v, l]) => (
                                            <SelectItem key={v} value={v}>
                                                {l}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>

                            <Field label="Situação" required error={erro('situacao_curso')}>
                                <RadioGroup
                                    value={data.situacao_curso}
                                    onValueChange={(v) => setData('situacao_curso', v)}
                                    className="flex h-10 items-center gap-5"
                                >
                                    <span className="flex items-center gap-2">
                                        <RadioGroupItem id="sit-cursando" value="cursando" />
                                        <Label htmlFor="sit-cursando" className="font-normal">
                                            Cursando
                                        </Label>
                                    </span>
                                    <span className="flex items-center gap-2">
                                        <RadioGroupItem id="sit-concluido" value="concluido" />
                                        <Label htmlFor="sit-concluido" className="font-normal">
                                            Concluído
                                        </Label>
                                    </span>
                                </RadioGroup>
                            </Field>

                            <Field label="Curso" htmlFor="curso" required error={erro('curso')}>
                                <Input
                                    id="curso"
                                    className="h-10"
                                    value={data.curso}
                                    onChange={(e) => setData('curso', e.target.value)}
                                    placeholder="Ex.: Administração"
                                />
                            </Field>

                            <Field label="Instituição de ensino" htmlFor="instituicao" required error={erro('instituicao')}>
                                <Input
                                    id="instituicao"
                                    className="h-10"
                                    value={data.instituicao}
                                    onChange={(e) => setData('instituicao', e.target.value)}
                                    placeholder="Ex.: UFSC"
                                />
                            </Field>

                            {cursando && (
                                <Field label="Semestre atual" htmlFor="semestre" required error={erro('semestre')}>
                                    <Input
                                        id="semestre"
                                        className="h-10"
                                        value={data.semestre}
                                        onChange={(e) => setData('semestre', e.target.value)}
                                        placeholder="Ex.: 5º"
                                    />
                                </Field>
                            )}

                            <Field
                                label={cursando ? 'Previsão de conclusão' : 'Data de conclusão'}
                                htmlFor="previsao_conclusao"
                                required
                                error={erro('previsao_conclusao')}
                            >
                                <Input
                                    id="previsao_conclusao"
                                    type="date"
                                    className="h-10"
                                    value={data.previsao_conclusao}
                                    onChange={(e) => setData('previsao_conclusao', e.target.value)}
                                />
                            </Field>

                            <Field
                                label="Currículo"
                                error={erro('curriculo')}
                                hint="Opcional, você pode enviar depois, no seu perfil ou na candidatura."
                                className="sm:col-span-2"
                            >
                                <CurriculoDropzone file={data.curriculo} onChange={(f) => setData('curriculo', f)} error={erro('curriculo')} />
                            </Field>
                        </div>
                    )}

                    {/* ── Etapa 5: Acessibilidade ── */}
                    {etapa === 4 && (
                        <div className="flex flex-col gap-5">
                            <Field
                                label="Você precisa de alguma condição de acessibilidade?"
                                error={erro('possui_acessibilidade')}
                                hint="Usamos essa informação para preparar entrevistas e ambientes adequados."
                            >
                                <SimNao
                                    value={data.possui_acessibilidade}
                                    onChange={(v) => setData('possui_acessibilidade', v)}
                                    idPrefix="acess"
                                />
                            </Field>

                            {data.possui_acessibilidade === '1' && (
                                <Field label="Descreva a acessibilidade necessária" htmlFor="acessibilidade_detalhe" required error={erro('acessibilidade_detalhe')}>
                                    <Textarea
                                        id="acessibilidade_detalhe"
                                        rows={4}
                                        value={data.acessibilidade_detalhe}
                                        onChange={(e) => setData('acessibilidade_detalhe', e.target.value)}
                                    />
                                </Field>
                            )}
                        </div>
                    )}

                    {/* ── Etapa 6: Questionário ── */}
                    {etapa === 5 && (
                        <div className="flex flex-col gap-5">
                            <Field
                                label="Você tem parentesco ou relação próxima com colaboradores ou dirigentes da FAPEU?"
                                error={erro('conflito_interesse')}
                            >
                                <SimNao
                                    value={data.conflito_interesse}
                                    onChange={(v) => setData('conflito_interesse', v)}
                                    idPrefix="conflito"
                                />
                            </Field>

                            {data.conflito_interesse === '1' && (
                                <Field label="Detalhe a relação" htmlFor="conflito_interesse_detalhe" required error={erro('conflito_interesse_detalhe')}>
                                    <Textarea
                                        id="conflito_interesse_detalhe"
                                        rows={4}
                                        value={data.conflito_interesse_detalhe}
                                        onChange={(e) => setData('conflito_interesse_detalhe', e.target.value)}
                                    />
                                </Field>
                            )}

                            <div className="flex flex-col gap-3 border-t pt-5">
                                <label className="flex items-start gap-2.5">
                                    <Checkbox
                                        checked={data.codigo_conduta_aceite}
                                        onCheckedChange={(v) => setData('codigo_conduta_aceite', Boolean(v))}
                                        className="mt-0.5"
                                    />
                                    <span className="text-sm leading-relaxed">
                                        Li e aceito o{' '}
                                        <a
                                            href="https://fapeu.org.br/codigoconduta"
                                            target="_blank"
                                            rel="noreferrer"
                                            className="font-semibold text-primary hover:underline"
                                        >
                                            Código de Conduta da FAPEU
                                        </a>
                                        .
                                    </span>
                                </label>
                                {erro('codigo_conduta_aceite') && (
                                    <p className="text-xs font-medium text-destructive">{erro('codigo_conduta_aceite')}</p>
                                )}

                                <label className="flex items-start gap-2.5">
                                    <Checkbox
                                        checked={data.lgpd_consentimento}
                                        onCheckedChange={(v) => setData('lgpd_consentimento', Boolean(v))}
                                        className="mt-0.5"
                                    />
                                    <span className="text-sm leading-relaxed">
                                        Autorizo o tratamento dos meus dados pessoais para processos seletivos, conforme a{' '}
                                        <Link
                                            href={route('politica.privacidade')}
                                            className="font-semibold text-primary hover:underline"
                                        >
                                            Política de Privacidade
                                        </Link>{' '}
                                        (LGPD).
                                    </span>
                                </label>
                                {erro('lgpd_consentimento') && (
                                    <p className="text-xs font-medium text-destructive">{erro('lgpd_consentimento')}</p>
                                )}
                            </div>
                        </div>
                    )}
                </div>

                <div className="mt-5 flex items-center justify-between">
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => setEtapa((e) => Math.max(0, e - 1))}
                        disabled={etapa === 0 || processing}
                        className={cn(etapa === 0 && 'invisible')}
                    >
                        <ArrowLeft data-icon="inline-start" /> Voltar
                    </Button>

                    <Button type="submit" className="h-10 min-w-36" disabled={processing}>
                        {processing ? (
                            <Loader2 className="animate-spin" data-icon="inline-start" />
                        ) : etapa === ETAPAS.length - 1 ? (
                            'Criar conta'
                        ) : (
                            <>
                                Continuar <ArrowRight data-icon="inline-end" />
                            </>
                        )}
                        {processing && 'Enviando…'}
                    </Button>
                </div>
            </form>
        </AuthLayout>
    );
}
