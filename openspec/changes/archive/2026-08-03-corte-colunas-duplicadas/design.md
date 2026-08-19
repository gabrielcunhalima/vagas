## Context

Ver `proposal.md` — Why. O que importa aqui é o estado exato de onde se parte.

`candidaturas` tem hoje 22 colunas nuláveis e ainda populadas que duplicam dados do perfil (`candidatos`), deixadas assim de propósito pela migration `2026_08_02_100009_tornar_colunas_duplicadas_de_candidaturas_nullable` — o passo 1 do Migration Plan de `cadastro-minimo-perfil-unico`. `Candidatura::CAMPOS_DO_PERFIL` já intercepta a leitura de todas elas via `getAttribute` e delega para `candidato`; nenhum controller, mail ou view lê essas colunas diretamente.

A única exceção é `CandidatoRegistroController::adotarCandidaturasAnteriores()`, que ainda faz `Candidatura::whereNull('candidato_id')->where('cpf', ...)->where('email', ...)` — uma query builder, não um `getAttribute`, então ela não passa pelo shim e vai direto às colunas físicas. É o único código que ainda depende delas, e por isso é o único que precisa mudar além das migrations.

Restrições:

- A base de destino tem apenas dados de teste (5 candidatos, 11 candidaturas, 4 delas órfãs, verificado nesta sessão). Não há estratégia de consolidação a definir.
- `candidaturas` tem `unique(vaga_id, cpf)`; `cpf` é uma das colunas removidas.
- `candidatos.conflito_interesse` / `conflito_interesse_detalhe` / `codigo_conduta_aceito_em` não são lidos nem escritos por nenhum código de aplicação — só existem em `candidaturas` hoje.

## Goals / Non-Goals

**Goals:**

- Fazer o esquema físico corresponder ao que a aplicação já assume: perfil como único dono desses dados.
- Fechar a janela de adoção retroativa sem deixar um caminho quebrado (query para coluna inexistente) no lugar de um removido de propósito.
- Preservar a garantia de "uma candidatura por vaga por conta" trocando a base do índice único de `cpf` para `candidato_id`.

**Non-Goals:**

- Qualquer mudança de comportamento observável pelo candidato ou pelo coordenador — já não há nenhuma, e esta change não deve introduzir uma.
- Política de retenção de currículos versionados — segue em aberto em `cadastro-minimo-perfil-unico`.
- `www/coordenador_c` — fora deste repositório; lê via Eloquent do mesmo banco e não referencia as colunas removidas.

## Decisions

### 1. Sem passo aditivo — esta change é só o "contract"

`cadastro-minimo-perfil-unico` seguiu expand → migrate → cortar leitura → contract. Os três primeiros já aconteceram. Esta change é inteiramente o quarto passo: não cria nada, só remove o que ficou para trás depois que a aplicação parou de precisar.

**Alternativa considerada**: reabrir uma janela adicional de nulidade para alguma das colunas, como margem de segurança. Descartada — o `getAttribute` de `Candidatura` já prova que nenhuma leitura de aplicação depende delas; adiar mais um deploy só prolonga um estado que já é puramente estrutural.

### 2. Ordem das migrations: limpar órfãs antes de derrubar colunas

A remoção de candidaturas com `candidato_id` nulo precisa rodar **antes** da migration que torna `candidato_id` `NOT NULL` — caso contrário a alteração da coluna falha com linhas nulas presentes. E a remoção do método de adoção no controller precisa ser um commit de código que acompanha o mesmo deploy das migrations: se o código continuar rodando contra colunas que não existem mais, `adotarCandidaturasAnteriores()` lança exceção de SQL a cada cadastro novo.

```
1. Remover candidaturas com candidato_id nulo (dados de teste — sem estratégia de resgate)
2. Remover CandidatoRegistroController::adotarCandidaturasAnteriores() e sua chamada em store()
3. Migration: candidato_id NOT NULL em candidaturas
4. Migration: unique(vaga_id, cpf) → unique(vaga_id, candidato_id)
5. Migration: drop das 22 colunas duplicadas de candidaturas
6. Migration: drop de conflito_interesse / conflito_interesse_detalhe / codigo_conduta_aceito_em de candidatos
```

Os passos 3–6 podem ser uma única migration ou seguir nessa ordem em migrations separadas; o que importa é a ordem relativa entre 1 e 3, e entre 2 e 5 (código e schema mudam juntos, no mesmo deploy).

### 3. Índice único migra de `cpf` para `candidato_id`

Como `candidatos.cpf` já é `unique`, e `candidato_id` vira `NOT NULL`, `unique(vaga_id, candidato_id)` preserva exatamente a mesma garantia que `unique(vaga_id, cpf)` oferecia — duas candidaturas do mesmo CPF na mesma vaga continuam impossíveis, só que a checagem passa pela conta em vez de por um valor copiado.

**Alternativa considerada**: manter os dois índices por um tempo. Descartada — `cpf` está sendo removida da tabela nesta mesma change; não há como manter um índice sobre uma coluna que não existe.

## Risks / Trade-offs

**Candidaturas órfãs remanescentes são apagadas, não migradas** → Aceitável porque a base de destino é só de dados de teste (verificado: 4 candidaturas com `candidato_id` nulo). Numa base real, isso exigiria decidir o destino dessas linhas antes de aplicar — criar contas-sombra, notificar por e-mail, ou aceitar a perda — decisão que este design não toma porque não se aplica ao estado atual do repositório.

**Ponto sem volta** → A partir da migration que remove as colunas, não há `down()` que recupere os dados: eles já não existem. Mitigado por ser, como o proposal descreve, o desfecho pretendido do Migration Plan da change anterior — o rollback reversível terminou no passo 3 dela, antes desta change começar.

## Migration Plan

1. Apagar as candidaturas com `candidato_id` nulo remanescentes na base.
2. Remover `CandidatoRegistroController::adotarCandidaturasAnteriores()` e a chamada em `store()`, no mesmo deploy das migrations abaixo.
3. Migration: `candidaturas.candidato_id` vira `NOT NULL`.
4. Migration: troca `unique(vaga_id, cpf)` por `unique(vaga_id, candidato_id)`.
5. Migration: remove as 22 colunas duplicadas de `candidaturas`.
6. Migration: remove `conflito_interesse`, `conflito_interesse_detalhe`, `codigo_conduta_aceito_em` de `candidatos`.

**Rollback**: nenhum, a partir do passo 3. Antes disso (passo 1 e a remoção de código do passo 2), reversível normalmente — restaurar da migration anterior e reverter o commit.
