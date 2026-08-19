## Why

A change `cadastro-minimo-perfil-unico` tornou o perfil do candidato a fonte única dos seus dados: `Candidatura::CAMPOS_DO_PERFIL` já intercepta a leitura de nome, CPF, e-mail, formação, endereço e currículo e delega tudo para `candidato`. Mas isso é uma garantia só de leitura. As colunas homônimas continuam de pé em `candidaturas` — nuláveis desde a migration `2026_08_02_100009`, mas ainda existindo, ainda populadas, e ainda alcançáveis por qualquer query que não passe pelo Eloquent. `CandidatoRegistroController::adotarCandidaturasAnteriores()` é a prova viva disso: consulta `cpf` e `email` de `candidaturas` diretamente via query builder a cada cadastro novo, ignorando o próprio getAttribute que o resto da aplicação respeita.

Esse é o passo 4, deliberadamente adiado, do Migration Plan daquela change (ver `openspec/changes/cadastro-minimo-perfil-unico/design.md`): "derrubar as colunas duplicadas... deve ser um deploy separado, depois de o passo 3 estar em produção e estável". O passo 3 está — é o que esta proposta assume e fecha.

## What Changes

- **Remoção das candidaturas órfãs remanescentes e do caminho de adoção retroativa** — **BREAKING**: `CandidatoRegistroController::adotarCandidaturasAnteriores()` é removido, e as candidaturas com `candidato_id` nulo que sobrarem na base são apagadas antes da migration destrutiva, já que não há mais como reivindicá-las depois que `cpf`/`email` de `candidaturas` deixarem de existir. A partir deste deploy, uma candidatura feita sem conta autenticada deixa de ser recuperável — mas isso já não acontece: `candidatura-vinculada-a-conta` exige conta autenticada para se candidatar desde a change anterior. O que se fecha aqui é só a herança de dados feitos antes dela existir.
- **Corte das colunas duplicadas de `candidaturas`** — **BREAKING** no esquema (não na aplicação, que já não as lê): `nome`, `email`, `cpf`, `telefone`, `linkedin`, `curso`, `instituicao`, `semestre`, `previsao_conclusao`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `pais`, `pretensao_salarial`, `disponibilidade`, `pcd`, `pcd_tipo`, `curriculo_path`, `curriculo_nome_original` deixam de existir na tabela.
- **Troca da unicidade de candidatura**: `unique(vaga_id, cpf)` dá lugar a `unique(vaga_id, candidato_id)`, e `candidato_id` passa a `NOT NULL` — a garantia contra dupla inscrição passa a se apoiar na conta, não em uma cópia de CPF que está saindo.
- **Corte de `conflito_interesse`, `conflito_interesse_detalhe` e `codigo_conduta_aceito_em` de `candidatos`**: esses campos migraram para `candidaturas` na change anterior (são respondidos por vaga, não pela conta) e nenhum código lê ou escreve mais neles em `candidatos` — as colunas ali são peso morto.
- **`Candidatura::CAMPOS_DO_PERFIL` e o `getAttribute` que o acompanha permanecem**: continuam sendo a forma como a candidatura expõe os dados do perfil; o que muda é que as colunas que eles hoje "escondem" deixam de existir por baixo.

## Capabilities

### Modified Capabilities

- `candidatura-vinculada-a-conta`: o requirement "Adoção de candidaturas anteriores sem vínculo" é removido — a capacidade de incorporar candidaturas antigas deixa de existir porque as colunas (`cpf`, `email` de `candidaturas`) de que ela dependia para casar identidade são removidas nesta change.

## Impact

**Banco de dados**:
- `candidaturas`: remoção das 22 colunas listadas acima; `unique(vaga_id, cpf)` → `unique(vaga_id, candidato_id)`; `candidato_id` vira `NOT NULL`.
- `candidatos`: remoção de `conflito_interesse`, `conflito_interesse_detalhe`, `codigo_conduta_aceito_em`.
- Migration de limpeza: apagar candidaturas com `candidato_id` nulo antes da migration destrutiva (dado de teste hoje; ver Migration Plan em design.md para o caso de já haver dado real).
- **Ponto sem volta**: diferente da change anterior, aqui não há passo aditivo — rollback deixa de ser possível por migration a partir do momento em que as colunas caem.

**Aplicação**:
- `CandidatoRegistroController::store()` e `adotarCandidaturasAnteriores()` — o método é removido e a chamada em `store()` sai junto, incluindo a mensagem de "candidaturas anteriores incorporadas".
- `App\Models\Candidato` — remoção de `conflito_interesse`/`conflito_interesse_detalhe`/`codigo_conduta_aceito_em` do `$fillable`, se lá estiverem.
- Nenhuma outra leitura de aplicação depende das colunas removidas — verificado em `Candidatura::CAMPOS_DO_PERFIL`, `$fillable` e nos controllers que montam dados de candidatura (`CandidaturaController`, `InscricaoController`, `PerfilController`), que já leem `conflito_interesse` e afins a partir de `candidaturas`, não de `candidatos`.

**Fora de escopo**: qualquer alteração de comportamento visível ao candidato ou ao coordenador — a aplicação já opera como se as colunas não existissem; esta change só torna isso verdade no esquema. O painel do coordenador em `www/coordenador_c` não referencia essas colunas (lê via Eloquent apontando para o mesmo banco) e não deveria precisar de mudança, mas fica coberto pela tarefa de verificação em tasks.md.

## Decisões tomadas

1. **Sem passo aditivo intermediário**: ao contrário de `cadastro-minimo-perfil-unico`, esta change não introduz estrutura nova — ela só remove o que ficou para trás. Não há necessidade de expand/contract porque o "expand" já aconteceu na change anterior.
2. **Candidaturas órfãs remanescentes são apagadas, não arquivadas**: a base de destino continua sendo de teste (ver Migration Plan em design.md); em produção real, isso exigiria uma decisão diferente antes de aplicar.
