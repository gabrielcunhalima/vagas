/*
 * Sem JS, o <input type="file"> nativo fica visível e funciona (só feio).
 * Com JS, escondemos o input nativo e assumimos a UI arrastar-e-soltar —
 * o próprio JS decide trocar de estado, nunca o Blade.
 */
function formatarTamanho(bytes) {
    return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
}

function atualizar(root, input, vazio, preview) {
    const arquivo = input.files?.[0];
    if (arquivo) {
        vazio.classList.add('hidden');
        preview.classList.remove('hidden');
        preview.querySelector('[data-curriculo-filename]').textContent = arquivo.name;
        preview.querySelector('[data-curriculo-filesize]').textContent = formatarTamanho(arquivo.size);
    } else {
        preview.classList.add('hidden');
        vazio.classList.remove('hidden');
    }
}

export function initCurriculoDropzone(root = document) {
    root.querySelectorAll('[data-curriculo-dropzone]:not([data-wired])').forEach((wrapper) => {
        wrapper.dataset.wired = '1';

        const input = wrapper.querySelector('[data-curriculo-input]');
        const vazio = wrapper.querySelector('[data-curriculo-empty]');
        const preview = wrapper.querySelector('[data-curriculo-preview]');
        const remover = wrapper.querySelector('[data-curriculo-remove]');
        if (!input || !vazio || !preview) return;

        input.classList.add('hidden');
        vazio.classList.remove('hidden');
        atualizar(wrapper, input, vazio, preview);

        vazio.addEventListener('click', () => input.click());
        vazio.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                input.click();
            }
        });

        vazio.addEventListener('dragover', (e) => {
            e.preventDefault();
            vazio.classList.add('border-primary', 'bg-accent/60');
        });
        vazio.addEventListener('dragleave', () => {
            vazio.classList.remove('border-primary', 'bg-accent/60');
        });
        vazio.addEventListener('drop', (e) => {
            e.preventDefault();
            vazio.classList.remove('border-primary', 'bg-accent/60');
            if (e.dataTransfer.files?.length) {
                input.files = e.dataTransfer.files;
                atualizar(wrapper, input, vazio, preview);
            }
        });

        input.addEventListener('change', () => atualizar(wrapper, input, vazio, preview));

        remover?.addEventListener('click', () => {
            input.value = '';
            atualizar(wrapper, input, vazio, preview);
        });
    });
}
