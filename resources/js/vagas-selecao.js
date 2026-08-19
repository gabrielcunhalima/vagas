import { abrirSheet } from './ui/sheet';

/*
 * Master-detail da listagem pública de vagas. Sem fetch ao selecionar: os
 * painéis de detalhe de todas as vagas da página já vêm renderizados
 * (escondidos) no HTML — só troca qual fica visível.
 *
 * >= xl (80rem): o painel de detalhe fixo na terceira coluna.
 * < xl: clicar num item copia o painel correspondente para dentro do
 * off-canvas "vaga-detalhe" e o abre — mesmo comportamento do Sheet do
 * Radix, sem portal.
 */
export function initVagasSelecao(root = document) {
    const lista = root.querySelector('[data-vaga-lista]:not([data-wired])');
    if (!lista) return;
    lista.dataset.wired = '1';

    const detalhes = [...document.querySelectorAll('[data-vaga-detalhe-id]')];
    const itens = [...lista.querySelectorAll('[data-vaga-item]')];
    const mobileConteudo = document.querySelector('[data-vaga-detalhe-mobile]');
    const mq = window.matchMedia('(min-width: 80rem)');

    function selecionar(id) {
        itens.forEach((el) => {
            const ativa = el.dataset.vagaId === String(id);
            el.setAttribute('aria-current', ativa ? 'true' : 'false');
            el.classList.toggle('bg-accent', ativa);
            el.classList.toggle('hover:bg-muted/60', !ativa);
            el.querySelector('h3')?.classList.toggle('text-primary', ativa);
        });

        const painel = detalhes.find((el) => el.dataset.vagaDetalheId === String(id));
        if (!painel) return;

        if (mq.matches) {
            detalhes.forEach((el) => el.classList.toggle('hidden', el !== painel));
            return;
        }

        if (mobileConteudo) {
            mobileConteudo.innerHTML = '';
            const clone = painel.cloneNode(true);
            clone.classList.remove('hidden');
            mobileConteudo.appendChild(clone);
            window.initWidgets?.(mobileConteudo);
            abrirSheet('vaga-detalhe');
        }
    }

    itens.forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            selecionar(el.dataset.vagaId);
        });
    });
}
