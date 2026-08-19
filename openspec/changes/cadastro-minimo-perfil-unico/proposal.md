## Why

Para existir uma conta hoje, o candidato precisa vencer 6 etapas e cerca de 20 campos obrigatórios — nome, nacionalidade, formação completa, acessibilidade, conflito de interesse e dois aceites — antes de qualquer retorno do portal. O esforço é cobrado no ponto em que a pessoa menos investiu, e quem desiste no meio não deixa nada: nem conta, nem e-mail, nem forma de ser reconhecido depois.

Ao mesmo tempo, os dados que ela preenche não têm dono único. `candidaturas` guarda uma **cópia congelada** de nome, CPF, e-mail, formação, endereço e currículo a cada inscrição. Isso significa que um currículo atualizado só aparece para quem receber a próxima candidatura, enquanto os coordenadores anteriores continuam avaliando um PDF velho — e que cada cópia é mais um lugar onde dado pessoal precisa ser apagado à mão. O `AnonimizacaoService` já demonstra o custo disso: ele enumera 15 campos e deixa `curso`, `instituicao`, `semestre`, `previsao_conclusao`, `pretensao_salarial`, `disponibilidade`, `pcd` e `pcd_tipo` para trás — inclusive dado sensível (art. 11 da LGPD) — sobrevivendo à exclusão da conta pelo titular.

As duas coisas se resolvem juntas: se o dado tem um dono único e vivo, a conta pode nascer pequena e crescer depois.

## What Changes

- **Cadastro mínimo**: criar conta passa a exigir apenas **e-mail, senha, CPF e consentimento LGPD**. O consentimento é obrigatório desde o primeiro passo porque o CPF é coletado ali. O aceite do código de conduta deixa de ser condição de conta.
- **Conflito de interesse e código de conduta passam a ser respondidos por candidatura** — **BREAKING**: hoje são campos da conta, respondidos uma vez no cadastro. Mas "você tem vínculo com alguém da equipe *desta* vaga?" tem resposta diferente a cada inscrição, e o aceite do código de conduta é um ato do processo seletivo. Ambos migram para a candidatura.
- **Perfil incompleto vira estado legítimo**. Depois do cadastro o candidato já está autenticado e pode navegar, preencher o perfil aos poucos e voltar quando quiser. Nada é cobrado até ele querer agir.
- **O wizard não morre, muda de lugar.** As etapas de dados pessoais, endereço, formação e acessibilidade saem da porta de entrada e passam a ser o formulário do perfil — o mesmo formulário exibido no momento da candidatura.
- **Perfil como fonte única e viva** — **BREAKING**: `candidaturas` deixa de guardar cópia de identidade, formação, endereço e currículo. Coordenadores de vagas diferentes leem sempre o mesmo registro do candidato. Editar os dados na tela de candidatura grava direto na conta.
- **Currículo versionado**: cada envio cria uma versão nova em vez de sobrescrever. O perfil aponta para a versão atual, que é a que todos leem; as anteriores ficam retidas para o registro dos processos que as julgaram.
- **Histórico de processo em vez de cópia de pessoa**: as transições de status passam a ser registradas (quem, quando, para qual status, com qual versão de currículo vigente). O registro descreve a **decisão**, não a identidade — nenhum campo pessoal é duplicado, então nenhum passivo novo de LGPD é criado.
- **Acesso do coordenador decai com o processo**: enquanto a candidatura está em andamento ele vê o perfil vivo. Reprovada, o acesso ao perfil cai **90 dias** após a decisão — carência que cobre a janela de contestação. Aprovada, o acesso é mantido, porque o coordenador ainda precisa dos dados para efetivar a contratação. Encerrado o acesso, resta apenas o registro do processo.
- **Verificação de e-mail passa a valer por ato, não por área**: navegar e preencher o próprio perfil ficam livres; **candidatar-se** e **ativar alertas** exigem e-mail verificado. Hoje o middleware `candidato.verified` bloqueia a área logada inteira, inclusive `/meus-dados`, o que devolveria na saída o atrito removido na entrada.
- **Candidatura exige login, e-mail verificado e perfil completo** — **BREAKING**: acaba a candidatura anônima. `candidato_id` deixa de ser opcional. A consulta pública por CPF + e-mail (`/minhas-candidaturas`) vira legado, já que existia para quem se inscreveu sem conta.
- **Adoção retroativa**: ao criar conta, candidaturas anteriores sem vínculo cujo CPF **e** e-mail coincidam com os da conta passam a pertencer a ela. Exigir os dois campos evita que apenas o CPF permita capturar histórico alheio.
- **Alertas de vaga exigem conta autenticada e verificada** — **BREAKING**: `vaga_alertas` passa a ser vinculado ao candidato em vez de chaveado por e-mail solto. O cancelamento por token continua funcionando sem login.
- **Anonimização colapsa**: apagar o perfil apaga o dado em toda parte, por construção. A lista de campos enumerados à mão — e a lacuna atual que preserva dado sensível — deixa de existir.
- **Retenção passa a ser por conta, não por candidatura**: a rotina diária `vagas:anonimizar-candidaturas-antigas` anonimizava a cópia que cada candidatura guardava; sem a cópia ela perde a premissa, e usar "a vaga encerrou" como régua apagaria os dados de quem ainda concorre a outras vagas. Ela dá lugar a `vagas:anonimizar-contas-inativas`: conta sem atividade por dois anos e sem candidatura em aberto recebe aviso por e-mail e é anonimizada trinta dias depois, e qualquer retorno cancela o processo.

## Capabilities

### New Capabilities

- `perfil-candidato-unico`: o perfil do candidato como fonte única e viva dos seus dados — o que o compõe, o que significa estar completo, como ele é editado a partir de qualquer contexto, versionamento de currículo e efeito da exclusão da conta.
- `candidatura-vinculada-a-conta`: pré-condições para se candidatar (autenticado, verificado, perfil completo), o que pertence à candidatura em vez do perfil, e adoção de candidaturas anteriores sem vínculo.
- `visibilidade-candidatura-coordenador`: o que o coordenador enxerga de uma candidatura enquanto o processo está aberto e depois de encerrado, e o registro das transições de status.

### Modified Capabilities

- `cadastro-candidato-conta`: os campos exigidos para criar conta passam a ser apenas e-mail, senha, CPF e consentimento LGPD — a capability deixa de descrever a "etapa de credenciais" de um wizard e passa a descrever o cadastro inteiro. Entram também o estado de e-mail não verificado como estado navegável e os atos que exigem verificação. O `## Purpose` da spec principal também precisa ser reescrito, por ainda descrever etapas de um wizard que deixa de existir — deltas não carregam Purpose, então é edição direta.
- `alertas-vagas`: passa a exigir conta autenticada e verificada, o destino deixa de ser um campo digitável e vira o e-mail da conta, e o consentimento LGPD sai da página por já ter sido concedido no cadastro. Os requisitos de layout que posicionavam o campo de e-mail e o consentimento são reescritos em função disso.

> **Sequenciamento**: as changes `validacao-imediata-conta` e `alertas-layout-10-80-10` já foram arquivadas, e suas specs `cadastro-candidato-conta` e `alertas-vagas` estão em `openspec/specs/`. As validações imediatas de CPF, senha e confirmação permanecem válidas — deixam de ser a etapa 0 e passam a ser o formulário todo. O layout 10-80-10 da página de alertas permanece; muda apenas o que ocupa a área de identificação.

## Impact

**Banco de dados** — o núcleo da mudança e a parte irreversível:
- `candidatos`: `nome` deixa de ser `NOT NULL` (hoje bloqueia o cadastro mínimo na primeira linha); ganha marcação de completude e ponteiro para a versão atual do currículo.
- `candidaturas`: remoção das colunas de identidade, formação, endereço e currículo; `candidato_id` passa a `NOT NULL`; a unicidade migra de `(vaga_id, cpf)` para `(vaga_id, candidato_id)`.
- `candidaturas` ganha `conflito_interesse`, `conflito_interesse_detalhe` e `codigo_conduta_aceito_em`, que saem de `candidatos`.
- Nova tabela de versões de currículo e nova tabela de eventos de candidatura.
- `vaga_alertas`: vínculo com `candidatos`.
- **Migração de dados**: a base tem apenas dados de teste, então as migrations podem derrubar e recriar colunas sem estratégia de consolidação de snapshots. Não há regra de precedência a definir.

**Aplicação**:
- `CandidatoRegistroController` / `CandidatoRegistroRequest` — reduzidos ao cadastro mínimo.
- `Registro.jsx` — de wizard de 6 etapas para formulário único.
- `PerfilController` / `Candidato/Perfil/Edit.jsx` — passam a receber as etapas que saíram do cadastro e a ser o formulário reaproveitado na candidatura.
- `InscricaoController` / `InscricaoRequest` / `Publico/Candidatura.jsx` — passam a exigir login, a ler do perfil e a gravar nele; o write-back parcial que já existe em `InscricaoController` é substituído por escrita direta.
- `CandidaturaController` (coordenador) — passa a ler do perfil via relação e a respeitar o decaimento de acesso.
- `AlertaVagaController` / `Publico/Alertas.jsx` — exigem autenticação.
- `AnonimizacaoService` — deixa de anonimizar candidatura por candidatura.
- Middleware `candidato.verified` — deixa de cobrir a área logada inteira; passa a ser aplicado por rota.
- `Candidato::dadosParaCandidatura()` — perde a razão de existir na forma atual.
- `routes/web.php` — `/candidatura/{vaga}` e `/alertas` saem da área pública.

**Fora de escopo**: layout e tipografia (cobertos por `alertas-layout-10-80-10` e `aumentar-fontes-2px`); o painel do coordenador em `www/coordenador_c`.

## Decisões tomadas

1. **Perfil completo** = os campos hoje obrigatórios no cadastro (nome, nacionalidade, nível de escolaridade, situação do curso, curso, instituição, previsão de conclusão, semestre quando cursando, resposta de acessibilidade) **mais telefone**, **mais currículo**. Endereço, nome social, LinkedIn, pretensão salarial e disponibilidade permanecem opcionais.
2. **Conflito de interesse e código de conduta migram para a candidatura**, respondidos a cada inscrição.
3. **Decaimento de acesso**: 90 dias após a decisão para candidatura reprovada; sem decaimento para aprovada.
4. **Migração**: base só com dados de teste — sem consolidação de snapshots.

## Questão em aberto

**Critérios de elegibilidade por dado do perfil.** Se alguma vaga corta candidatos por semestre, situação de curso ou dado equivalente, esse valor precisa ser registrado no evento de submissão como critério avaliado — do contrário não há como sustentar a decisão depois que o candidato editar o perfil. Nenhuma vaga hoje declara critério assim; se isso mudar, o evento de submissão é o lugar de registrar.
