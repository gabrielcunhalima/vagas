## Context

Hoje `curso`, `instituicao`, `nivel_escolaridade`, `situacao_curso`, `semestre` e `previsao_conclusao` são colunas escalares em `candidatos`. `App\Models\Vagas\Candidatura` nunca guarda cópia própria: `CAMPOS_DO_PERFIL` + `getAttribute()` delegam essas chaves para o candidato (ver `app/Models/Vagas/Candidatura.php:73-87`), e `InscricaoController::perfilParaConferencia()` lê diretamente do candidato. Isso significa que o ponto único de verdade a mudar é o `Candidato` — o resto do sistema (candidatura, coordenador) só passa a exibir o que o candidato passar a expor. Ver proposal.md para a motivação.

Módulos que leem essas colunas hoje: `App\Models\Candidato` (`CAMPOS_OBRIGATORIOS`, `pendencias()`, `estadoCompletude()`), `PerfilController` (edit/update/exportarDados), `AnonimizacaoService`, `InscricaoController::perfilParaConferencia`, `Candidatura::scopeBusca`, e as páginas React `Candidato/Perfil/Edit.jsx`, `Publico/Candidatura.jsx`, `Coord/Candidaturas/Show.jsx`, `Candidato/Candidaturas/Show.jsx`.

## Goals / Non-Goals

**Goals:**
- Permitir zero ou mais formações por candidato, cada uma validada e exibida de forma independente.
- Manter a regra de completude equivalente à atual: pelo menos uma formação totalmente preenchida.
- Dois campos de texto livre novos, sem estrutura própria, para pós-graduação e outros cursos/palestras.
- Preservar o princípio de fonte única (`perfil-candidato-unico`): nenhuma cópia de formação em `candidaturas`.

**Non-Goals:**
- Reordenar formações manualmente (drag-and-drop) — a ordem de cadastro é suficiente.
- Estruturar os dois campos de texto livre em nome/ano separados — o pedido é texto livre.
- Anexar comprovantes/diplomas por formação — fora do escopo desta mudança.

## Decisions

### Tabela relacional `candidato_formacoes`, não JSON

Uma tabela 1:N (`candidato_id`, `nivel_escolaridade`, `situacao_curso`, `curso`, `instituicao`, `semestre`, `previsao_conclusao`, timestamps) em vez de uma coluna JSON em `candidatos`.

Alternativa considerada: coluna `formacoes json` em `candidatos`. Rejeitada porque `scopeBusca` já filtra candidaturas por `curso` via SQL (`Candidatura::scopeBusca`) e isso fica muito mais simples com uma tabela relacional (`whereHas('formacoes', ...)`) do que com filtro em JSON. Também mantém consistência com o padrão já usado para `curriculos` (1:N com versão vigente) em vez de introduzir um segundo padrão de dados semiestruturados.

Sem coluna de ordenação explícita: a ordem de exibição é a ordem de inserção (`orderBy('id')`). Não há pedido de reordenação, então uma coluna `ordem` seria complexidade sem uso.

### Substituição total da lista a cada salvamento do perfil

`PerfilController@update` substitui todas as formações do candidato a cada envio do formulário (apaga as existentes e recria a partir do array enviado), em vez de fazer diff por id.

Alternativa considerada: casar por id enviado, atualizando in-place e removendo os ausentes. Rejeitada por complexidade desnecessária — nenhuma outra tabela referencia uma formação específica por id (diferente de `curriculos`, cujas versões são referenciadas por eventos de candidatura), então recriar a lista inteira é seguro e muito mais simples de validar e testar.

### Dois campos de texto livre na própria tabela `candidatos`

`outras_formacoes_mec` (text, nullable) e `outros_cursos` (text, nullable) ficam em `candidatos`, não em `candidato_formacoes` — são texto livre, não repetível, e semanticamente pertencem ao perfil como um todo (assim como `carta_apresentacao` pertence à candidatura como um todo).

### `pendencias()` passa a inspecionar a coleção de formações

Hoje `Candidato::CAMPOS_OBRIGATORIOS` é um mapa `coluna => rótulo` percorrido com `blank($this->{campo})`. Isso deixa de funcionar porque formação não é mais um atributo escalar do candidato.

Nova divisão:
- Campos que continuam sendo atributos escalares do candidato (`nome`, `nacionalidade`, `telefone`) continuam no mesmo laço.
- Uma nova checagem dedicada percorre `$this->formacoes` procurando ao menos uma formação com todos os campos exigidos preenchidos (aplicando a mesma regra condicional de semestre por formação). Se nenhuma formação satisfizer isso, a pendência `formacao` é reportada — reaproveitando o rótulo agregado em vez de repetir os cinco campos individuais, já que agora são N conjuntos e não um só.
- `possui_acessibilidade` e currículo continuam com a lógica própria já existente em `pendencias()`.

O componente `montarCompletude()` em `Candidato/Perfil/Edit.jsx` duplica essa regra no cliente (comentário já existe no código explicando por quê: reagir a cada tecla sem round-trip). Ele precisa da mesma regra — "existe ao menos uma formação da lista com todos os campos preenchidos" — em vez de checar as antigas chaves escalares.

### `Candidatura` para de delegar campos de formação individualmente

`CAMPOS_DO_PERFIL` perde `curso`, `instituicao`, `semestre`, `previsao_conclusao`. Em vez disso, `Candidatura` ganha um método (`formacoes()` ou accessor) que devolve `$this->candidato?->formacoes`. Todo consumidor (conferência, e as duas telas de detalhe) passa a iterar essa lista em vez de ler um único curso/instituição.

`nivel_escolaridade` e `situacao_curso` também saem de `CAMPOS_DO_PERFIL` pelo mesmo motivo (eram usados só junto com curso/instituição).

## Migration Plan

Uma única migration cobre a expansão e a migração de dados, nessa ordem dentro do `up()`:

1. Cria `candidato_formacoes` (`id`, `candidato_id` FK `cascadeOnDelete`, `nivel_escolaridade` string(50) nullable, `situacao_curso` enum(cursando,concluido) nullable, `curso` string nullable, `instituicao` string nullable, `semestre` string(10) nullable, `previsao_conclusao` date nullable, timestamps).
2. Copia, para cada candidato com `curso` ou `instituicao` preenchido, uma linha para `candidato_formacoes` com os valores atuais dessas seis colunas.
3. Adiciona `outras_formacoes_mec` (text, nullable) e `outros_cursos` (text, nullable) em `candidatos`.
4. Remove de `candidatos` as colunas `curso`, `instituicao`, `nivel_escolaridade`, `situacao_curso`, `semestre`, `previsao_conclusao`.

No `down()`: recria as seis colunas em `candidatos`, copia de volta apenas a primeira formação (`orderBy('id')->first()`) de cada candidato para essas colunas, e remove a tabela `candidato_formacoes` e as duas colunas de texto livre.

Depois da migration de schema, os seguintes pontos precisam mudar juntos (mesmo PR, pela ordem de dependência):
1. `CandidatoFormacao` (model novo) + `Candidato::formacoes()`.
2. `Candidato::pendencias()` / `estadoCompletude()` / `CAMPOS_OBRIGATORIOS`.
3. `PerfilController` (edit, update, exportarDados) e `AnonimizacaoService`.
4. `Candidatura::CAMPOS_DO_PERFIL` / `getAttribute` / `scopeBusca` + `InscricaoController::perfilParaConferencia`.
5. Frontend: `Candidato/Perfil/Edit.jsx`, `Publico/Candidatura.jsx`, `Coord/Candidaturas/Show.jsx`, `Candidato/Candidaturas/Show.jsx`.
6. Seeders/factories/testes que hoje geram candidatos com as colunas antigas.

## Risks / Trade-offs

- **Rollback com perda de dados** → se um candidato cadastrar uma segunda formação depois desta mudança e a migration for revertida, o `down()` só recupera a primeira formação (por ordem de id) de cada candidato. Aceitável porque isso só ocorre em rollback manual explícito, não em operação normal.
- **Duplicação da regra de completude entre servidor e cliente** → já existe hoje (comentário em `Candidato/Perfil/Edit.jsx` reconhece isso) e continua existindo com a nova regra baseada em lista; o servidor continua sendo a fonte de verdade (`Candidato::pendencias()`), o cliente só antecipa a UI.
- **Todas as telas que hoje mostram "Curso" / "Instituição" como uma linha única precisam virar uma lista** → risco de esquecer alguma tela; mitigado listando explicitamente as quatro páginas React afetadas nas tasks.
