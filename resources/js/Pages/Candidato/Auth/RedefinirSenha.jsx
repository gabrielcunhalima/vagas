import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { Eye, EyeOff, Loader2 } from 'lucide-react';
import AuthLayout from '@/Layouts/AuthLayout';
import Field from '@/components/Field';
import PasswordStrengthMeter from '@/components/PasswordStrengthMeter';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

export default function RedefinirSenha({ token, email }) {
    const { data, setData, post, processing, errors } = useForm({
        token,
        email: email ?? '',
        password: '',
        password_confirmation: '',
    });
    const [verSenha, setVerSenha] = useState(false);

    function submit(e) {
        e.preventDefault();
        post(route('candidato.senha.update'));
    }

    return (
        <AuthLayout
            title="Redefinir senha"
            headline="Sua próxima oportunidade começa aqui."
            sub="Estágios, bolsas e empregos em projetos administrados pela FAPEU"
        >
            <h1 className="text-2xl font-bold tracking-tight">Crie uma nova senha</h1>
            <p className="mt-1 text-sm text-muted-foreground">Escolha uma senha forte para proteger sua conta.</p>

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
                        autoFocus={!email}
                        required
                    />
                </Field>

                <Field label="Nova senha" htmlFor="password" error={errors.password}>
                    <div className="relative">
                        <Input
                            id="password"
                            type={verSenha ? 'text' : 'password'}
                            className="h-10 pr-10"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="••••••••"
                            autoComplete="new-password"
                            autoFocus={Boolean(email)}
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
                    <PasswordStrengthMeter password={data.password} />
                </Field>

                <Field label="Confirmar nova senha" htmlFor="password_confirmation" error={errors.password_confirmation}>
                    <Input
                        id="password_confirmation"
                        type={verSenha ? 'text' : 'password'}
                        className="h-10"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        placeholder="••••••••"
                        autoComplete="new-password"
                        required
                    />
                </Field>

                <Button type="submit" className="h-10 w-full" disabled={processing}>
                    {processing && <Loader2 className="animate-spin" data-icon="inline-start" />}
                    Redefinir senha
                </Button>
            </form>

            <p className="mt-8 text-center text-sm text-muted-foreground">
                Lembrou sua senha?{' '}
                <Link href={route('candidato.login')} className="font-semibold text-primary hover:underline">
                    Entrar
                </Link>
            </p>
        </AuthLayout>
    );
}
