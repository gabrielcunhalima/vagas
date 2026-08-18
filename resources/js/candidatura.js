export function initCandidatura(root = document) {
    root.querySelectorAll('[data-conflito-select]:not([data-wired])').forEach((select) => {
        select.dataset.wired = '1';
        const detalhe = document.querySelector('[data-conflito-detalhe]');
        if (!detalhe) return;

        const atualizar = () => detalhe.classList.toggle('hidden', select.value !== '1');
        select.addEventListener('change', atualizar);
        atualizar();
    });
}
