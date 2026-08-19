/*
 * Mostra/esconde um bloco conforme o valor de outro campo do formulário.
 * `data-mostrar-se="idDoCampo=valorEsperado"` — funciona com select, radio e
 * checkbox (checkbox usa "1"/"0"). Sem JS o bloco fica sempre visível.
 */
export function initCamposCondicionais(root = document) {
    root.querySelectorAll('[data-mostrar-se]:not([data-wired])').forEach((alvo) => {
        alvo.dataset.wired = '1';
        const [idCampo, valorEsperado] = alvo.dataset.mostrarSe.split('=');
        const campo = document.getElementById(idCampo);
        if (!campo) return;

        function atualizar() {
            const valorAtual = campo.type === 'checkbox' ? (campo.checked ? '1' : '0') : campo.value;
            alvo.classList.toggle('hidden', valorAtual !== valorEsperado);
        }

        campo.addEventListener('change', atualizar);
        campo.addEventListener('input', atualizar);
        atualizar();
    });
}
