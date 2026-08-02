import { Head, Link } from '@inertiajs/react';
import { Toaster } from '@/components/ui/sonner';
import FlashMessages from '@/components/FlashMessages';
import Logo from '@/components/Logo';
import ThemeToggle from '@/components/ThemeToggle';
import { asset } from '@/lib/asset';
import { cn } from '@/lib/utils';

/*
 * Layout de autenticação split-screen:
 * painel esquerdo com foto + overlay de 50% e texto branco; formulário à direita.
 */
export default function AuthLayout({ title, headline, sub, wide = false, children }) {
    return (
        <>
            <Head title={title} />
            <Toaster position="top-right" richColors />
            <FlashMessages />

            <div className={cn('grid min-h-dvh', wide ? 'lg:grid-cols-[2fr_3fr]' : 'lg:grid-cols-[1.1fr_1fr]')}>
                <div className="relative hidden overflow-hidden lg:block">
                    <img
                        src={asset('imagens/auth-hero.jpg')}
                        alt=""
                        className="absolute inset-0 size-full object-cover"
                    />
                    <div className="absolute inset-0 bg-black/50" />
                    <div className="absolute inset-0 bg-gradient-to-t from-brand-deep/85 via-transparent to-black/20" />

                    <div className="relative z-10 flex h-full flex-col justify-between p-10">
                        <Link href={route('home')} className="w-fit">
                            <Logo white className="h-30" />
                        </Link>

                        <div className="max-w-md">
                            <h2 className="text-3xl font-bold leading-tight tracking-tight text-white xl:text-4xl">
                                {headline}
                            </h2>
                            {sub && <p className="mt-3 text-sm leading-relaxed text-white/80">{sub}</p>}
                        </div>

                        <p className="text-xs text-white/60">
                            © {new Date().getFullYear()} FAPEU, Portal de Vagas
                        </p>
                    </div>
                </div>

                <div className="flex flex-col">
                    <div className="flex items-center justify-between px-5 py-4">
                        <Link href={route('home')} className="lg:invisible">
                            <Logo className="h-8" />
                        </Link>
                        <ThemeToggle className="text-muted-foreground" />
                    </div>

                    <div className="flex flex-1 items-start justify-center px-5 pb-12 pt-4 sm:items-center sm:pt-0">
                        <div className={cn('w-full', wide ? 'max-w-2xl' : 'max-w-sm')}>{children}</div>
                    </div>
                </div>
            </div>
        </>
    );
}
