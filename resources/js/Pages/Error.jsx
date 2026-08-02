import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import Logo from '@/components/Logo';

const MENSAGENS = {
    403: {
        titulo: 'Acesso negado',
        texto: 'Você não tem permissão para acessar esta página.',
    },
    404: {
        titulo: 'Página não encontrada',
        texto: 'A página que você procura não existe ou a vaga não está mais disponível.',
    },
    419: {
        titulo: 'Sessão expirada',
        texto: 'Sua sessão expirou. Recarregue a página e tente novamente.',
    },
    500: {
        titulo: 'Erro interno',
        texto: 'Algo deu errado do nosso lado. Tente novamente em instantes.',
    },
    503: {
        titulo: 'Em manutenção',
        texto: 'O portal está em manutenção rápida. Volte em alguns minutos.',
    },
};

export default function Error({ status }) {
    const { titulo, texto } = MENSAGENS[status] ?? MENSAGENS[500];

    return (
        <div className="flex min-h-dvh flex-col items-center justify-center bg-background px-4 text-center">
            <Head title={`${status}: ${titulo}`} />
            <Logo className="h-10" />
            <p className="mt-8 text-6xl font-bold tracking-tight text-primary/25">{status}</p>
            <h1 className="mt-2 text-2xl font-bold tracking-tight">{titulo}</h1>
            <p className="mt-2 max-w-sm text-sm leading-relaxed text-muted-foreground">{texto}</p>
            <Button asChild className="mt-7">
                <Link href={route('home')}>Voltar ao início</Link>
            </Button>
        </div>
    );
}
