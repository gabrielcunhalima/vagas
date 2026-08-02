import { SearchX } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function EmptyState({ icon: Icon = SearchX, title, description, children, className }) {
    return (
        <div className={cn('flex flex-col items-center justify-center px-6 py-16 text-center', className)}>
            <div className="flex size-12 items-center justify-center rounded-full bg-muted">
                <Icon className="size-5 text-muted-foreground" />
            </div>
            <h3 className="mt-4 text-sm font-semibold">{title}</h3>
            {description && <p className="mt-1 max-w-sm text-sm text-muted-foreground">{description}</p>}
            {children && <div className="mt-5 flex flex-wrap items-center justify-center gap-2">{children}</div>}
        </div>
    );
}
