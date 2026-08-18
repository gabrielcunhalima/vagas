/*
 * Multi-seleção simples via <input list="..."> (datalist nativo) + chips.
 * Sem framework de combobox: digitar um valor da lista e apertar Enter (ou
 * escolher na sugestão nativa do navegador) adiciona um chip com um
 * <input type="hidden"> próprio, que é o que realmente vai no submit.
 */
export function initMultiSelect(root = document) {
    root.querySelectorAll('[data-multi-select]:not([data-wired])').forEach((wrapper) => {
        wrapper.dataset.wired = '1';

        const input = wrapper.querySelector('[data-multi-select-input]');
        const chips = wrapper.querySelector('[data-multi-select-chips]');
        const nome = wrapper.dataset.multiSelectName;
        if (!input || !chips || !nome) return;

        function valoresAtuais() {
            return [...chips.querySelectorAll('input[type="hidden"]')].map((i) => i.value);
        }

        function adicionar(valor) {
            valor = valor.trim();
            if (!valor || valoresAtuais().includes(valor)) return;

            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1 rounded-4xl bg-secondary py-0.5 pl-2.5 pr-1 text-xs font-medium text-secondary-foreground';
            chip.innerHTML = `${valor} <button type="button" class="cursor-pointer rounded-full p-0.5 hover:bg-foreground/10" aria-label="Remover ${valor}"><svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button><input type="hidden" name="${nome}[]" value="${valor}">`;
            chip.querySelector('button').addEventListener('click', () => chip.remove());
            chips.appendChild(chip);
        }

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                adicionar(input.value);
                input.value = '';
            }
        });

        input.addEventListener('change', () => {
            if (input.value) {
                adicionar(input.value);
                input.value = '';
            }
        });
    });
}
