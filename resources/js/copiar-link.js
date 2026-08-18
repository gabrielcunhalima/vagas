export function initCopiarLink(root = document) {
    root.querySelectorAll('[data-copiar-link]:not([data-wired])').forEach((botao) => {
        botao.dataset.wired = '1';
        const label = botao.querySelector('[data-copiar-link-label]');
        if (!label) return;
        const textoOriginal = label.textContent;

        botao.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(window.location.href);
                label.textContent = 'Link copiado!';
            } catch {
                label.textContent = 'Não foi possível copiar';
            }
            setTimeout(() => {
                label.textContent = textoOriginal;
            }, 2000);
        });
    });
}
