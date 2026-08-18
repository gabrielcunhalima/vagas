import { maskCpf, onlyDigits, validarCpf } from './lib/cpf';

/*
 * Máscara + verificação de CPF em tempo real no cadastro. A validação final é
 * sempre do servidor (CandidatoRegistroRequest); isto é só feedback antecipado
 * — sem JS, o campo aceita digitação livre e o envio ainda funciona.
 */
export function initCadastroCpf(root = document) {
    root.querySelectorAll('[data-cpf-input]:not([data-wired])').forEach((input) => {
        input.dataset.wired = '1';
        const feedback = document.querySelector(`[data-cpf-feedback-for="${input.id}"]`);
        const hintDuplicado = document.querySelector(`[data-cpf-duplicado-hint-for="${input.id}"]`);
        const urlBase = input.dataset.cpfCheckUrl;
        let abortController = null;
        let timer = null;

        function mostrar(texto, tom) {
            hintDuplicado?.setAttribute('hidden', '');
            if (!feedback) return;
            feedback.textContent = texto ?? '';
            feedback.classList.remove('text-destructive', 'text-emerald-600', 'dark:text-emerald-400', 'text-muted-foreground');
            if (tom === 'erro') feedback.classList.add('text-destructive');
            else if (tom === 'ok') feedback.classList.add('text-emerald-600', 'dark:text-emerald-400');
            else feedback.classList.add('text-muted-foreground');
        }

        function mostrarDuplicado() {
            if (feedback) feedback.textContent = '';
            hintDuplicado?.removeAttribute('hidden');
        }

        input.addEventListener('input', () => {
            input.value = maskCpf(input.value);
            const digitos = onlyDigits(input.value);

            abortController?.abort();
            clearTimeout(timer);

            if (digitos.length < 11) {
                mostrar('', null);
                return;
            }
            if (!validarCpf(digitos)) {
                mostrar('CPF inválido.', 'erro');
                return;
            }
            if (!urlBase) return;

            mostrar('Verificando CPF…', null);
            abortController = new AbortController();
            timer = setTimeout(async () => {
                try {
                    const resposta = await fetch(`${urlBase}?cpf=${digitos}`, {
                        signal: abortController.signal,
                        headers: { Accept: 'application/json' },
                    });
                    if (!resposta.ok) throw new Error('indisponível');
                    const dados = await resposta.json();
                    if (dados.existe) mostrarDuplicado();
                    else mostrar('CPF disponível.', 'ok');
                } catch (e) {
                    if (e.name !== 'AbortError') mostrar('', null);
                }
            }, 400);
        });
    });
}
