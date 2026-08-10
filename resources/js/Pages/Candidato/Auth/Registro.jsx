import { useEffect, useMemo, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { Eye, EyeOff, Loader2 } from 'lucide-react';
import AuthLayout from '@/Layouts/AuthLayout';
import Field from '@/components/Field';
import PasswordStrengthMeter, { REGRAS_SENHA } from '@/components/PasswordStrengthMeter';
import PoliticaPrivacidadeConteudo from '@/components/PoliticaPrivacidadeConteudo';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { maskCpf, onlyDigits, validarCpf } from '@/lib/cpf';

/*
 * Cadastro mínimo: CPF, e-mail, senha e consentimento.
 *
 * O restante do perfil — dados pessoais, endereço, formação e acessibilidade —
 * saiu daqui e passou a ser preenchido em "Meus dados", no ritmo do candidato.
 * As validações imediatas de CPF, senha e confirmação continuam valendo; deixam
 * de guardar a passagem de etapa e passam a guardar o envio.
 */
export default function Registro({ redirect }) {
    const { data, setData, post, processing, errors: serverErrors } = useForm({
        cpf: '',
        email: '',
        password: '',
        password_confirmation: '',
        lgpd_consentimento: false,
    });

    const [tentou, setTentou] = useState(false);
    const [tocado, setTocado] = useState({});
    const [verSenha, setVerSenha] = useState(false);
    // idle | checking | ok | invalido | duplicado | indisponivel
    const [cpfStatus, setCpfStatus] = useState('idle');

    const forcaSenha = REGRAS_SENHA.filter((r) => r.re.test(data.password)).length;
    const cpfDigits = onlyDigits(data.cpf);
    const senhasCoincidem = Boolean(data.password_confirmation) && data.password === data.password_confirmation;

    function marcarTocado(campo) {
        setTocado((t) => ({ ...t, [campo]: true }));
    }

    const locais = useMemo(() => {
        const e = {};
        if (!cpfDigits) e.cpf = 'Informe seu CPF.';
        else if (cpfDigits.length < 11) e.cpf = 'CPF incompleto.';
        else if (!validarCpf(cpfDigits)) e.cpf = 'CPF inválido.';
        else if (cpfStatus === 'duplicado') e.cpf = 'Este CPF já está cadastrado.';
        if (!/^\S+@\S+\.\S+$/.test(data.email)) e.email = 'Informe um e-mail válido.';
        if (forcaSenha < REGRAS_SENHA.length) e.password = 'A senha não atende a todos os requisitos.';
        if (!data.password_confirmation || data.password !== data.password_confirmation)
            e.password_confirmation = 'As senhas não coincidem.';
        if (!data.lgpd_consentimento) e.lgpd_consentimento = 'É necessário autorizar o tratamento dos dados.';
        return e;
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [data, cpfStatus]);

    /* O erro aparece assim que o campo perde o foco, sem esperar o envio. */
    function erroCampo(campo) {
        return serverErrors[campo] ?? (tocado[campo] || tentou ? locais[campo] : undefined);
    }

    /* CPF tem gatilho próprio: o veredito é definitivo aos 11 dígitos e silencioso antes disso. */
    function erroCpf() {
        if (serverErrors.cpf) return serverErrors.cpf;
        if (cpfStatus === 'invalido') return 'CPF inválido.';
        if (cpfStatus === 'duplicado') return undefined; // o bloco com os atalhos assume a sinalização
        return tentou ? locais.cpf : undefined;
    }

    function enviar() {
        if (Object.keys(locais).length > 0 || cpfStatus === 'checking') {
            setTentou(true);
            return;
        }
        post(route('candidato.registro.post', redirect ? { redirect } : {}));
    }

    /*
     * Avalia o CPF assim que ele fica completo, sem esperar o campo perder o foco.
     * O cleanup cancela o timer e aborta a requisição em voo, para que a resposta de
     * um CPF já editado não sobrescreva o estado do valor atual.
     */
    useEffect(() => {
        if (cpfDigits.length < 11) {
            setCpfStatus('idle');
            return undefined;
        }
        if (!validarCpf(cpfDigits)) {
            setCpfStatus('invalido'); // dígitos verificadores não conferem: não consulta o servidor
            return undefined;
        }

        setCpfStatus('checking');
        const controller = new AbortController();
        const timer = setTimeout(async () => {
            try {
                const res = await fetch(route('candidato.registro.verificar-cpf', { cpf: cpfDigits }), {
                    signal: controller.signal,
                    headers: { Accept: 'application/json' },
                });
                if (!res.ok) throw new Error('verificação indisponível');
                const d = await res.json();
                setCpfStatus(d.existe ? 'duplicado' : 'ok');
            } catch (err) {
                // Falha de rede, 429 ou 5xx não podem travar o cadastro: o envio revalida a unicidade.
                if (err.name !== 'AbortError') setCpfStatus('indisponivel');
            }
        }, 400);

        return () => {
            clearTimeout(timer);
            controller.abort();
        };
    }, [cpfDigits]);

    return (
        <AuthLayout
            title="Criar conta"
            headline="Sua próxima oportunidade pode estar aqui."
            sub="Crie sua conta em um passo."
        >
            <h1 className="text-2xl font-bold tracking-tight">Criar conta</h1>
            <p className="mt-1 text-sm text-muted-foreground">
                Já tem conta?{' '}
                <Link href={route('candidato.login')} className="font-semibold text-primary hover:underline">
                    Entrar
                </Link>
            </p>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    enviar();
                }}
                className="mt-6"
            >
                <div className="flex flex-col gap-5 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
                    <Field
                        label="CPF"
                        htmlFor="cpf"
                        required
                        error={erroCpf()}
                        success={cpfStatus === 'ok' ? 'CPF disponível.' : undefined}
                        hint={cpfStatus === 'checking' ? 'Verificando CPF…' : undefined}
                    >
                        <Input
                            id="cpf"
                            inputMode="numeric"
                            className="h-10"
                            value={data.cpf}
                            onChange={(e) => setData('cpf', maskCpf(e.target.value))}
                            placeholder="000.000.000-00"
                            autoFocus
                            aria-invalid={cpfStatus === 'invalido' || cpfStatus === 'duplicado' || undefined}
                            aria-describedby={cpfStatus === 'duplicado' ? 'cpf-duplicado' : undefined}
                        />

                        {cpfStatus === 'duplicado' && (
                            <div
                                id="cpf-duplicado"
                                className="mt-0.5 rounded-lg border border-destructive/30 bg-destructive/5 p-3 text-xs"
                            >
                                <p className="font-medium text-destructive">Este CPF já está cadastrado.</p>
                                <p className="mt-1 text-muted-foreground">
                                    Entre com o e-mail da sua conta. Se não lembrar qual usou, recupere o acesso pela senha.
                                </p>
                                <div className="mt-2 flex flex-wrap gap-x-4 gap-y-1">
                                    <Link href={route('candidato.login')} className="font-semibold text-primary hover:underline">
                                        Entrar
                                    </Link>
                                    <Link
                                        href={route('candidato.senha.request')}
                                        className="font-semibold text-primary hover:underline"
                                    >
                                        Esqueci minha senha
                                    </Link>
                                </div>
                            </div>
                        )}
                    </Field>

                    <Field label="E-mail" htmlFor="email" required error={erroCampo('email')}>
                        <Input
                            id="email"
                            type="email"
                            className="h-10"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            onBlur={() => marcarTocado('email')}
                            placeholder="seu@email.com"
                            autoComplete="username"
                        />
                    </Field>

                    <Field label="Senha" htmlFor="password" required error={erroCampo('password')}>
                        <div className="relative">
                            <Input
                                id="password"
                                type={verSenha ? 'text' : 'password'}
                                className="h-10 pr-10"
                                value={data.password}
                                onChange={(e) => {
                                    setData('password', e.target.value);
                                    // Não insistir no erro enquanto a senha está sendo corrigida.
                                    setTocado((t) => ({ ...t, password: false }));
                                }}
                                onBlur={() => marcarTocado('password')}
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
                        error={erroCampo('password_confirmation')}
                        success={senhasCoincidem ? 'As senhas coincidem.' : undefined}
                    >
                        <Input
                            id="password_confirmation"
                            type={verSenha ? 'text' : 'password'}
                            className="h-10"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            onBlur={() => marcarTocado('password_confirmation')}
                            autoComplete="new-password"
                        />
                    </Field>

                    <div className="flex flex-col gap-1.5">
                        <label className="flex items-start gap-2.5">
                            <Checkbox
                                checked={data.lgpd_consentimento}
                                onCheckedChange={(v) => {
                                    setData('lgpd_consentimento', Boolean(v));
                                    marcarTocado('lgpd_consentimento');
                                }}
                                className="mt-0.5"
                            />
                            <span className="text-sm leading-relaxed">
                                Autorizo o tratamento dos meus dados pessoais para processos seletivos, conforme a{' '}
                                <Dialog>
                                    <DialogTrigger asChild>
                                        <button type="button" className="font-semibold text-primary hover:underline">
                                            Política de Privacidade
                                        </button>
                                    </DialogTrigger>
                                    <DialogContent className="max-h-[80vh] overflow-y-auto sm:max-w-2xl">
                                        <DialogTitle className="sr-only">Política de Privacidade</DialogTitle>
                                        <PoliticaPrivacidadeConteudo />
                                    </DialogContent>
                                </Dialog>{' '}
                                (LGPD).
                            </span>
                        </label>
                        {erroCampo('lgpd_consentimento') && (
                            <p className="text-xs font-medium text-destructive">{erroCampo('lgpd_consentimento')}</p>
                        )}
                    </div>
                </div>

                <Button type="submit" className="mt-5 h-10 w-full" disabled={processing}>
                    {processing ? (
                        <>
                            <Loader2 className="animate-spin" data-icon="inline-start" /> Criando conta…
                        </>
                    ) : (
                        'Criar conta'
                    )}
                </Button>

                <p className="mt-4 text-center text-xs text-muted-foreground">
                    Depois de criar a conta você já pode navegar pelas vagas. Os dados do currículo são pedidos só quando
                    for se candidatar.
                </p>
            </form>
        </AuthLayout>
    );
}
