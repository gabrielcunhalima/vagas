## 1. Pré-requisito

- [x] 1.1 Arquivar a change `validacao-imediata-conta` para que `cadastro-candidato-conta` exista em `openspec/specs/` e possa receber a delta desta change
- [x] 1.2 Confirmar que a base de destino contém apenas dados de teste antes de qualquer migration destrutiva — 5 candidatos, 11 candidaturas (5 órfãs), 4 alertas, 48 vagas

## 2. Estrutura de dados aditiva

Passo 1 do Migration Plan — nada é removido, a aplicação continua funcionando.

- [x] 2.1 Migration: `candidatos.nome` passa a nullable
- [x] 2.2 Migration: criar `candidato_curriculos` (`candidato_id`, `path`, `nome_original`, `enviado_em`)
- [x] 2.3 Migration: adicionar `candidatos.curriculo_atual_id` referenciando `candidato_curriculos`
- [x] 2.4 Migration: criar `candidatura_eventos` (`candidatura_id`, `tipo`, `status_anterior`, `status_novo`, `autor_id`, `curriculo_id_vigente`, `observacao`, `ocorrido_em`)
- [x] 2.5 Migration: adicionar `conflito_interesse`, `conflito_interesse_detalhe` e `codigo_conduta_aceito_em` em `candidaturas`
- [x] 2.6 Migration: adicionar `vaga_alertas.candidato_id`
- [x] 2.7 Models `CandidatoCurriculo` e `CandidaturaEvento` com as relações correspondentes em `Candidato` e `Candidatura`

## 3. Perfil como fonte única

- [x] 3.1 Implementar o cálculo de completude do perfil em um único ponto do servidor, conforme a lista da spec `perfil-candidato-unico`
- [x] 3.2 Expor o estado de completude e a lista de pendências para a interface, sem duplicar o critério no cliente
- [x] 3.3 Substituir o currículo único do perfil pelo envio versionado: cada upload cria versão nova e atualiza `curriculo_atual_id`
- [x] 3.4 Remoção do currículo limpa a versão vigente e devolve o perfil ao estado incompleto
- [x] 3.5 Relações de `Candidatura` para ler nome, contato, formação, endereço e currículo do candidato

## 4. Cadastro mínimo

- [x] 4.1 `CandidatoRegistroRequest` reduzido a e-mail, senha, confirmação, CPF e consentimento LGPD
- [x] 4.2 `CandidatoRegistroController::store()` cria a conta apenas com esses campos, registra a data do consentimento e não exige código de conduta
- [x] 4.3 `Registro.jsx`: de wizard de 6 etapas para formulário único, preservando as validações imediatas de CPF, senha e confirmação já entregues por `validacao-imediata-conta`
- [x] 4.4 Adoção retroativa na criação da conta: candidaturas sem vínculo com CPF **e** e-mail coincidentes passam a pertencer à conta
- [x] 4.5 Informar ao candidato, após o cadastro, que existem candidaturas anteriores incorporadas — exibindo o conteúdo apenas após a verificação do e-mail

## 5. Verificação de e-mail por ato

- [x] 5.1 Remover `candidato.verified` do grupo de rotas em `routes/web.php`
- [x] 5.2 Aplicar `candidato.verified` apenas nas rotas de candidatura e de alertas
- [x] 5.3 Liberar perfil, envio de currículo, listagem de vagas e consulta de candidaturas para conta não verificada
- [x] 5.4 Aviso persistente de verificação pendente com reenvio, visível na área autenticada

## 6. Perfil: formulário que recebeu as etapas do cadastro

- [x] 6.1 `PerfilController` e `Candidato/Perfil/Edit.jsx` passam a cobrir dados pessoais, endereço, formação e acessibilidade
- [x] 6.2 Exibir o progresso de completude e as pendências desde o primeiro acesso à conta, não apenas quando houver bloqueio
- [x] 6.3 Remover `conflito_interesse`, `conflito_interesse_detalhe` e `codigo_conduta_aceito_em` do perfil

## 7. Candidatura vinculada à conta

- [x] 7.1 `/candidatura/{vaga}` passa a exigir autenticação, retomando a vaga pretendida após o login ou cadastro
- [x] 7.2 Bloquear o envio com perfil incompleto, apresentando os campos pendentes sem perder a vaga
- [x] 7.3 Tela de candidatura passa a exibir os dados do perfil para conferência, com aviso de que a edição salva na conta
- [x] 7.4 `InscricaoRequest` passa a validar apenas os campos próprios da candidatura: carta, conflito de interesse, detalhamento e aceite do código de conduta
- [x] 7.5 `InscricaoController::store()` grava as alterações direto no perfil, substituindo o write-back parcial atual
- [x] 7.6 Registrar o evento de submissão com a versão de currículo vigente
- [x] 7.7 Remover o caminho de candidatura anônima e marcar `/minhas-candidaturas` (consulta por CPF + e-mail) como legado

## 8. Visibilidade do coordenador

- [x] 8.1 `candidaturaResumo()` e as telas do coordenador passam a ler os dados pelo relacionamento com o candidato
- [x] 8.2 `Candidatura::busca()` passa a filtrar na relação em vez de nas colunas locais
- [x] 8.3 Mails de processo (`CandidaturaRecebidaMail`, `ConviteEntrevistaMail`, `AprovacaoMail`, `ReprovacaoMail`) passam a obter nome e e-mail do candidato
- [x] 8.4 Registrar evento a cada transição de status em `updateStatus`
- [x] 8.5 Exibir o histórico de transições na tela da candidatura
- [x] 8.6 Policy de decaimento de acesso: reprovada + 90 dias, aprovada sem decaimento, sem estado terminal + vaga encerrada há 90 dias
- [x] 8.7 Aplicar a policy na visualização, na listagem, na busca e no download de currículo
- [x] 8.8 Tela de registro do processo para candidatura com acesso decaído, explicando por que os dados pessoais não estão disponíveis
- [x] 8.9 Exibir a data da última atualização do perfil na ficha da candidatura

## 9. Alertas autenticados

- [x] 9.1 `/alertas` sai da área pública e passa a exigir autenticação e e-mail verificado
- [x] 9.2 `AlertaVagaController` passa a usar o e-mail da conta, sem aceitar endereço digitado
- [x] 9.3 `Publico/Alertas.jsx` remove o campo de e-mail e o consentimento avulso, preservando o layout 10-80-10 e a ordem de empilhamento definidos por `alertas-vagas`
- [x] 9.4 Manter o cancelamento por token funcionando sem autenticação
- [x] 9.5 Permitir reativação pelo candidato autenticado após cancelamento por link
- [x] 9.6 Vincular `vaga_alertas` ao candidato e garantir um alerta por conta

## 10. Anonimização

- [x] 10.1 `AnonimizacaoService` deixa de percorrer candidatura por candidatura
- [x] 10.2 Exclusão da conta anonimiza o perfil e remove os arquivos de todas as versões de currículo
- [x] 10.3 Candidaturas de conta excluída permanecem como registro de processo, sem expor dados pessoais
- [x] 10.4 `Candidato::dadosParaCandidatura()` removido

## 11. Migração de dados

Passo 2 do Migration Plan.

- [x] 11.1 Currículos existentes no perfil viram versão 1 e passam a ser a versão vigente
- [x] 11.2 Alertas existentes cujo e-mail casa com uma conta são vinculados a ela; os demais são desativados
- [x] 11.3 Candidaturas órfãs cujo CPF **e** e-mail já correspondem a uma conta existente são adotadas por ela

> As órfãs restantes **não** são removidas aqui: enquanto as colunas `cpf`/`email` de `candidaturas` existirem, quem se cadastrar depois do deploy ainda pode adotá-las pelo caminho do grupo 4. A remoção acontece no grupo 12, junto com o `NOT NULL` que encerra essa janela.

## 12. Corte das colunas duplicadas

Passo 4 do Migration Plan — ponto sem volta, **deploy separado**, apenas depois de os grupos 3 a 11 estarem em produção e estáveis.

> **Não executado nesta entrega, de propósito.** As migrations destrutivas nem sequer
> foram escritas: um arquivo de migration no repositório é executado por qualquer
> `php artisan migrate`, e isso encerraria antes da hora a janela de adoção
> retroativa — enquanto as colunas `cpf`/`email` de `candidaturas` existirem, quem
> se cadastrar ainda pode reivindicar candidaturas feitas sem conta.
>
> O passo 1 já deixou essas colunas nuláveis, então a aplicação não depende mais
> delas e os dois deploys são independentes. Este grupo é o gatilho do segundo.

- [x] 12.1 Verificar que nenhuma leitura da aplicação depende mais das colunas duplicadas de `candidaturas`
- [ ] 12.2 Migration: remover as candidaturas órfãs remanescentes e o caminho de adoção retroativa, que deixa de ter o que casar
- [ ] 12.3 Migration: remover de `candidaturas` as colunas de identidade, contato, formação, endereço e currículo
- [ ] 12.4 Migration: trocar `unique(vaga_id, cpf)` por `unique(vaga_id, candidato_id)` e tornar `candidato_id` obrigatório
- [ ] 12.5 Migration: remover `conflito_interesse`, `conflito_interesse_detalhe` e `codigo_conduta_aceito_em` de `candidatos`

## 15. Retenção de contas inativas

Substitui `vagas:anonimizar-candidaturas-antigas`, suspenso no grupo 10 por ter
perdido a premissa. A retenção **durante** o processo já é tratada pelo decaimento
de acesso; o que faltava era o fim da linha para contas que pararam de ser usadas.

**Política**: conta sem atividade há 2 anos e sem candidatura em aberto recebe aviso
por e-mail e é anonimizada 30 dias depois, se não houver retorno.

- [x] 15.1 Migration: `candidatos.ultimo_acesso_em` e `candidatos.aviso_inatividade_em`
- [x] 15.2 Registrar o acesso no login do candidato
- [x] 15.3 `Candidato::ultimaAtividadeEm()` — o mais recente entre acesso, edição do perfil e última candidatura
- [x] 15.4 `Candidato::temProcessoEmAberto()` — guarda que impede anonimizar quem ainda está concorrendo
- [x] 15.5 Notification de aviso prévio, com o prazo e como manter a conta
- [x] 15.6 Reescrever `vagas:anonimizar-candidaturas-antigas` como `vagas:anonimizar-contas-inativas`, em duas fases (avisar / anonimizar)
- [x] 15.7 Atualizar o agendamento em `routes/console.php`
- [x] 15.8 Testes: aviso enviado no prazo, anonimização só após a carência, retorno do candidato cancela o processo, processo em aberto protege a conta

## 13. Specs principais

- [x] 13.1 Reescrever o `## Purpose` de `openspec/specs/cadastro-candidato-conta/spec.md`, que ainda descreve a "etapa de credenciais" e o avanço "para as etapas seguintes" de um wizard que deixa de existir

## 14. Testes

- [x] 14.1 Cadastro mínimo cria conta autenticada com perfil incompleto e recusa envio sem consentimento
- [x] 14.2 Adoção retroativa incorpora com CPF **e** e-mail coincidentes, e não incorpora com apenas um deles
- [x] 14.3 Completude do perfil: telefone pendente bloqueia, semestre dispensado com curso concluído, campos opcionais em branco não bloqueiam
- [x] 14.4 Candidatura recusada sem autenticação, sem verificação e com perfil incompleto
- [x] 14.5 Edição de dado na candidatura altera o perfil e aparece nas demais candidaturas do candidato
- [x] 14.6 Currículo substituído passa a ser lido por todas as candidaturas, e a versão anterior permanece identificável pelos eventos
- [x] 14.7 Conflito de interesse é solicitado a cada vaga e o detalhamento é exigido quando declarado
- [x] 14.8 Decaimento de acesso nos quatro casos da policy, incluindo o bloqueio do download e da listagem
- [x] 14.9 Exclusão da conta remove dado sensível e todas as versões de currículo, preservando o registro do processo
- [x] 14.10 Alerta exige autenticação e verificação, usa o e-mail da conta, e o cancelamento por token dispensa login
