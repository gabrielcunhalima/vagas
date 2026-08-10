import { Moon, Sun } from 'lucide-react';
import { Button } from '@/components/ui/button';

export default function ThemeToggle({ className, size = 'icon', iconClassName = '' }) {
    function toggle() {
        const isDark = !document.documentElement.classList.contains('dark');
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    return (
        <Button
            type="button"
            variant="ghost"
            size={size}
            onClick={toggle}
            className={className}
            aria-label="Alternar tema claro/escuro"
        >
            <Sun className={`dark:hidden ${iconClassName}`} />
            <Moon className={`hidden dark:block ${iconClassName}`} />
        </Button>
    );
}
