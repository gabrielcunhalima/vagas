import { Head, Link, usePage } from '@inertiajs/react';
import { Bell, Briefcase, ChevronDown, FileCheck2, LogOut, MailWarning, Menu, UserRound } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { Toaster } from '@/components/ui/sonner';
import FlashMessages from '@/components/FlashMessages';
import Logo from '@/components/Logo';
import ThemeToggle from '@/components/ThemeToggle';
import { iniciais } from '@/lib/format';
import { CONTAINER_LARGO } from '@/lib/layout';

function MenuMobile({ candidato }) {
    return (
        <Sheet>
            <SheetTrigger asChild>
                <Button variant="ghost" size="icon" className="lg:hidden" aria-label="Abrir menu">
                    <Menu />
                </Button>
            </SheetTrigger>
            <SheetContent side="right" className="w-72">
                <nav className="flex flex-col gap-1 p-4 pt-10">
                    <Link href={route('vagas.publicas.index')} className="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">
                        <Briefcase className="size-4 text-primary" /> Vagas
                    </Link>
                    <Link href={route('alertas.create')} className="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">
                        <Bell className="size-4 text-primary" /> Alertas de vagas
                    </Link>
                    <div className="my-3 border-t" />

                    {candidato ? (
                        <>
                            <Link href={route('candidato.candidaturas.index')} className="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">
                                <FileCheck2 className="size-4 text-primary" /> Minhas candidaturas
                            </Link>
                            <Link href={route('candidato.perfil.edit')} className="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">
                                <UserRound className="size-4 text-primary" /> Meus dados
                            </Link>
                            <Link
                                href={route('candidato.logout')}
                                method="post"
                                as="button"
                                className="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-destructive hover:bg-destructive/10"
                            >
                                <LogOut className="size-4" /> Sair
                            </Link>
                        </>
                    ) : (
                        <div className="flex flex-col gap-2 px-1 pt-1">
                            <Button asChild variant="outline">
                                <Link href={route('candidato.login')}>Entrar</Link>
                            </Button>
                            <Button asChild>
                                <Link href={route('candidato.registro')}>Criar conta</Link>
                            </Button>
                        </div>
                    )}
                </nav>
            </SheetContent>
        </Sheet>
    );
}

/*
 * O cadastro mínimo deixa duas pendências possíveis: e-mail por confirmar e perfil
 * por completar. Ambas aparecem desde o primeiro acesso — descobrir isso só ao ser
 * barrado na candidatura empurraria a frustração em vez de removê-la.
 */
function AvisosConta({ candidato }) {
    if (!candidato) return null;

    if (!candidato.email_verified) {
        return (
            <div className="border-b border-amber-500/30 bg-amber-500/10">
                <div className="mx-auto flex w-full max-w-6xl flex-wrap items-center gap-x-3 gap-y-1 px-4 py-2.5 text-sm">
                    <MailWarning className="size-4 shrink-0 text-amber-600 dark:text-amber-500" />
                    <span>
                        Confirme seu e-mail para se candidatar e ativar alertas.
                    </span>
                    <Link
                        href={route('candidato.verification.notice')}
                        className="font-semibold text-primary hover:underline"
                    >
                        Reenviar confirmação
                    </Link>
                </div>
            </div>
        );
    }

    if (!candidato.perfil_completo) {
        return (
            <div className="border-b bg-muted/60">
                <div className="mx-auto flex w-full max-w-6xl flex-wrap items-center gap-x-3 gap-y-1 px-4 py-2.5 text-sm">
                    <UserRound className="size-4 shrink-0 text-muted-foreground" />
                    <span>
                        Faltam {candidato.pendencias}{' '}
                        {candidato.pendencias === 1 ? 'informação' : 'informações'} no seu perfil para você poder se
                        candidatar.
                    </span>
                    <Link href={route('candidato.perfil.edit')} className="font-semibold text-primary hover:underline">
                        Completar perfil
                    </Link>
                </div>
            </div>
        );
    }

    return null;
}

/*
 * Com o cadastro mínimo a conta nasce sem nome — ele só chega quando o candidato
 * preenche o perfil. Até lá, o e-mail identifica a pessoa na interface.
 */
function primeiroNome(candidato) {
    if (candidato.nome) return candidato.nome.split(' ')[0];
    return candidato.email.split('@')[0];
}

export default function PublicLayout({ title, children }) {
    const { auth } = usePage().props;
    const candidato = auth?.candidato;

    return (
        <div className="flex min-h-dvh flex-col">
            <Head title={title} />
            <Toaster position="top-right" richColors />
            <FlashMessages />

            <AvisosConta candidato={candidato} />

            <header className="sticky top-0 z-40 border-b bg-background/85 backdrop-blur-md">
                <div className={`${CONTAINER_LARGO} flex h-16 items-center justify-between gap-4`}>
                    <Link href={route('home')} className="flex shrink-0 items-center gap-3">
                        <Logo className="h-9" />
                        <span className="hidden items-baseline gap-1.5 sm:flex">
                            <span className="text-sm font-medium text-muted-foreground">Portal de</span>
                            <span className="text-lg font-bold tracking-tight text-foreground">Vagas</span>
                        </span>
                    </Link>

                    <div className="flex items-center gap-2.5">
                        <ThemeToggle size="icon-lg" className="text-muted-foreground" iconClassName="size-5" />

                        {candidato ? (
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="outline" className="hidden h-10 gap-2.5 rounded-full pl-2 lg:inline-flex">
                                        <span className="flex size-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">
                                            {iniciais(candidato.nome)}
                                        </span>
                                        <span className="text-[0.925rem] font-medium">{primeiroNome(candidato)}</span>
                                        <ChevronDown className="size-4 text-muted-foreground" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" className="min-w-56">
                                    <DropdownMenuLabel className="font-normal">
                                        <div className="text-sm font-semibold">{candidato.nome ?? primeiroNome(candidato)}</div>
                                        <div className="text-xs text-muted-foreground">{candidato.email}</div>
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem asChild>
                                        <Link href={route('candidato.candidaturas.index')}>
                                            <FileCheck2 /> Minhas candidaturas
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem asChild>
                                        <Link href={route('candidato.perfil.edit')}>
                                            <UserRound /> Meus dados
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem asChild variant="destructive">
                                        <Link href={route('candidato.logout')} method="post" as="button" className="w-full">
                                            <LogOut /> Sair
                                        </Link>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        ) : (
                            <div className="hidden items-center gap-2 lg:flex">
                                <Button asChild variant="ghost" size="lg">
                                    <Link href={route('candidato.login')}>Entrar</Link>
                                </Button>
                                <Button asChild size="lg">
                                    <Link href={route('candidato.registro')}>Criar conta</Link>
                                </Button>
                            </div>
                        )}

                        <MenuMobile candidato={candidato} />
                    </div>
                </div>
            </header>

            <main className="flex-1">{children}</main>

            <footer className="mt-16 border-t bg-card">
                <div className="mx-auto grid w-full max-w-6xl gap-8 px-4 py-10 md:grid-cols-[2fr_1fr_1fr]">
                    <div>
                        <Logo className="h-9" />
                        <p className="mt-3 max-w-xs text-sm leading-relaxed text-muted-foreground">
                            Oportunidades de estágio, emprego e bolsa em projetos administrados pela FAPEU.
                        </p>
                    </div>
                    <div>
                        <div className="text-xs font-bold uppercase tracking-wider text-muted-foreground">Portal</div>
                        <ul className="mt-3 flex flex-col gap-2 text-sm">
                            <li><Link href={route('vagas.publicas.index')} className="text-muted-foreground transition-colors hover:text-foreground">Vagas abertas</Link></li>
                            <li><Link href={route('alertas.create')} className="text-muted-foreground transition-colors hover:text-foreground">Alertas de vagas</Link></li>
                            <li><Link href={route('politica.privacidade')} className="text-muted-foreground transition-colors hover:text-foreground">Política de Privacidade</Link></li>
                        </ul>
                    </div>
                    <div>
                        <div className="text-xs font-bold uppercase tracking-wider text-muted-foreground">Candidatos</div>
                        <ul className="mt-3 flex flex-col gap-2 text-sm">
                            {candidato ? (
                                <>
                                    <li><Link href={route('candidato.candidaturas.index')} className="text-muted-foreground transition-colors hover:text-foreground">Minhas candidaturas</Link></li>
                                    <li><Link href={route('candidato.perfil.edit')} className="text-muted-foreground transition-colors hover:text-foreground">Meus dados</Link></li>
                                </>
                            ) : (
                                <>
                                    <li><Link href={route('candidato.login')} className="text-muted-foreground transition-colors hover:text-foreground">Entrar</Link></li>
                                    <li><Link href={route('candidato.registro')} className="text-muted-foreground transition-colors hover:text-foreground">Criar conta</Link></li>
                                </>
                            )}
                        </ul>
                    </div>
                </div>
                <div className="border-t">
                    <p className="mx-auto max-w-6xl px-4 py-4 text-xs text-muted-foreground">
                        © {new Date().getFullYear()} FAPEU, Fundação de Amparo à Pesquisa e Extensão Universitária
                    </p>
                </div>
            </footer>
        </div>
    );
}
