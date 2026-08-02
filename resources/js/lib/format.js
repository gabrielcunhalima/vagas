import { maskCep, onlyDigits } from '@/lib/cpf';

const DAY_MS = 86_400_000;

/* Datas vêm do Laravel como ISO UTC ("2026-08-01T00:00:00.000000Z").
   Formatamos sempre em UTC para não deslocar o dia no fuso local. */

export function formatDate(value) {
    if (!value) return 'N/A';
    return new Intl.DateTimeFormat('pt-BR', { timeZone: 'UTC' }).format(new Date(value));
}

export function formatDateTime(value) {
    if (!value) return 'N/A';
    return new Intl.DateTimeFormat('pt-BR', {
        timeZone: 'UTC',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

export function toDateInput(value) {
    return value ? String(value).slice(0, 10) : '';
}

export function toDateTimeInput(value) {
    return value ? String(value).slice(0, 16) : '';
}

export function formatMoney(value) {
    if (value === null || value === undefined || value === '') return null;
    return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

export function faixaSalarial(vaga) {
    const min = formatMoney(vaga.remuneracao);
    const max = formatMoney(vaga.remuneracao_max);
    if (min && max) return `${min} a ${max}`;
    return min ?? 'A combinar';
}

export function diasRestantes(dataEncerramento) {
    if (!dataEncerramento) return 0;
    const fim = new Date(String(dataEncerramento).slice(0, 10) + 'T00:00:00Z').getTime();
    const hoje = new Date();
    const hojeUtc = Date.UTC(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());
    return Math.max(0, Math.round((fim - hojeUtc) / DAY_MS));
}

/* Rótulo de prazo da listagem pública. Último dia vira "Encerra hoje" — o
   `diasRestantes` satura em 0, então esse caso cobre também datas já vencidas. */
export function prazoInscricao(dataEncerramento) {
    const dias = diasRestantes(dataEncerramento);
    if (dias === 0) return 'Encerra hoje';
    if (dias <= 5) return `Encerra em ${dias} ${dias === 1 ? 'dia' : 'dias'}`;
    return `Inscrições até ${formatDate(dataEncerramento)}`;
}

export function isNova(vaga) {
    const ref = vaga.autorizada_em ?? vaga.created_at;
    if (!ref) return false;
    return Date.now() - new Date(ref).getTime() <= 3 * DAY_MS;
}

export function iniciais(nome) {
    if (!nome) return '?';
    const partes = nome.trim().split(/\s+/);
    return ((partes[0]?.[0] ?? '') + (partes[1]?.[0] ?? partes[0]?.[1] ?? '')).toUpperCase();
}

export function localVaga(vaga) {
    if (vaga.modalidade === 'remoto') return 'Remoto';
    if (vaga.cidade && vaga.estado) return `${vaga.cidade}/${vaga.estado}`;
    return vaga.cidade || vaga.local_trabalho || 'A definir';
}
