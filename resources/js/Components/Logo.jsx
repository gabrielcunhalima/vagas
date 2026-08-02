import { asset } from '@/lib/asset';
import { cn } from '@/lib/utils';

/*
 * Logo institucional (fapeulogobranca.png — arte branca com fundo transparente).
 * Dark mode e fundos escuros fixos (`white`): arte branca original.
 * Light mode: silhueta tingida com a cor primária (teal) via CSS mask.
 */
export default function Logo({ className, white = false }) {
    const src = asset('imagens/fapeulogobranca.png');

    if (white) {
        return <img src={src} alt="FAPEU" className={cn('w-auto flex-none', className)} />;
    }

    const mask = {
        aspectRatio: '291 / 497',
        maskImage: `url("${src}")`,
        WebkitMaskImage: `url("${src}")`,
        maskSize: 'contain',
        WebkitMaskSize: 'contain',
        maskRepeat: 'no-repeat',
        WebkitMaskRepeat: 'no-repeat',
        maskPosition: 'center',
        WebkitMaskPosition: 'center',
    };

    return (
        <>
            <span
                role="img"
                aria-label="FAPEU"
                className={cn('inline-block flex-none bg-primary dark:hidden', className)}
                style={mask}
            />
            <img src={src} alt="FAPEU" className={cn('hidden w-auto flex-none dark:block', className)} />
        </>
    );
}
