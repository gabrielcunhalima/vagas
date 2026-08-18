/*
 * A troca de classe `dark` + localStorage já existe em
 * `resources/views/partials/theme-script.blade.php` (roda antes do paint,
 * para não piscar). Este widget só liga os botões de alternância a essa
 * função global.
 */
export function initTema(root = document) {
    root.querySelectorAll('[data-tema-toggle]:not([data-wired])').forEach((botao) => {
        botao.dataset.wired = '1';
        botao.addEventListener('click', () => window.toggleTheme?.());
    });
}
