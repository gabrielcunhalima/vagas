import { Link } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    Bell,
    Briefcase,
    CalendarDays,
    Clock,
    Copy,
    GraduationCap,
    MapPin,
    Wallet,
} from 'lucide-react';
import { toast } from 'sonner';
import PublicLayout from '@/Layouts/PublicLayout';
import WhatsAppIcon from '@/components/icons/WhatsAppIcon';
import { Button } from '@/components/ui/button';
import { NovaBadge, TipoAdmissaoBadge } from '@/components/badges';
import { diasRestantes, faixaSalarial, formatDate, isNova, localVaga } from '@/lib/format';

const tituloSecaoClasses =
    'inline-flex w-fit items-center rounded-4xl bg-secondary px-3 py-1 text-[1.25rem] font-bold uppercase tracking-wider text-secondary-foreground';

function Secao({ titulo, children }) {
    return (
        <section>
            <h2 className={tituloSecaoClasses}>{titulo}</h2>
            <div className="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{children}</div>
        </section>
    );
}

function MetaLinha({ icon: Icon, label, children }) {
    return (
        <div className="flex items-center justify-between gap-3 text-sm">
            <span className="inline-flex items-center gap-2 text-muted-foreground">
                <Icon className="size-4" /> {label}
            </span>
            <span className="text-right font-medium">{children}</span>
        </div>
    );
}

/*
 * Detalhe da vaga em página própria. Os campos vêm do DRHFlow e cada bloco testa
 * o próprio valor: informação ausente na origem é omitida por completo, sem
 * rótulo órfão nem seção vazia.
 *
 * Não há mais "vagas relacionadas": elas eram calculadas por área, que
 * `EN_VAGA_EMPREGO` não tem.
 */
export default function Show({ vaga }) {
    const dias = diasRestantes(vaga.data_encerramento);
    const urlInscricao = route('inscricao.create', vaga.id);
    const local = localVaga(vaga);

    function copiarLink() {
        navigator.clipboard
            .writeText(window.location.href)
            .then(() => toast.success('Link copiado!'))
            .catch(() => toast.error('Não foi possível copiar o link.'));
    }

    const linkWhatsApp = `https://wa.me/?text=${encodeURIComponent(`${vaga.titulo}, Portal de Vagas FAPEU: ${window.location.href}`)}`;

    return (
        <PublicLayout title={vaga.titulo}>
            <div className="mx-auto w-full max-w-6xl px-4 pt-8">
                <Link
                    href={route('vagas.publicas.index')}
                    className="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft className="size-4" /> Todas as vagas
                </Link>

                {/* Cabeçalho */}
                <header className="mt-5">
                    <div className="flex flex-wrap items-center gap-2">
                        {isNova(vaga) && <NovaBadge />}
                        <TipoAdmissaoBadge tipo={vaga.tipo} codigo={vaga.tipo_codigo} />
                    </div>
                    <h1 className="mt-3 max-w-3xl text-2xl font-bold leading-tight tracking-tight sm:text-3xl">
                        {vaga.titulo}
                    </h1>
                    <div className="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm text-muted-foreground">
                        {local && (
                            <span className="inline-flex items-center gap-1.5">
                                <MapPin className="size-4" /> {local}
                            </span>
                        )}
                        {vaga.projeto_nome && <span>Projeto — {vaga.projeto_nome}</span>}
                    </div>
                </header>

                <div className="mt-8 grid items-start gap-8 lg:grid-cols-[1fr_330px]">
                    {/* Conteúdo */}
                    <article className="flex min-w-0 flex-col divide-y [&>*:not(:first-child)]:pt-8 [&>*:not(:last-child)]:pb-8">
                        {vaga.descricao && <Secao titulo="Sobre a vaga">{vaga.descricao}</Secao>}
                        {vaga.requisitos && <Secao titulo="Requisitos">{vaga.requisitos}</Secao>}
                        {vaga.beneficios && <Secao titulo="Benefícios">{vaga.beneficios}</Secao>}
                        {vaga.documentacao && (
                            <Secao titulo="Documentação necessária">{vaga.documentacao}</Secao>
                        )}
                        {vaga.horario && <Secao titulo="Horário">{vaga.horario}</Secao>}
                        {vaga.projeto_nome && <Secao titulo="Projeto">{vaga.projeto_nome}</Secao>}
                    </article>

                    {/* Painel de candidatura */}
                    <aside className="flex flex-col gap-4 lg:sticky lg:top-20">
                        <div className="rounded-xl bg-card/45 p-5 shadow-lg shadow-black/[0.07] backdrop-blur-2xl backdrop-saturate-150 dark:bg-card/30 dark:shadow-black/40">
                            <div
                                className={
                                    dias <= 5
                                        ? 'inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary dark:bg-primary/15'
                                        : 'inline-flex items-center gap-1.5 rounded-full bg-accent/80 px-3 py-1 text-xs font-semibold text-accent-foreground backdrop-blur-sm'
                                }
                            >
                                <CalendarDays className="size-3.5" />
                                {/* Wording próprio desta página (mais enfático que o `prazoInscricao`),
                                    mas o último dia usa o mesmo "Encerra hoje" do resto do portal. */}
                                {dias === 0
                                    ? 'Encerra hoje'
                                    : dias <= 5
                                      ? `Últimos ${dias} ${dias === 1 ? 'dia' : 'dias'} para se inscrever`
                                      : `Inscrições até ${formatDate(vaga.data_encerramento)}`}
                            </div>

                            <div className="mt-4 flex flex-col gap-3">
                                <MetaLinha icon={Wallet} label="Remuneração">
                                    {faixaSalarial(vaga)}
                                </MetaLinha>
                                {vaga.carga_horaria && (
                                    <MetaLinha icon={Clock} label="Carga horária">
                                        {vaga.carga_horaria}
                                    </MetaLinha>
                                )}
                                {vaga.escolaridade && (
                                    <MetaLinha icon={GraduationCap} label="Escolaridade">
                                        {vaga.escolaridade}
                                    </MetaLinha>
                                )}
                                {vaga.experiencia && (
                                    <MetaLinha icon={Briefcase} label="Experiência">
                                        {vaga.experiencia}
                                    </MetaLinha>
                                )}
                                {local && (
                                    <MetaLinha icon={MapPin} label="Local">
                                        {local}
                                    </MetaLinha>
                                )}
                            </div>

                            <Button asChild size="lg" className="mt-5 h-11 w-full text-base font-semibold">
                                <Link href={urlInscricao}>
                                    Candidatar-se agora <ArrowRight data-icon="inline-end" />
                                </Link>
                            </Button>
                        </div>

                        <div className="flex items-center justify-center gap-2 rounded-xl bg-card/45 p-2 backdrop-blur-2xl backdrop-saturate-150 dark:bg-card/30">
                            <Button variant="outline" size="sm" onClick={copiarLink}>
                                <Copy data-icon="inline-start" /> Copiar link
                            </Button>
                            <Button asChild variant="outline" size="sm">
                                <a href={linkWhatsApp} target="_blank" rel="noreferrer">
                                    <WhatsAppIcon data-icon="inline-start" /> WhatsApp
                                </a>
                            </Button>
                        </div>

                        <Link
                            href={route('alertas.create')}
                            className="inline-flex items-center justify-center gap-1.5 text-xs text-muted-foreground transition-colors hover:text-foreground"
                        >
                            <Bell className="size-3.5" /> Quero receber vagas como esta por e-mail
                        </Link>
                    </aside>
                </div>
            </div>
        </PublicLayout>
    );
}
