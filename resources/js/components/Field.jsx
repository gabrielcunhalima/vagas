import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

export default function Field({ label, htmlFor, error, success, hint, required, className, children }) {
    return (
        <div className={cn('flex flex-col gap-1.5', className)}>
            {label && (
                <Label htmlFor={htmlFor}>
                    {label}
                    {required && <span className="-ml-0.5 text-destructive">*</span>}
                </Label>
            )}
            {children}
            {error ? (
                <p className="text-xs font-medium text-destructive">{error}</p>
            ) : success ? (
                <p className="text-xs font-medium text-emerald-600 dark:text-emerald-400">{success}</p>
            ) : hint ? (
                <p className="text-xs text-muted-foreground">{hint}</p>
            ) : null}
        </div>
    );
}
