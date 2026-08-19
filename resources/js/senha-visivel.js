/* Alterna type="password"/"text" no input indicado por data-senha-toggle="id".
   O botão traz dois ícones filhos (data-icon-mostrar/data-icon-ocultar); só um
   fica visível por vez. */
export function initSenhaVisivel(root = document) {
    root.querySelectorAll('[data-senha-toggle]:not([data-wired])').forEach((botao) => {
        botao.dataset.wired = '1';
        const alvo = document.getElementById(botao.dataset.senhaToggle);
        const iconeMostrar = botao.querySelector('[data-icon-mostrar]');
        const iconeOcultar = botao.querySelector('[data-icon-ocultar]');
        if (!alvo) return;

        botao.addEventListener('click', () => {
            const vaiMostrar = alvo.type === 'password';
            alvo.type = vaiMostrar ? 'text' : 'password';
            botao.setAttribute('aria-label', vaiMostrar ? 'Ocultar senha' : 'Mostrar senha');
            iconeMostrar?.classList.toggle('hidden', vaiMostrar);
            iconeOcultar?.classList.toggle('hidden', !vaiMostrar);
        });
    });
}
