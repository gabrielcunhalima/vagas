import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

const tones = {
    primary: 'bg-primary/10 text-primary',
    amber: 'bg-amber-500/12 text-amber-600 dark:text-amber-400',
    blue: 'bg-blue-500/12 text-blue-600 dark:text-blue-400',
    sky: 'bg-sky-500/12 text-sky-600 dark:text-sky-400',
    violet: 'bg-violet-500/12 text-violet-600 dark:text-violet-400',
    red: 'bg-red-500/12 text-red-600 dark:text-red-400',
    neutral: 'bg-muted text-muted-foreground',
};

export default function StatCard({ icon: Icon, label, value, tone = 'primary', href, className }) {
    const conteudo = (
        <div
            className={cn(
                'flex items-center gap-3.5 rounded-xl bg-card p-4 ring-1 ring-foreground/10 transition-shadow',
                href && 'hover:shadow-md hover:ring-primary/35',
                className,
            )}
        >
            <div className={cn('flex size-10 shrink-0 items-center justify-center rounded-lg', tones[tone])}>
                <Icon className="size-5" />
            </div>
            <div className="min-w-0">
                <div className="text-2xl font-bold leading-none tracking-tight">{value}</div>
                <div className="mt-1 truncate text-xs font-medium text-muted-foreground">{label}</div>
            </div>
        </div>
    );

    return href ? <Link href={href}>{conteudo}</Link> : conteudo;
}
