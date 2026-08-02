import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { Eye, EyeOff, Loader2 } from 'lucide-react';
import AuthLayout from '@/Layouts/AuthLayout';
import Field from '@/components/Field';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function Login({ redirect }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
        redirect: redirect ?? '',
    });
    const [verSenha, setVerSenha] = useState(false);

    function submit(e) {
        e.preventDefault();
        post(route('candidato.login.post'));
    }

    return (
        <AuthLayout
            title="Entrar"
            headline="Sua próxima oportunidade começa aqui."
            sub="Estágios, bolsas e empregos em projetos administrados pela FAPEU"
        >
            <h1 className="text-2xl font-bold tracking-tight">Bem-vindo de volta</h1>
            <p className="mt-1 text-sm text-muted-foreground">Entre para se candidatar e acompanhar suas candidaturas.</p>

            <form onSubmit={submit} className="mt-8 flex flex-col gap-5">
                <Field label="E-mail" htmlFor="email" error={errors.email}>
                    <Input
                        id="email"
                        type="email"
                        className="h-10"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        placeholder="seu@email.com"
                        autoComplete="username"
                        autoFocus
                        required
                    />
                </Field>

                <Field label="Senha" htmlFor="password" error={errors.password}>
                    <div className="relative">
                        <Input
                            id="password"
                            type={verSenha ? 'text' : 'password'}
                            className="h-10 pr-10"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="••••••••"
                            autoComplete="current-password"
                            required
                        />
                        <button
                            type="button"
                            onClick={() => setVerSenha((v) => !v)}
                            className="absolute inset-y-0 right-0 flex w-10 cursor-pointer items-center justify-center text-muted-foreground transition-colors hover:text-foreground"
                            aria-label={verSenha ? 'Ocultar senha' : 'Mostrar senha'}
                        >
                            {verSenha ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
                        </button>
                    </div>
                </Field>

                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="remember"
                            checked={data.remember}
                            onCheckedChange={(v) => setData('remember', Boolean(v))}
                        />
                        <Label htmlFor="remember" className="font-normal text-muted-foreground">
                            Manter conectado
                        </Label>
                    </div>

                    <Link href={route('candidato.senha.request')} className="text-sm font-medium text-primary hover:underline">
                        Esqueci minha senha
                    </Link>
                </div>

                <Button type="submit" className="h-10 w-full" disabled={processing}>
                    {processing && <Loader2 className="animate-spin" data-icon="inline-start" />}
                    Entrar
                </Button>
            </form>

            <div className="mt-8 rounded-lg bg-accent/60 px-4 py-3 text-center text-sm">
                Novo por aqui?{' '}
                <Link
                    href={redirect ? route('candidato.registro', { redirect }) : route('candidato.registro')}
                    className="font-semibold text-primary hover:underline"
                >
                    Crie sua conta gratuita
                </Link>
            </div>

            <p className="mt-6 text-center text-xs text-muted-foreground">
                Coordenador ou gestor?{' '}
                <Link href={route('login')} className="font-medium hover:text-foreground hover:underline">
                    Acesse o painel
                </Link>
            </p>
        </AuthLayout>
    );
}
