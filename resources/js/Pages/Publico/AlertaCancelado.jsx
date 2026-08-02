import { Link } from '@inertiajs/react';
import { BellOff } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Button } from '@/components/ui/button';

export default function AlertaCancelado() {
    return (
        <PublicLayout title="Alertas cancelados">
            <div className="mx-auto flex w-full max-w-md flex-col items-center px-4 pt-16 text-center">
                <div className="flex size-14 items-center justify-center rounded-full bg-muted">
                    <BellOff className="size-6 text-muted-foreground" />
                </div>
                <h1 className="mt-5 text-2xl font-bold tracking-tight">Alertas cancelados</h1>
                <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                    Você não receberá mais e-mails de novas vagas. Se mudar de ideia, é só ativar novamente.
                </p>
                <div className="mt-7 flex gap-3">
                    <Button asChild variant="outline">
                        <Link href={route('alertas.create')}>Reativar alertas</Link>
                    </Button>
                    <Button asChild>
                        <Link href={route('vagas.publicas.index')}>Ver vagas</Link>
                    </Button>
                </div>
            </div>
        </PublicLayout>
    );
}
