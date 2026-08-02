import { useEffect, useState } from 'react';

/*
 * Casa uma media query em JS, para casos em que a classe utilitária não resolve —
 * conteúdo portalado (Sheet/Dialog) não obedece a `xl:hidden` no wrapper.
 */
export default function useMediaQuery(query) {
    const [combina, setCombina] = useState(
        () => typeof window !== 'undefined' && window.matchMedia(query).matches,
    );

    useEffect(() => {
        const mq = window.matchMedia(query);
        const aoMudar = (e) => setCombina(e.matches);

        setCombina(mq.matches);
        mq.addEventListener('change', aoMudar);
        return () => mq.removeEventListener('change', aoMudar);
    }, [query]);

    return combina;
}
