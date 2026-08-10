## 1. Banco de dados

- [ ] 1.1 Criar migration em `candidatos`: `renameColumn('telefone', 'telefone_celular')`
- [ ] 1.2 Na mesma migration, adicionar `telefone_fixo` (string(20), nullable)
- [ ] 1.3 Na mesma migration, adicionar `deficiencia_fisica`, `deficiencia_auditiva`, `deficiencia_fala`, `deficiencia_visual`, `deficiencia_mental`, `deficiencia_intelectual`, `reabilitado_inss`, `deficiencia_autismo` (boolean, default false)
- [ ] 1.4 Na mesma migration, adicionar `tipo_logradouro`, `tipo_bairro` (string, nullable)
- [ ] 1.5 Na mesma migration, remover a coluna `pcd_tipo`
- [ ] 1.6 Implementar `down()` revertendo os passos 1.2–1.5 e desfazendo o rename de 1.1
- [ ] 1.7 Criar migration em `candidaturas`: adicionar `vinculo_servidor_direcao`, `vinculo_dirigente`, `vinculo_cargo_direcao_superior`, `vinculo_coordenador_projeto`, `vinculo_fiscal_contrato` (boolean, default false, após `conflito_interesse_detalhe`)
- [ ] 1.8 Implementar `down()` removendo as 5 colunas de 1.7

## 2. Modelos

- [ ] 2.1 `App\Models\Candidato`: trocar `telefone` por `telefone_celular` em `$fillable` e `CAMPOS_OBRIGATORIOS`; adicionar `telefone_fixo`, as 8 colunas de deficiência e `tipo_logradouro`/`tipo_bairro` a `$fillable`; remover `pcd_tipo` de `$fillable`
- [ ] 2.2 `App\Models\Candidato`: adicionar casts booleanos para as 8 colunas de deficiência
- [ ] 2.3 `App\Models\Candidato`: adicionar método/accessor que calcula `pcd` a partir das 8 marcações (verdadeiro se qualquer uma for verdadeira)
- [ ] 2.4 `App\Models\Vagas\Candidatura`: adicionar as 5 colunas de vínculo a `$fillable` e aos casts booleanos
- [ ] 2.5 `App\Models\Vagas\Candidatura`: trocar `telefone` por `telefone_celular` em `CAMPOS_DO_PERFIL` (e adicionar `telefone_fixo`, `tipo_logradouro`, `tipo_bairro` se a candidatura precisar exibi-los na conferência)
- [ ] 2.6 `App\Models\Vagas\Candidatura`: adicionar método/accessor que calcula `conflito_interesse` a partir das 5 colunas de vínculo (verdadeiro se qualquer uma for verdadeira)

## 3. Backend — perfil do candidato

- [ ] 3.1 `PerfilController@edit`: trocar `telefone` por `telefone_celular` na lista de `only()`; incluir `telefone_fixo`, as 8 marcações de deficiência (em vez de `pcd_tipo`) e `tipo_logradouro`/`tipo_bairro` no payload
- [ ] 3.2 `PerfilController@update`: validar `telefone_celular` (mantendo as mesmas regras hoje aplicadas a `telefone`), `telefone_fixo` (nullable, string, max:20), as 8 marcações de deficiência (nullable, boolean) e `tipo_logradouro`/`tipo_bairro` (nullable, string, max:255); remover a validação de `pcd_tipo`
- [ ] 3.3 `PerfilController@update`: calcular `pcd` a partir das 8 marcações antes de `$candidato->update($dados)`, em vez de receber `pcd` do formulário
- [ ] 3.4 `PerfilController@exportarDados`: trocar `telefone`/`pcd_tipo` pelas novas chaves (`telefone_celular`, `telefone_fixo`, as 8 marcações, `tipo_logradouro`, `tipo_bairro`)
- [ ] 3.5 `App\Services\AnonimizacaoService`: trocar a anonimização de `telefone` por `telefone_celular` e `telefone_fixo`; remover a referência a `pcd_tipo`; confirmar se as 8 marcações de deficiência devem ser zeradas na anonimização (dado sensível, mesma lógica hoje aplicada a `pcd`)

## 4. Backend — candidatura

- [ ] 4.1 `App\Http\Requests\Vagas\InscricaoRequest`: trocar a validação de `conflito_interesse` (Sim/Não único) pelas 5 colunas de vínculo (`required`, `boolean`, cada uma); ajustar `conflito_interesse_detalhe` para `required_if` quando qualquer uma das 5 for verdadeira
- [ ] 4.2 `App\Http\Controllers\Vagas\InscricaoController`: ao gravar a candidatura, calcular `conflito_interesse` a partir das 5 colunas antes de salvar; em `perfilParaConferencia`, expor `telefone_celular`/`telefone_fixo` em vez de `telefone` (e demais campos novos, se exibidos na conferência)
- [ ] 4.3 `App\Http\Controllers\Vagas\CandidaturaController`: trocar `telefone` por `telefone_celular` (e incluir `telefone_fixo` se exibido) na visão do coordenador; expor as 5 colunas de vínculo (não só o agregado) na visão da candidatura
- [ ] 4.4 `App\Http\Controllers\Candidato\MinhaCandidaturaController`: trocar `telefone` por `telefone_celular` (e incluir `telefone_fixo` se exibido)

## 5. Frontend — Meus Dados (`Candidato/Perfil/Edit.jsx`)

- [ ] 5.1 Trocar o campo único "Telefone" por dois campos — "Telefone fixo" e "Telefone celular" — reaproveitando `maskTelefone`; `telefone_celular` continua exigido pela barra de completude, `telefone_fixo` não
- [ ] 5.2 Na seção "Preferências", trocar o `Switch` "Sou pessoa com deficiência (PcD)" + input de texto livre por 8 checkboxes independentes, uma por condição (física, auditiva, fala, visual, mental, intelectual, "Reabilitado conforme Resolução INSS/PRES Nº 118, de 04/11/2010", autismo)
- [ ] 5.3 Atualizar `CAMPOS_COMPLETUDE`/`montarCompletude()` para checar `telefone_celular` em vez de `telefone`
- [ ] 5.4 Na seção "Endereço", adicionar os campos "Tipo de endereço" e "Tipo de bairro" (inputs de texto livre, opcionais)

## 6. Frontend — Candidatura (`Publico/Candidatura.jsx`)

- [ ] 6.1 Trocar o campo `Dado` "Telefone" por "Telefone celular" e, se preenchido, "Telefone fixo" na seção "Seus dados"
- [ ] 6.2 Substituir o único `Select` de conflito de interesse pelos 5 checkboxes de vínculo com a FAPEU, com os rótulos: "servidor das instituições apoiadas pela FAPEU, que atue na direção da Fundação", "dirigente das instituições apoiadas pela FAPEU", "ocupante de cargo de direção superior das instituições apoiadas pela FAPEU", "coordenador de projeto administrado pela FAPEU", "fiscal de contrato entre a FAPEU e terceiros"
- [ ] 6.3 Manter o campo "Descreva a relação" (`conflito_interesse_detalhe`) condicionado a qualquer um dos 5 checkboxes estar marcado, em vez de à resposta única anterior

## 7. Frontend — telas de exibição

- [ ] 7.1 `Coord/Candidaturas/Show.jsx`: trocar `Info` "Conflito de interesse" (Sim/Não único) por uma lista dos vínculos declarados; manter "Relação declarada" condicionada a qualquer vínculo marcado
- [ ] 7.2 `Candidato/Candidaturas/Show.jsx`: se a tela já exibir dados de contato/conflito de interesse, aplicar a mesma troca; confirmar e pular se a tela não exibir esses dados hoje

## 8. Seeders, factories e testes

- [ ] 8.1 `database/seeders/CandidatoSeeder.php`: trocar a chave `telefone` por `telefone_celular` nos candidatos seedados
- [ ] 8.2 `database/seeders/CenariosTesteSeeder.php`: trocar `telefone` por `telefone_celular` em todos os cenários; adicionar exemplos com deficiências marcadas e com vínculo FAPEU declarado, para cobrir os novos campos nos cenários de teste manual
- [ ] 8.3 `database/factories/CandidatoFactory.php`: trocar `telefone` por `telefone_celular`
- [ ] 8.4 `tests/Unit/PerfilCompletudeTest.php`: atualizar `test_telefone_em_branco_bloqueia` para usar `telefone_celular`; adicionar teste garantindo que `telefone_fixo` em branco não bloqueia a completude
- [ ] 8.5 `tests/Unit/CandidaturaModelTest.php`: atualizar a lista de campos delegados ao perfil (`CAMPOS_DO_PERFIL`) para refletir `telefone_celular`
- [ ] 8.6 Adicionar teste cobrindo o cálculo de `pcd` a partir das 8 marcações (verdadeiro com qualquer uma marcada, falso com todas desmarcadas)
- [ ] 8.7 Adicionar teste cobrindo o cálculo de `conflito_interesse` a partir dos 5 vínculos, incluindo a exigência de `conflito_interesse_detalhe` quando qualquer um for verdadeiro
