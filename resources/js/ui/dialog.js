/* <dialog> nativo: data-dialog-trigger="id" abre dialog[data-dialog="id"];
   data-dialog-close ou clique no backdrop fecha. iframe[data-src] dentro do
   dialog só carrega na primeira abertura. */
export function initDialogs(root = document) {
    root.querySelectorAll('[data-dialog-trigger]:not([data-wired])').forEach((trigger) => {
        trigger.dataset.wired = '1';
        trigger.addEventListener('click', () => {
            const dialog = document.querySelector(`dialog[data-dialog="${trigger.dataset.dialogTrigger}"]`);
            if (!dialog) return;

            dialog.querySelectorAll('iframe[data-src]:not([src])').forEach((iframe) => {
                iframe.src = iframe.dataset.src;
            });
            dialog.showModal();
        });
    });

    root.querySelectorAll('dialog[data-dialog]:not([data-wired-dialog])').forEach((dialog) => {
        dialog.dataset.wiredDialog = '1';
        dialog.querySelectorAll('[data-dialog-close]').forEach((botao) => {
            botao.addEventListener('click', () => dialog.close());
        });
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) dialog.close();
        });
    });
}
