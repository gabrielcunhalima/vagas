/*
 * Lista de formações do perfil: adicionar clona resources/views/candidato/perfil/_formacao.blade.php
 * (renderizado uma vez como <template> com índice __INDEX__); remover só tira a
 * linha do DOM. Nomes viram formacoes[N][campo] — PHP aceita índices com
 * lacunas sem problema, então remover no meio não exige reindexar o resto.
 */
export function initFormacoes(root = document) {
    const lista = root.querySelector('[data-formacoes-lista]:not([data-wired])');
    if (!lista) return;
    lista.dataset.wired = '1';

    const template = document.querySelector('[data-formacao-template]');
    const botaoAdicionar = document.querySelector('[data-adicionar-formacao]');
    let contador = lista.querySelectorAll('[data-formacao-linha]').length;

    function renumerar() {
        const linhas = lista.querySelectorAll('[data-formacao-linha]');
        linhas.forEach((linha, i) => {
            const numero = linha.querySelector('[data-formacao-numero]');
            if (numero) numero.textContent = String(i + 1);
            const botaoRemover = linha.querySelector('[data-remover-formacao]');
            botaoRemover?.classList.toggle('hidden', linhas.length <= 1);
        });
    }

    function ligarRemover(linha) {
        linha.querySelector('[data-remover-formacao]')?.addEventListener('click', () => {
            linha.remove();
            renumerar();
        });
    }

    lista.querySelectorAll('[data-formacao-linha]').forEach(ligarRemover);
    renumerar();

    botaoAdicionar?.addEventListener('click', () => {
        if (!template) return;
        const html = template.innerHTML.replaceAll('__INDEX__', String(contador));
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const linha = wrapper.firstElementChild;
        lista.appendChild(linha);
        ligarRemover(linha);
        window.initWidgets?.(linha);
        contador++;
        renumerar();
    });
}
