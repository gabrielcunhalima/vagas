import { Link, useForm } from '@inertiajs/react';
import { Loader2, MailCheck } from 'lucide-react';
import { usePage } from '@inertiajs/react';
import AuthLayout from '@/Layouts/AuthLayout';
import Field from '@/components/Field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

export default function EsqueciSenha() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });
    const { flash } = usePage().props;

    function submit(e) {
        e.preventDefault();
        post(route('candidato.senha.email'));
    }

    if (flash?.success) {
        return (
            <AuthLayout
                title="Verifique seu e-mail"
                headline="Sua próxima oportunidade começa aqui."
                sub="Estágios, bolsas e empregos em projetos administrados pela FAPEU"
            >
                <div className="mx-auto flex size-12 items-center justify-center rounded-full bg-accent">
                    <MailCheck className="size-6 text-accent-foreground" />
                </div>
                <h1 className="mt-5 text-2xl font-bold tracking-tight">Verifique seu e-mail</h1>
                <p className="mt-2 text-sm leading-relaxed text-muted-foreground">{flash.success}</p>

                <Link href={route('candidato.login')} className="mt-8 inline-block text-sm font-semibold text-primary hover:underline">
                    Voltar para o login
                </Link>
            </AuthLayout>
        );
    }

    return (
        <AuthLayout
            title="Esqueci minha senha"
            headline="Sua próxima oportunidade começa aqui."
            sub="Estágios, bolsas e empregos em projetos administrados pela FAPEU"
        >
            <h1 className="text-2xl font-bold tracking-tight">Esqueceu sua senha?</h1>
            <p className="mt-1 text-sm text-muted-foreground">
                Informe seu e-mail cadastrado e enviaremos um link para você criar uma nova senha.
            </p>

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

                <Button type="submit" className="h-10 w-full" disabled={processing}>
                    {processing && <Loader2 className="animate-spin" data-icon="inline-start" />}
                    Enviar link de recuperação
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
