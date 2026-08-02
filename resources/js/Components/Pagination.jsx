import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { buttonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';

function NavArrow({ url, children, label }) {
    if (!url) {
        return (
            <span className={cn(buttonVariants({ variant: 'ghost', size: 'icon-sm' }), 'pointer-events-none opacity-40')}>
                {children}
            </span>
        );
    }
    return (
        <Link href={url} aria-label={label} className={buttonVariants({ variant: 'ghost', size: 'icon-sm' })}>
            {children}
        </Link>
    );
}

export default function Pagination({ paginator, className }) {
    if (!paginator || paginator.last_page <= 1) return null;

    const paginas = paginator.links.slice(1, -1);

    return (
        <nav className={cn('flex flex-wrap items-center justify-between gap-3', className)} aria-label="Paginação">
            <p className="text-xs text-muted-foreground">
                {paginator.from} a {paginator.to} de {paginator.total}
            </p>
            <div className="flex items-center gap-0.5">
                <NavArrow url={paginator.prev_page_url} label="Página anterior">
                    <ChevronLeft />
                </NavArrow>
                {paginas.map((link, i) =>
                    link.url ? (
                        <Link
                            key={i}
                            href={link.url}
                            className={cn(
                                buttonVariants({ variant: link.active ? 'secondary' : 'ghost', size: 'sm' }),
                                'min-w-7 px-2',
                                link.active && 'pointer-events-none font-semibold',
                            )}
                        >
                            {link.label}
                        </Link>
                    ) : (
                        <span key={i} className="px-1.5 text-sm text-muted-foreground">
                            …
                        </span>
                    ),
                )}
                <NavArrow url={paginator.next_page_url} label="Próxima página">
                    <ChevronRight />
                </NavArrow>
            </div>
        </nav>
    );
}
