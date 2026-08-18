/*
 * Off-canvas genérico (menu mobile, painel de detalhe de vaga no mobile).
 * `data-sheet-trigger="id"` abre `[data-sheet="id"]`; backdrop, botão
 * `[data-sheet-close]` e Esc fecham. Trava o scroll do body enquanto aberto.
 */
function abrir(sheet) {
    sheet.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    requestAnimationFrame(() => sheet.classList.add('sheet-aberto'));
}

function fechar(sheet) {
    sheet.classList.remove('sheet-aberto');
    document.body.classList.remove('overflow-hidden');
    setTimeout(() => sheet.classList.add('hidden'), 200);
}

export function initSheets(root = document) {
    root.querySelectorAll('[data-sheet-trigger]:not([data-wired])').forEach((trigger) => {
        trigger.dataset.wired = '1';
        trigger.addEventListener('click', () => {
            const sheet = document.querySelector(`[data-sheet="${trigger.dataset.sheetTrigger}"]`);
            if (sheet) abrir(sheet);
        });
    });

    root.querySelectorAll('[data-sheet]:not([data-wired-sheet])').forEach((sheet) => {
        sheet.dataset.wiredSheet = '1';

        sheet.querySelectorAll('[data-sheet-close]').forEach((btn) => {
            btn.addEventListener('click', () => fechar(sheet));
        });
        sheet.querySelector('[data-sheet-backdrop]')?.addEventListener('click', () => fechar(sheet));

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !sheet.classList.contains('hidden')) fechar(sheet);
        });
    });
}

export function fecharSheet(id) {
    const sheet = document.querySelector(`[data-sheet="${id}"]`);
    if (sheet) fechar(sheet);
}
