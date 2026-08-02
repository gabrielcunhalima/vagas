import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Loader2, MailCheck } from 'lucide-react';
import FlashMessages from '@/components/FlashMessages';
import Logo from '@/components/Logo';
import { Button } from '@/components/ui/button';
import { Toaster } from '@/components/ui/sonner';

export default function VerificarEmail() {
    const { auth } = usePage().props;
    const { post, processing } = useForm({});

    function reenviar(e) {
        e.preventDefault();
        post(route('candidato.verification.send'));
    }

    return (
        <div className="flex min-h-dvh flex-col items-center justify-center bg-background px-4 py-10">
            <Head title="Confirme seu e-mail" />
            <Toaster position="top-right" richColors />
            <FlashMessages />

            <Link href={route('home')}>
                <Logo className="h-10" />
            </Link>

            <div className="mt-8 w-full max-w-md rounded-xl bg-card p-8 text-center ring-1 ring-foreground/10">
                <div className="mx-auto flex size-12 items-center justify-center rounded-full bg-accent">
                    <MailCheck className="size-6 text-accent-foreground" />
                </div>

                <h1 className="mt-5 text-xl font-bold tracking-tight">Confirme seu e-mail</h1>
                <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                    Enviamos um link de confirmação para{' '}
                    <span className="font-semibold text-foreground">{auth?.candidato?.email}</span>. Confirme para
                    acessar sua conta e se candidatar às vagas.
                </p>

                <form onSubmit={reenviar} className="mt-6">
                    <Button type="submit" className="w-full" disabled={processing}>
                        {processing && <Loader2 className="animate-spin" data-icon="inline-start" />}
                        Reenviar e-mail de confirmação
                    </Button>
                </form>

                <p className="mt-4 text-xs text-muted-foreground">Verifique também a caixa de spam.</p>
            </div>

            <Link
                href={route('candidato.logout')}
                method="post"
                as="button"
                className="mt-6 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                Sair da conta
            </Link>
        </div>
    );
}
