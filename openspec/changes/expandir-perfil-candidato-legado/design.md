## Context

`candidatos` guarda hoje `telefone` (string única) e `pcd` (boolean) + `pcd_tipo` (texto livre) como os únicos dados de contato/deficiência do perfil. `candidaturas` guarda `conflito_interesse` (boolean) + `conflito_interesse_detalhe` (texto) como a única pergunta de vínculo com a FAPEU, respondida a cada inscrição (ver `app/Http/Requests/Vagas/InscricaoRequest.php` e `app/Models/Vagas/Candidatura.php:73-87`, que delega `telefone` — e nada de deficiência/endereço-tipo — do candidato para a candidatura via `CAMPOS_DO_PERFIL`).

Assim como em `add-multiplas-formacoes-candidatura`, o ponto único de verdade para os campos de perfil é `Candidato` — `Candidatura` só exibe o que o candidato expõe, exceto os campos que já são propriamente da candidatura (conflito de interesse, código de conduta, carta de apresentação), que continuam vivendo em `candidaturas`.

Ver proposal.md para a comparação completa com o formulário legado e a motivação.

## Goals / Non-Goals

**Goals:**
- Separar telefone fixo de telefone celular, preservando o dado já coletado hoje (que semanticamente já era usado como celular, a julgar pela máscara `(48) 99999-9999` usada na tela) e a exigência de completude que já recai sobre ele.
- Substituir o texto livre de tipo de deficiência por marcações específicas e combináveis, alinhadas às 8 categorias do formulário legado.
- Acrescentar tipo de endereço e tipo de bairro como dados opcionais do endereço do perfil.
- Substituir a pergunta única de conflito de interesse por 5 perguntas específicas de vínculo com a FAPEU, mantendo o comportamento já existente de detalhamento obrigatório quando há vínculo declarado.

**Non-Goals:**
- Qualquer campo de Formação (curso superior 1/2/3, outras formações MEC, outros cursos/palestras) — coberto por `add-multiplas-formacoes-candidatura`.
- Qualquer alteração em upload/versionamento de currículo ou no aceite do código de conduta — já atendidos.
- Sincronizar ou exportar esses dados para a tabela de produção em SQL Server. Essa change só amplia a coleta no portal; uma eventual integração é trabalho futuro, fora deste escopo.
- Definir uma lista fechada de tipos de logradouro/bairro — ver decisão abaixo.

## Decisions

### Rename de `telefone` para `telefone_celular`, não coluna nova

O campo `telefone` existente já é tratado como celular na interface (máscara de 9 dígitos, placeholder `(48) 99999-9999`) e já é exigido para o perfil ser considerado completo (`Candidato::CAMPOS_OBRIGATORIOS`). Renomear preserva esse dado e essa exigência sem migração de dados adicional. Uma coluna nova ao lado da antiga (mantendo `telefone`) foi descartada: criaria uma ambiguidade permanente sobre qual campo é a fonte de verdade, e obrigaria a decidir o que fazer com o valor já gravado em `telefone` de cada candidato.

`telefone_fixo` é adicionado como coluna nova, nullable, sem exigência de preenchimento — o formulário legado não marca nenhum dos dois telefones como obrigatório, e não há motivo para tornar o fixo obrigatório quando o celular já cumpre esse papel.

### Deficiência como 8 booleanos, `pcd` passa a ser calculado

Alternativa considerada: manter `pcd_tipo` como texto livre e só adicionar as 8 marcações ao lado. Rejeitada porque o texto livre e as marcações específicas descreveriam o mesmo dado por dois caminhos diferentes — nada garante que fiquem consistentes, e a tela passaria a ter um campo redundante. `pcd_tipo` é removido.

`pcd` deixa de ser um campo editável diretamente pelo formulário e passa a ser derivado no backend (verdadeiro se qualquer uma das 8 marcações for verdadeira), calculado em `PerfilController@update` antes de salvar — mantém `pcd` funcionando para qualquer leitor existente (exportação de dados, por exemplo) sem exigir que cada leitor passe a somar 8 campos.

Autismo permanece como marcação própria, separada de "Mental"/"Intelectual" — no formulário legado já aparece como categoria distinta, refletindo o enquadramento como pessoa com deficiência por equiparação (Lei 12.764/2012), tratado à parte das demais deficiências.

### Tipo de endereço e tipo de bairro como texto livre, não enum fechado

O formulário legado usa dropdowns para esses dois campos, mas o repositório não tem visibilidade sobre a lista de valores usada em produção (não há acesso de leitura confirmado à base SQL Server de produção nem uma tabela de domínio equivalente neste repositório). Inventar uma lista fechada arriscaria não bater com os valores que a produção espera, e o pedido do usuário foi explícito em não precisar reproduzir o formulário exatamente, apenas capturar o mesmo dado. Por isso os dois campos entram como texto livre, opcionais, sem exigência de preenchimento — se a lista de produção for conhecida depois, transformar em `select` é uma mudança de UI que não exige nova migração (a coluna já guarda string).

### Conflito de interesse como 5 booleanos, `conflito_interesse` passa a ser calculado

Mesma lógica da deficiência: os 5 vínculos específicos (servidor na direção, dirigente, cargo de direção superior, coordenador de projeto administrado pela FAPEU, fiscal de contrato) entram como colunas booleanas próprias em `candidaturas`, e `conflito_interesse` passa a ser calculado a partir delas em vez de perguntado diretamente. `conflito_interesse_detalhe` continua exigido (`required_if`) quando qualquer um dos 5 for verdadeiro — mesma regra de hoje, só que a condição passa a somar 5 campos em vez de ler 1.

Alternativa considerada: manter a pergunta única "tem vínculo?" ao lado das 5 específicas. Rejeitada pelo mesmo motivo do `pcd_tipo` — duas fontes para o mesmo fato, sem garantia de consistência.

### Onde cada campo vive

Telefone, deficiência e tipo de endereço/bairro são dados da pessoa, não da candidatura específica — entram em `candidatos`, seguindo o princípio de fonte única de `perfil-candidato-unico`. Os 5 vínculos de conflito de interesse continuam em `candidaturas`, porque a resposta é por vaga (`candidatura-vinculada-a-conta` já estabelece isso para a pergunta única de hoje — ver Requirement "Dados próprios da candidatura").

## Migration Plan

Duas migrations, uma por tabela:

**`candidatos`** (uma migration):
1. `renameColumn('telefone', 'telefone_celular')`.
2. Adicionar `telefone_fixo` string(20) nullable.
3. Adicionar `deficiencia_fisica`, `deficiencia_auditiva`, `deficiencia_fala`, `deficiencia_visual`, `deficiencia_mental`, `deficiencia_intelectual`, `reabilitado_inss`, `deficiencia_autismo` — todos boolean, default false.
4. Adicionar `tipo_logradouro`, `tipo_bairro` — string nullable.
5. Remover `pcd_tipo`.

No `down()`: reverter cada passo (recriar `pcd_tipo`, remover as colunas novas, `renameColumn('telefone_celular', 'telefone')`).

**`candidaturas`** (uma migration):
1. Adicionar `vinculo_servidor_direcao`, `vinculo_dirigente`, `vinculo_cargo_direcao_superior`, `vinculo_coordenador_projeto`, `vinculo_fiscal_contrato` — todos boolean, default false, posicionados depois de `conflito_interesse_detalhe`.

No `down()`: remover as 5 colunas.

Depois das migrations, os seguintes pontos mudam juntos (mesma ordem de dependência de `add-multiplas-formacoes-candidatura`):
1. `Candidato::$fillable`/`CAMPOS_OBRIGATORIOS` (troca `telefone` por `telefone_celular`, remove `pcd_tipo`, adiciona as novas colunas) e casts booleanos das 8 marcações de deficiência.
2. `PerfilController` (edit/update calcula `pcd`; exportarDados troca as chaves).
3. `AnonimizacaoService` — troca `telefone` por `telefone_celular` + `telefone_fixo`; remove referência a `pcd_tipo`.
4. `Candidatura::$fillable`/casts (5 novas colunas booleanas), cálculo de `conflito_interesse` no `InscricaoController`/`CandidaturaController`.
5. `InscricaoRequest` — troca a validação de uma pergunta Sim/Não por 5 booleans + `required_if` ajustado para "qualquer um verdadeiro".
6. Frontend: `Candidato/Perfil/Edit.jsx`, `Publico/Candidatura.jsx`, `Coord/Candidaturas/Show.jsx`, `Candidato/Candidaturas/Show.jsx`.
7. Seeders/factories/testes que hoje usam `telefone` ou `pcd_tipo`.

## Risks / Trade-offs

- **Rename de coluna em vez de coluna nova** → qualquer código, seed, factory ou teste que ainda referencie `telefone` diretamente quebra até ser atualizado; mitigado listando explicitamente cada arquivo afetado em tasks.md (o grep feito para esta proposta já identificou todos).
- **Tipo de endereço/bairro como texto livre** → sem padronização, dois candidatos podem descrever o mesmo tipo de formas diferentes (ex.: "Av." vs "Avenida"). Aceitável porque não há lista de referência confiável disponível; ver Decisions.
- **Cálculo de `pcd`/`conflito_interesse` movido para o backend** → qualquer novo ponto que grave esses modelos diretamente (fora de `PerfilController`/fluxo de candidatura) precisa lembrar de recalcular o agregado; mitigado concentrando o cálculo em um único método/local por model, não duplicado em cada controller.
- **Sobreposição de escopo com `add-multiplas-formacoes-candidatura`** → as duas changes tocam `Candidato/Perfil/Edit.jsx` e `PerfilController` (seções diferentes: formação vs. identificação/deficiência/endereço). Não há conflito de campos, mas a ordem de implementação importa para evitar merge trabalhoso — recomendação: aplicar uma change de cada vez, não em paralelo na mesma branch.
