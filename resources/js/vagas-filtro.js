/*
 * Filtros de /vagas: selects aplicam a busca imediatamente ao mudar; texto e
 * faixa salarial só ao enviar o formulário (botão "Buscar"). Sem JS, tudo
 * cai no submit GET normal — o `action`/`method` do form já resolvem isso.
 * Com JS, troca só o HTML do resultado (lista + detalhe), preservando scroll.
 */
function paramsDoForm(form) {
    const params = new URLSearchParams();
    new FormData(form).forEach((valor, chave) => {
        if (valor !== '' && valor !== null) params.set(chave, valor);
    });
    return params;
}

async function aplicar(form, url) {
    const resultado = document.querySelector('[data-vagas-resultado]');
    if (!resultado) {
        window.location.href = url;
        return;
    }

    resultado.classList.add('opacity-60');
    try {
        const resposta = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!resposta.ok) throw new Error('resposta não ok');

        resultado.innerHTML = await resposta.text();
        window.history.pushState({}, '', url);
        window.initWidgets?.(resultado);
    } catch {
        window.location.href = url;
    } finally {
        resultado.classList.remove('opacity-60');
    }
}

export function initVagasFiltro(root = document) {
    const form = root.querySelector('[data-vagas-filtro]:not([data-wired])');
    if (!form) return;
    form.dataset.wired = '1';

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        aplicar(form, `${form.action}?${paramsDoForm(form)}`);
    });

    // Os campos ficam fora do <form> no DOM e se associam por form="filtro-vagas"
    // (o form em si é só um "porta-estado" vazio) — por isso a busca é no
    // document, não em form.querySelectorAll, que só acha descendentes.
    document.querySelectorAll(`[data-auto-apply][form="${form.id}"]`).forEach((campo) => {
        campo.addEventListener('change', () => aplicar(form, `${form.action}?${paramsDoForm(form)}`));
    });

    document.querySelectorAll('[data-vagas-limpar]').forEach((botao) => {
        botao.addEventListener('click', () => aplicar(form, form.action));
    });
}
