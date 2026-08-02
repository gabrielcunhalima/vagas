import { Head, Link, usePage } from '@inertiajs/react';
import {
    Briefcase,
    ExternalLink,
    LayoutDashboard,
    LogOut,
    Menu,
    PlusCircle,
    ShieldCheck,
    Users,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { Toaster } from '@/components/ui/sonner';
import FlashMessages from '@/components/FlashMessages';
import Logo from '@/components/Logo';
import ThemeToggle from '@/components/ThemeToggle';
import { iniciais } from '@/lib/format';
import { cn } from '@/lib/utils';

function ativa(atual, padrao) {
    if (padrao.endsWith('.*')) return atual.startsWith(padrao.slice(0, -2));
    return atual === padrao;
}

function NavItem({ href, icon: Icon, children, active, external = false }) {
    const classes = cn(
        'mx-3 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
        active
            ? 'bg-sidebar-accent text-sidebar-accent-foreground'
            : 'text-sidebar-foreground/65 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground',
    );

    if (external) {
        return (
            <a href={href} target="_blank" rel="noreferrer" className={classes}>
                <Icon className="size-4 shrink-0" /> {children}
            </a>
        );
    }

    return (
        <Link href={href} className={classes}>
            <Icon className="size-4 shrink-0" /> {children}
        </Link>
    );
}

function SidebarSection({ children }) {
    return (
        <div className="px-6 pb-1.5 pt-5 text-[0.65rem] font-bold uppercase tracking-widest text-sidebar-foreground/40">
            {children}
        </div>
    );
}

function SidebarContent({ user, atual }) {
    const perfil = user.perfil;

    return (
        <div className="flex h-full flex-col bg-sidebar text-sidebar-foreground">
            <div className="flex items-center gap-3 border-b border-sidebar-border px-5 py-5">
                <Logo white className="h-9" />
                <div>
                    <div className="text-sm font-bold leading-tight">Portal de Vagas</div>
                    <div className="text-xs text-sidebar-foreground/55">Painel interno</div>
                </div>
            </div>

            <div className="flex-1 overflow-y-auto pb-4">
                {(perfil === 'coordenador' || perfil === 'admin') && (
                    <>
                        <SidebarSection>Coordenador</SidebarSection>
                        <nav className="flex flex-col gap-0.5">
                            <NavItem href={route('coord.dashboard')} icon={LayoutDashboard} active={ativa(atual, 'coord.dashboard')}>
                                Dashboard
                            </NavItem>
                            <NavItem
                                href={route('coord.vagas.index')}
                                icon={Briefcase}
                                active={ativa(atual, 'coord.vagas.*') && atual !== 'coord.vagas.create'}
                            >
                                Minhas vagas
                            </NavItem>
                            <NavItem href={route('coord.vagas.create')} icon={PlusCircle} active={atual === 'coord.vagas.create'}>
                                Nova vaga
                            </NavItem>
                            <NavItem
                                href={route('coord.candidaturas.todas')}
                                icon={Users}
                                active={ativa(atual, 'coord.candidaturas.*')}
                            >
                                Candidaturas
                            </NavItem>
                        </nav>
                    </>
                )}

                {(perfil === 'gestor' || perfil === 'admin') && (
                    <>
                        <SidebarSection>Gestor</SidebarSection>
                        <nav className="flex flex-col gap-0.5">
                            <NavItem href={route('gestor.dashboard')} icon={LayoutDashboard} active={ativa(atual, 'gestor.dashboard')}>
                                Dashboard
                            </NavItem>
                            <NavItem href={route('gestor.vagas.index')} icon={ShieldCheck} active={ativa(atual, 'gestor.vagas.*')}>
                                Autorizar vagas
                            </NavItem>
                        </nav>
                    </>
                )}

                <SidebarSection>Sistema</SidebarSection>
                <nav>
                    <NavItem href={route('vagas.publicas.index')} icon={ExternalLink} external>
                        Ver portal público
                    </NavItem>
                </nav>
            </div>

            <div className="border-t border-sidebar-border px-4 py-3.5">
                <div className="flex items-center gap-2.5">
                    <div className="flex size-9 shrink-0 items-center justify-center rounded-full bg-sidebar-primary text-xs font-bold text-sidebar-primary-foreground">
                        {iniciais(user.name)}
                    </div>
                    <div className="min-w-0 flex-1">
                        <div className="truncate text-sm font-semibold">{user.name}</div>
                        <div className="text-xs capitalize text-sidebar-foreground/55">{user.perfil}</div>
                    </div>
                    <Link
                        href={route('logout')}
                        method="post"
                        as="button"
                        title="Sair"
                        className="rounded-md p-1.5 text-sidebar-foreground/55 transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                    >
                        <LogOut className="size-4" />
                    </Link>
                </div>
            </div>
        </div>
    );
}

export default function InternalLayout({ title, pageTitle, breadcrumb, topbarActions, children }) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const atual = route().current() ?? '';

    return (
        <>
            <Head title={title} />
            <Toaster position="top-right" richColors />
            <FlashMessages />

            <aside className="fixed inset-y-0 left-0 z-40 hidden w-64 lg:block">
                <SidebarContent user={user} atual={atual} />
            </aside>

            <div className="flex min-h-dvh flex-col lg:pl-64">
                <div className="sticky top-0 z-30 flex items-center justify-between gap-3 border-b bg-background/85 px-4 py-3 backdrop-blur-md sm:px-6">
                    <div className="flex items-center gap-3">
                        <Sheet>
                            <SheetTrigger asChild>
                                <Button variant="outline" size="icon-sm" className="lg:hidden" aria-label="Abrir menu">
                                    <Menu />
                                </Button>
                            </SheetTrigger>
                            <SheetContent side="left" className="w-72 border-sidebar-border p-0">
                                <SidebarContent user={user} atual={atual} />
                            </SheetContent>
                        </Sheet>
                        <div>
                            <h1 className="text-sm font-bold tracking-tight sm:text-base">{pageTitle ?? 'Painel'}</h1>
                            {breadcrumb && <p className="text-xs text-muted-foreground">{breadcrumb}</p>}
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        {topbarActions}
                        <ThemeToggle className="text-muted-foreground" />
                    </div>
                </div>

                <div className="flex-1 px-4 py-6 sm:px-6">{children}</div>
            </div>
        </>
    );
}
