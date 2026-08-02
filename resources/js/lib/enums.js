export const tiposLabel = {
    estagio: 'Estágio',
    emprego: 'CLT',
    bolsa: 'Bolsa',
};

export const modalidadesLabel = {
    presencial: 'Presencial',
    remoto: 'Remoto',
    hibrido: 'Híbrido',
};

export const statusVagaLabel = {
    rascunho: 'Rascunho',
    aguardando_autorizacao: 'Aguardando autorização',
    ativa: 'Ativa',
    encerrada: 'Encerrada',
    recusada: 'Recusada',
    inativa: 'Inativa',
};

export const statusCandidaturaLabel = {
    recebida: 'Recebida',
    em_analise: 'Em análise',
    entrevista: 'Entrevista',
    aprovado: 'Aprovado',
    reprovado: 'Reprovado',
};

export const proximosStatusCandidatura = {
    recebida: ['em_analise', 'entrevista', 'reprovado'],
    em_analise: ['entrevista', 'reprovado'],
    entrevista: ['aprovado', 'reprovado'],
    aprovado: [],
    reprovado: [],
};

export const niveisEscolaridade = {
    medio: 'Ensino médio',
    tecnico: 'Técnico',
    graduacao: 'Graduação',
    pos: 'Pós-graduação',
    mestrado: 'Mestrado',
    doutorado: 'Doutorado',
};

export const disponibilidades = ['Imediata', '15 dias', '30 dias', '60 dias'];

export const ufs = [
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS',
    'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC',
    'SP', 'SE', 'TO',
];
