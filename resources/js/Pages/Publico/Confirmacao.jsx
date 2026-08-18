import { Link } from '@inertiajs/react';
import { CheckCircle2, Search } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Button } from '@/components/ui/button';

export default function Confirmacao({ vaga, nome }) {
    return (
        <PublicLayout title="Candidatura enviada">
            <div className="mx-auto flex w-full max-w-lg flex-col items-center px-4 pt-16 text-center">
                <div className="flex size-16 items-center justify-center rounded-full bg-accent">
                    <CheckCircle2 className="size-8 text-primary" />
                </div>

                <h1 className="mt-6 text-2xl font-bold tracking-tight">Candidatura enviada!</h1>
                <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                    {nome ? `${nome.split(' ')[0]}, sua` : 'Sua'} candidatura para{' '}
                    <span className="font-semibold text-foreground">{vaga.titulo}</span> foi recebida. Enviamos um
                    e-mail de confirmação. O RH entrará em contato pelos dados informados.
                </p>

                <div className="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <Button asChild>
                        <Link href={route('vagas.publicas.index')}>Ver mais vagas</Link>
                    </Button>
                    <Button asChild variant="outline">
                        <Link href={route('candidatura.consulta')}>
                            <Search data-icon="inline-start" /> Acompanhar candidatura
                        </Link>
                    </Button>
                </div>

                <p className="mt-8 text-xs text-muted-foreground">
                    Dica: crie uma conta para acompanhar suas candidaturas em um só lugar.{' '}
                    <Link href={route('candidato.registro')} className="font-medium text-primary hover:underline">
                        Criar conta
                    </Link>
                </p>
            </div>
        </PublicLayout>
    );
}
