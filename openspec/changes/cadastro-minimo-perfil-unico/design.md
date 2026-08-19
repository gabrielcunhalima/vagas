## Context

Ver `proposal.md` — Why. O que importa aqui é a forma atual dos dados.

`candidaturas` nasceu antes de `candidatos`: era uma tabela autossuficiente que guardava a ficha inteira de quem se inscrevia, sem conta nenhuma. Quando `candidatos` foi introduzida (migration `2026_06_17_100000`), ela não substituiu aquela ficha — foi acrescentada ao lado, com `candidato_id` nullable. O resultado é que os mesmos 15 campos existem nos dois lugares e ninguém é a fonte:

```
candidatos.curriculo_path ──?── candidaturas.curriculo_path
candidatos.curso          ──?── candidaturas.curso
candidatos.cep            ──?── candidaturas.cep         ... ×15
```

`InscricaoController::store()` já tenta reconciliar isso à mão, copiando alguns campos da candidatura de volta para o perfil com `array_filter`. É meio caminho, só de ida, e só para 8 dos 15 campos.

Restrições que moldam o desenho:

- A base tem apenas dados de teste. Migração pode derrubar colunas sem consolidação.
- `candidatos.nome` é `NOT NULL` — trava o cadastro mínimo na primeira linha.
- `candidaturas` tem `unique(vaga_id, cpf)`, e `cpf` é uma das colunas que saem.
- O middleware `candidato.verified` cobre a área logada inteira em `routes/web.php`.
- `AnonimizacaoService` enumera campos à mão e já está desatualizado em relação ao schema.

## Goals / Non-Goals

**Goals:**

- Uma única fonte de dado pessoal do candidato, sem cópia derivada em lugar nenhum.
- Auditoria do processo seletivo sem duplicar dado pessoal.
- Completude do perfil calculada em um lugar só, usada tanto para bloquear quanto para exibir progresso.
- Autorização de leitura do coordenador decidida no servidor, não na view.

**Non-Goals:**

- Reescrever o painel do coordenador. Ele passa a ler de outro lugar; a interface permanece.
- Fluxo de recuperação de senha, login e SSO — intocados.
- `www/coordenador_c` — fora deste repositório.
- Layout e tipografia — cobertos por outras changes em andamento.

## Decisions

### 1. Perfil vivo, sem snapshot de identidade

`candidaturas` perde `nome`, `email`, `cpf`, `telefone`, `curso`, `instituicao`, `semestre`, `previsao_conclusao`, `linkedin`, endereço completo, `pcd`, `pcd_tipo`, `curriculo_path` e `curriculo_nome_original`. Passa a alcançar tudo isso por `candidato_id`, que vira `NOT NULL`.

**Por que não snapshot.** A alternativa óbvia — congelar a ficha no envio — foi descartada por dois motivos. O primeiro é o requisito: coordenadores de vagas diferentes precisam ver o mesmo currículo. O segundo é que o snapshot já está cobrando seu preço: `AnonimizacaoService::anonimizarCandidatura()` zera 15 campos e deixa `curso`, `instituicao`, `semestre`, `previsao_conclusao`, `pretensao_salarial`, `disponibilidade`, `pcd` e `pcd_tipo` intactos após a exclusão da conta — `pcd`/`pcd_tipo` são dado sensível sob o art. 11 da LGPD. Toda cópia é uma lista que alguém precisa manter em dia, e essa já atrasou.

**Por que não snapshot só para auditoria** (variante "lê do perfil, guarda cópia por garantia"): recria exatamente o passivo acima, com a agravante de ser invisível na interface — dado pessoal parado numa coluna que ninguém lê e por isso ninguém lembra de apagar.

A unicidade migra de `unique(vaga_id, cpf)` para `unique(vaga_id, candidato_id)`. Como `candidatos.cpf` é único, a garantia contra dupla inscrição é preservada.

### 2. Currículo versionado, e o perfil aponta para a versão atual

Nova tabela `candidato_curriculos` (`candidato_id`, `path`, `nome_original`, `enviado_em`). `candidatos.curriculo_atual_id` referencia a vigente.

```
candidato ──curriculo_atual_id──► v3  ← todos leem esta
                                  v2  ┐ retidas: um processo julgou
                                  v1  ┘ o PDF daquela época
```

Sem isso, "versão vigente no momento da decisão" registrada num evento apontaria para um arquivo que já foi sobrescrito — o registro existiria e não significaria nada. Substituir o currículo passa a ser inserir versão nova, nunca sobrescrever arquivo.

Alternativa considerada: manter um único arquivo e aceitar a perda do histórico. Descartada porque esvazia o registro de eventos, que é a contrapartida de ter abandonado o snapshot.

### 3. Eventos de candidatura registram a decisão, não a pessoa

Nova tabela `candidatura_eventos` (`candidatura_id`, `tipo`, `status_anterior`, `status_novo`, `autor_id`, `curriculo_id_vigente`, `observacao`, `ocorrido_em`).

A regra que mantém isso limpo: **nenhum campo de identidade entra aqui**. Nome, CPF, e-mail e endereço nunca são copiados para o evento. O que o evento responde é *quem decidiu o quê, quando, e sobre qual versão de currículo* — informação de processo, não de pessoa. Por isso a tabela não precisa entrar na rotina de anonimização: ela já não contém dado pessoal do candidato, e `autor_id` é o coordenador.

Eventos são gravados na submissão e em cada transição de status. Isso também substitui a ausência atual de qualquer histórico — hoje `updateStatus` sobrescreve o status sem deixar rastro.

### 4. Decaimento de acesso apoiado nos eventos

O evento de decisão dá o carimbo temporal de que o decaimento precisa. A regra:

```
candidatura reprovada          → perfil vivo visível até 90 dias após o evento de decisão
candidatura aprovada           → perfil vivo permanece visível (contratação em curso)
candidatura em andamento       → perfil vivo visível
sem estado terminal, vaga      → perfil vivo visível até 90 dias após o encerramento
  encerrada há mais de 90 dias    da vaga
```

A quarta linha é um julgamento meu, não uma resposta dada: sem ela, uma candidatura abandonada em `recebida` numa vaga encerrada nunca perderia o acesso, e o decaimento viraria contornável por inércia. Está marcada na spec para revisão.

Encerrado o acesso, o coordenador vê o registro do processo — vaga, datas, status, eventos, entrevista — e não os dados atuais do candidato.

**Onde isso é imposto.** Numa policy consultada pelo controller, não na view. O decaimento precisa valer também para o download de currículo e para qualquer listagem, e a listagem de candidaturas do coordenador hoje monta o resumo direto do model.

### 5. Completude do perfil em um lugar só

Um único ponto no servidor decide se um perfil está completo, e ele é a origem tanto do bloqueio quanto da barra de progresso da interface. A lista:

```
COMPLETO exige:  nome, nacionalidade, telefone, nível de escolaridade,
                 situação do curso, curso, instituição, previsão de conclusão,
                 semestre (somente se situação = cursando),
                 resposta de acessibilidade, currículo

OPCIONAL:        nome social, LinkedIn, endereço completo,
                 pretensão salarial, disponibilidade
```

Duas listas divergindo — uma no `FormRequest`, outra no componente React — é o modo de falha previsível aqui: a interface libera o botão e o servidor recusa, ou o contrário. O cálculo é servidor, e a interface recebe o resultado.

### 6. Verificação de e-mail por ato, não por área

`candidato.verified` sai do grupo de rotas e passa a ser aplicado por rota:

```
sem verificação   navegar vagas, ver e editar o próprio perfil,
                  enviar currículo, ver candidaturas anteriores

com verificação   candidatar-se, criar ou alterar alerta
```

Editar o próprio perfil não tem efeito externo — bloquear isso devolveria na saída o atrito que o cadastro mínimo removeu na entrada. Já o alerta dispara e-mail: sem verificação, qualquer um cadastra o e-mail de terceiro e o portal vira o remetente do spam. Candidatar-se é ato com consequência para o coordenador, e o e-mail é o canal do processo.

### 7. Adoção retroativa exige CPF **e** e-mail

Ao criar conta, candidaturas com `candidato_id` nulo são adotadas apenas quando CPF **e** e-mail coincidem com os da conta nova. Só CPF permitiria a quem conhece o CPF alheio — dado amplamente vazado — capturar o histórico de outra pessoa. A adoção acontece na criação da conta, antes da verificação de e-mail, mas o conteúdo adotado só é exibido após verificar.

Candidaturas órfãs que não casarem por ambos os campos permanecem sem dono. Como `candidato_id` passa a ser `NOT NULL`, elas precisam de destino na migração — ver Migration Plan.

### 8. Alertas vinculados ao candidato, cancelamento ainda sem login

`vaga_alertas` ganha `candidato_id`. O e-mail de destino deixa de ser digitado e passa a ser o da conta. O cancelamento por token continua funcionando sem autenticação: exigir login para sair de uma lista de e-mails é abusivo e transformaria cada e-mail enviado num problema.

Alertas existentes cujo e-mail casa com uma conta são vinculados a ela; os demais são desativados, já que não há mais forma de seu dono editá-los.

### 9. Anonimização deixa de enumerar

Com fonte única, excluir a conta anonimiza o perfil e remove os arquivos de currículo de todas as versões. Nenhuma varredura por candidatura, nenhuma lista de campos a manter em dia. As candidaturas continuam existindo como registro de processo, apontando para um perfil anonimizado.

### 10. Pontos de leitura que quebram

Consequência direta da decisão 1, listada aqui porque é onde o trabalho se concentra:

- `CandidaturaController::candidaturaResumo()` monta `nome`, `email`, `curso` da candidatura.
- `Candidatura::busca()` filtra por nome/e-mail/CPF na própria linha — vira filtro na relação.
- `Candidatura::$fillable`, `cpf_formatado`, `endereco_completo`, `temCurriculo()`.
- Mails de processo (`CandidaturaRecebidaMail`, `ConviteEntrevistaMail`, `AprovacaoMail`, `ReprovacaoMail`) leem `$candidatura->nome` e `->email`.
- `InscricaoController::consulta()` — consulta pública por CPF + e-mail, que perde a base de dados que a sustentava.
- `Candidato::dadosParaCandidatura()` — existe para copiar perfil → candidatura; perde a função.

## Risks / Trade-offs

**Perda do "o que eu avaliei" literal** → Evento de decisão com versão de currículo vigente cobre currículo e desfecho. Não cobre "qual era o semestre dele na inscrição". Aceito porque nenhuma vaga hoje declara critério de corte por dado do perfil; se passar a declarar, o valor entra no evento de submissão como critério registrado.

**Coordenador de processo em aberto vê dados mudarem sob seus pés** → É o comportamento pedido, e para processo aberto o dado atual é o melhor dado. Mitigado exibindo a data da última atualização do perfil, para que a ficha nunca seja confundida com algo estático.

**Perfil completo vira gate silencioso** → Se a pessoa chega na candidatura e leva um bloqueio, o cadastro mínimo terá empurrado a frustração em vez de removê-la. Mitigado deixando o estado de completude visível desde o primeiro acesso à conta, não só no momento do bloqueio.

**Superfície grande em uma change só** → Cadastro, perfil, candidatura, alertas, painel do coordenador e anonimização mudam juntos. São acoplados pela mesma decisão de dados e separá-los criaria estados intermediários com duas fontes de verdade convivendo — exatamente o problema em correção. Mitigado pela ordem do Migration Plan, que mantém a aplicação coerente a cada passo.

**Arquivos de currículo acumulam** → Versionar significa nunca sobrescrever. Sem política de retenção, o disco cresce sem limite. Não resolvido nesta change; anotado em Open Questions.

## Migration Plan

Base só com dados de teste, então sem consolidação de snapshots. A ordem existe para manter a aplicação de pé entre os passos:

1. **Estrutura nova, sem remover nada.** `candidato_curriculos`, `candidatura_eventos`, `candidatos.curriculo_atual_id`, `candidaturas.conflito_interesse` / `conflito_interesse_detalhe` / `codigo_conduta_aceito_em`, `vaga_alertas.candidato_id`. `candidatos.nome` passa a nullable.
2. **Migrar o que existe.** Currículos atuais viram versão 1. Alertas casam com contas por e-mail; os que não casarem são desativados. Candidaturas órfãs (`candidato_id` nulo): como são dados de teste, são removidas — em base real isso exigiria criar contas-sombra, e é bom que a decisão fique registrada como consequência de a base ser descartável.
3. **Cortar a leitura antiga.** Aplicação passa a ler pelo relacionamento; nada mais lê as colunas duplicadas.
4. **Derrubar as colunas duplicadas** de `candidaturas`, trocar `unique(vaga_id, cpf)` por `unique(vaga_id, candidato_id)`, `candidato_id` vira `NOT NULL`, e `conflito_interesse` / `conflito_interesse_detalhe` / `codigo_conduta_aceito_em` saem de `candidatos`.

**Rollback**: até o passo 3, reversível por migration. A partir do passo 4, não — as colunas com dado foram removidas. O passo 4 é o ponto sem volta e deve ser um deploy separado, depois de o passo 3 estar em produção e estável.

## Open Questions

- **Retenção de versões antigas de currículo.** Guardar para sempre é o padrão desta change. Um limite (por idade ou por número de versões) precisa considerar que uma versão referenciada por evento de decisão não pode sumir enquanto o registro do processo importar. Decidível depois sem mexer em spec nem em tarefa.
- **Notificar o candidato quando o coordenador perde acesso ao perfil.** Transparência boa de ter, sem efeito sobre o modelo de dados.
