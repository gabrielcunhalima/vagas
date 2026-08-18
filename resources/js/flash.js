/*
 * Sem JS, a mensagem fica fixa na tela (ainda legível, só não some sozinha).
 * Com JS, garantimos o botão de fechar e o auto-dismiss.
 */
export function initFlash(root = document) {
    root.querySelectorAll('[data-flash-toast]:not([data-wired])').forEach((toast) => {
        toast.dataset.wired = '1';

        const remover = () => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 200);
        };

        toast.querySelector('[data-flash-close]')?.addEventListener('click', remover);
        toast.style.transition = 'opacity 0.2s ease';
        setTimeout(remover, 6000);
    });
}
