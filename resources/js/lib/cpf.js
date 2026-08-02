export function onlyDigits(value) {
    return String(value ?? '').replace(/\D/g, '');
}

/* Algoritmo de dígitos verificadores da Receita Federal */
export function validarCpf(cpf) {
    const digits = onlyDigits(cpf);
    if (digits.length !== 11 || /^(\d)\1{10}$/.test(digits)) return false;

    for (const fim of [9, 10]) {
        let soma = 0;
        for (let i = 0; i < fim; i++) {
            soma += Number(digits[i]) * (fim + 1 - i);
        }
        const dv = ((soma * 10) % 11) % 10;
        if (dv !== Number(digits[fim])) return false;
    }
    return true;
}

export function maskCpf(value) {
    return onlyDigits(value)
        .slice(0, 11)
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d{1,2})$/, '.$1-$2');
}

export function maskCep(value) {
    return onlyDigits(value)
        .slice(0, 8)
        .replace(/(\d{5})(\d)/, '$1-$2');
}

export function maskTelefone(value) {
    const d = onlyDigits(value).slice(0, 11);
    if (d.length <= 10) {
        return d.replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{4})(\d{1,4})$/, '$1-$2');
    }
    return d.replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d{1,4})$/, '$1-$2');
}
