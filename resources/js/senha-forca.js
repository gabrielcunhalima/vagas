export const REGRAS_SENHA = [
    { re: /.{8,}/, label: '8+ caracteres' },
    { re: /[a-z]/, label: 'Minúscula' },
    { re: /[A-Z]/, label: 'Maiúscula' },
    { re: /\d/, label: 'Número' },
    { re: /[^A-Za-z0-9]/, label: 'Símbolo' },
];

function render(saida, senha) {
    if (!senha) {
        saida.innerHTML = '';
        saida.classList.add('hidden');
        return;
    }
    saida.classList.remove('hidden');

    const forca = REGRAS_SENHA.filter((r) => r.re.test(senha)).length;
    const corBarra = forca <= 2 ? 'bg-red-500' : forca <= 4 ? 'bg-amber-500' : 'bg-emerald-500';

    const barras = REGRAS_SENHA.map(
        (_, i) => `<span class="h-1 flex-1 rounded-full transition-colors ${i < forca ? corBarra : 'bg-muted'}"></span>`,
    ).join('');

    const checklist = REGRAS_SENHA.map((r) => {
        const ok = r.re.test(senha);
        const cor = ok ? 'font-medium text-emerald-600 dark:text-emerald-400' : 'text-muted-foreground';
        const opacidade = ok ? '' : 'opacity-30';
        return `<span class="inline-flex items-center gap-1 text-[0.825rem] ${cor}">
            <svg class="size-3 ${opacidade}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            ${r.label}
        </span>`;
    }).join('');

    saida.innerHTML = `<div class="flex gap-1">${barras}</div><div class="mt-1.5 flex flex-wrap gap-x-3 gap-y-1">${checklist}</div>`;
}

export function initSenhaForca(root = document) {
    root.querySelectorAll('[data-senha-forca-input]:not([data-wired])').forEach((input) => {
        input.dataset.wired = '1';
        const alvo = input.dataset.senhaForcaInput;
        const saida = alvo ? document.getElementById(alvo) : input.parentElement?.querySelector('[data-senha-forca-output]');
        if (!saida) return;

        render(saida, input.value);
        input.addEventListener('input', () => render(saida, input.value));
    });
}
