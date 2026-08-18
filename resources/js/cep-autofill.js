import { maskCep, onlyDigits } from './lib/cpf';

/* CEP com máscara + preenchimento automático via /api/cep/{cep} ao perder o
   foco. Falha de rede não trava nada — o preenchimento manual segue valendo. */
export function initCepAutofill(root = document) {
    root.querySelectorAll('[data-cep-input]:not([data-wired])').forEach((input) => {
        input.dataset.wired = '1';
        const hint = document.querySelector(`[data-cep-hint-for="${input.id}"]`);

        input.addEventListener('input', () => {
            input.value = maskCep(input.value);
        });

        input.addEventListener('blur', async () => {
            const digitos = onlyDigits(input.value);
            if (digitos.length !== 8) return;

            if (hint) hint.textContent = 'Buscando endereço…';
            try {
                const resposta = await fetch(`${input.dataset.cepUrl}/${digitos}`);
                if (resposta.ok) {
                    const dados = await resposta.json();
                    if (dados && !dados.erro) {
                        const preencher = (campo, valor) => {
                            const alvo = document.getElementById(input.dataset[campo]);
                            if (alvo && valor) alvo.value = valor;
                        };
                        preencher('cepLogradouro', dados.logradouro);
                        preencher('cepBairro', dados.bairro);
                        preencher('cepCidade', dados.cidade);
                        preencher('cepEstado', dados.estado);
                    }
                }
            } catch {
                /* preenchimento manual segue disponível */
            } finally {
                if (hint) hint.textContent = '';
            }
        });
    });
}
