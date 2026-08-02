import { Head, Link, usePage } from '@inertiajs/react';
import { Bell, Briefcase, ChevronDown, FileCheck2, LogOut, Menu, SearchCheck, UserRound } from 'lucide-react';
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
                    <Link href={route('candidatura.consulta')} className="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">
                        <SearchCheck className="size-4 text-primary" /> Acompanhar candidatura
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

export default function PublicLayout({ title, children }) {
    const { auth } = usePage().props;
    const candidato = auth?.candidato;

    return (
        <div className="flex min-h-dvh flex-col">
            <Head title={title} />
            <Toaster position="top-right" richColors />
            <FlashMessages />

            <header className="sticky top-0 z-40 border-b bg-background/85 backdrop-blur-md">
                <div className="mx-auto flex h-16 w-full max-w-6xl items-center justify-between gap-4 px-4">
                    <Link href={route('home')} className="flex shrink-0 items-center gap-3">
                        <Logo className="h-9" />
                        <span className="hidden flex-col leading-none sm:flex">
                            <span className="text-[0.65rem] font-medium uppercase tracking-wider text-muted-foreground">Portal de</span>
                            <span className="text-[1.25rem] font-bold tracking-tight text-foreground-vagas">Vagas</span>
                        </span>
                    </Link>

                    <nav className="hidden items-center gap-1 lg:flex">
                        <Button asChild variant="ghost" size="sm" className="text-muted-foreground hover:text-foreground">
                            <Link href={route('vagas.publicas.index')}>Vagas</Link>
                        </Button>
                        <Button asChild variant="ghost" size="sm" className="text-muted-foreground hover:text-foreground">
                            <Link href={route('alertas.create')}>Alertas</Link>
                        </Button>
                        <Button asChild variant="ghost" size="sm" className="text-muted-foreground hover:text-foreground">
                            <Link href={route('candidatura.consulta')}>Acompanhar candidatura</Link>
                        </Button>
                    </nav>

                    <div className="flex items-center gap-1.5">
                        <ThemeToggle className="text-muted-foreground" />

                        {candidato ? (
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="outline" size="sm" className="hidden gap-2 rounded-full pl-1.5 lg:inline-flex">
                                        <span className="flex size-5 items-center justify-center rounded-full bg-primary text-[0.6rem] font-bold text-primary-foreground">
                                            {iniciais(candidato.nome)}
                                        </span>
                                        {candidato.nome.split(' ')[0]}
                                        <ChevronDown className="size-3.5 text-muted-foreground" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" className="min-w-56">
                                    <DropdownMenuLabel className="font-normal">
                                        <div className="text-sm font-semibold">{candidato.nome}</div>
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
                            <div className="hidden items-center gap-1.5 lg:flex">
                                <Button asChild variant="ghost" size="sm">
                                    <Link href={route('candidato.login')}>Entrar</Link>
                                </Button>
                                <Button asChild size="sm">
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
                            <li><Link href={route('candidatura.consulta')} className="text-muted-foreground transition-colors hover:text-foreground">Acompanhar candidatura</Link></li>
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
