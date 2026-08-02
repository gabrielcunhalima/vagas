import { Check } from 'lucide-react';
import { cn } from '@/lib/utils';

export const REGRAS_SENHA = [
    { re: /.{8,}/, label: '8+ caracteres' },
    { re: /[a-z]/, label: 'Minúscula' },
    { re: /[A-Z]/, label: 'Maiúscula' },
    { re: /\d/, label: 'Número' },
    { re: /[^A-Za-z0-9]/, label: 'Símbolo' },
];

export default function PasswordStrengthMeter({ password }) {
    if (!password) return null;

    const forca = REGRAS_SENHA.filter((r) => r.re.test(password)).length;

    return (
        <div className="mt-1 flex flex-col gap-1.5">
            <div className="flex gap-1">
                {REGRAS_SENHA.map((_, i) => (
                    <span
                        key={i}
                        className={cn(
                            'h-1 flex-1 rounded-full transition-colors',
                            i < forca
                                ? forca <= 2
                                    ? 'bg-red-500'
                                    : forca <= 4
                                      ? 'bg-amber-500'
                                      : 'bg-emerald-500'
                                : 'bg-muted',
                        )}
                    />
                ))}
            </div>
            <div className="flex flex-wrap gap-x-3 gap-y-1">
                {REGRAS_SENHA.map((r) => {
                    const ok = r.re.test(password);
                    return (
                        <span
                            key={r.label}
                            className={cn(
                                'inline-flex items-center gap-1 text-[0.7rem]',
                                ok ? 'font-medium text-emerald-600 dark:text-emerald-400' : 'text-muted-foreground',
                            )}
                        >
                            <Check className={cn('size-3', !ok && 'opacity-30')} />
                            {r.label}
                        </span>
                    );
                })}
            </div>
        </div>
    );
}
