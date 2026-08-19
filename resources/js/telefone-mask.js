import { maskTelefone } from './lib/cpf';

export function initTelefoneMask(root = document) {
    root.querySelectorAll('[data-telefone-input]:not([data-wired])').forEach((input) => {
        input.dataset.wired = '1';
        input.value = maskTelefone(input.value);
        input.addEventListener('input', () => {
            input.value = maskTelefone(input.value);
        });
    });
}
