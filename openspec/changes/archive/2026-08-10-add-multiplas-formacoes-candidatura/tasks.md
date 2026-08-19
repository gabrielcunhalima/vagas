## 1. Banco de dados

- [x] 1.1 Criar migration que cria a tabela `candidato_formacoes` (`candidato_id` FK `cascadeOnDelete`, `nivel_escolaridade`, `situacao_curso` enum(cursando,concluido), `curso`, `instituicao`, `semestre`, `previsao_conclusao`, timestamps)
- [x] 1.2 Na mesma migration, copiar para `candidato_formacoes` a formação existente de cada candidato com `curso` ou `instituicao` preenchido
- [x] 1.3 Na mesma migration, adicionar `outras_formacoes_mec` (text, nullable) e `outros_cursos` (text, nullable) em `candidatos`
- [x] 1.4 Na mesma migration, remover de `candidatos` as colunas `curso`, `instituicao`, `nivel_escolaridade`, `situacao_curso`, `semestre`, `previsao_conclusao`
- [x] 1.5 Implementar `down()` revertendo os passos acima (recriar colunas, copiar de volta apenas a primeira formação de cada candidato, remover tabela e colunas novas)

## 2. Modelos

- [x] 2.1 Criar `App\Models\Vagas\CandidatoFormacao` (ou `App\Models\CandidatoFormacao`) com `$fillable` para as seis colunas de formação e `belongsTo(Candidato::class)`
- [x] 2.2 Adicionar `Candidato::formacoes()` (`hasMany(CandidatoFormacao::class)->orderBy('id')`)
- [x] 2.3 Adicionar `outras_formacoes_mec` e `outros_cursos` a `Candidato::$fillable`
- [x] 2.4 Remover `curso`, `instituicao`, `nivel_escolaridade`, `situacao_curso`, `semestre`, `previsao_conclusao` de `Candidato::$fillable`
- [x] 2.5 Reescrever `Candidato::pendencias()` para checar nome/nacionalidade/telefone como hoje, e substituir a checagem das colunas antigas por uma checagem sobre `$this->formacoes`: incompleto se nenhuma formação da lista tiver todos os campos preenchidos (semestre exigido só quando aquela formação estiver "cursando")
- [x] 2.6 Atualizar `Candidato::CAMPOS_OBRIGATORIOS` e `estadoCompletude()` (contagem de itens) para refletir a formação como um único item de pendência agregado, não mais cinco colunas
- [x] 2.7 Remover `curso`, `instituicao`, `semestre`, `previsao_conclusao` (e `nivel_escolaridade`, `situacao_curso` se só usados junto) de `Candidatura::CAMPOS_DO_PERFIL`/`getAttribute`
- [x] 2.8 Adicionar em `Candidatura` um accessor/método que devolve as formações do candidato vinculado (`$this->candidato?->formacoes`)
- [x] 2.9 Atualizar `Candidatura::scopeBusca` para buscar por curso via `whereHas('candidato.formacoes', ...)` em vez da coluna direta

## 3. Backend — perfil do candidato

- [x] 3.1 Atualizar `PerfilController@edit` para carregar `formacoes` (com os seis campos) junto com o candidato e remover as chaves escalares antigas do payload
- [x] 3.2 Atualizar `PerfilController@update`: validar `formacoes` como array (cada item com `nivel_escolaridade`, `situacao_curso`, `curso`, `instituicao`, `semestre`, `previsao_conclusao`, todos nullable individualmente) e validar `outras_formacoes_mec`/`outros_cursos` (nullable, string, limite de tamanho razoável)
- [x] 3.3 No `update()`, substituir a lista de formações do candidato (apagar as existentes e recriar a partir do array validado) na mesma transação do restante do perfil
- [x] 3.4 Atualizar `PerfilController@exportarDados` para incluir a lista de formações e os dois campos de texto livre em vez das colunas antigas
- [x] 3.5 Atualizar `AnonimizacaoService::anonimizarCandidato` para apagar `candidato->formacoes` e zerar `outras_formacoes_mec`/`outros_cursos`, removendo as referências às colunas antigas

## 4. Backend — candidatura

- [x] 4.1 Atualizar `InscricaoController::perfilParaConferencia` para incluir a lista de formações (mapeada para array simples) e os dois campos de texto livre, em vez das chaves escalares antigas

## 5. Frontend — Meus Dados (`Candidato/Perfil/Edit.jsx`)

- [x] 5.1 Trocar os campos escalares de formação no `useForm` por `formacoes: [...]` (array vindo do candidato, ou uma linha em branco se vazio)
- [x] 5.2 Renderizar a seção "Formação" como uma lista de blocos repetíveis (nível, situação, curso, instituição, semestre condicional, previsão de conclusão), cada um com botão de remover
- [x] 5.3 Adicionar botão "Adicionar formação" que insere um novo bloco em branco no array
- [x] 5.4 Adicionar os dois novos campos de texto livre (textarea) com os rótulos "Outras Formações Superiores reconhecidas pelo MEC (Ex.: Especialização em XXX - ANO, Mestrado em XXX - ANO e Doutorado em XXX - ANO):" e "Outros Cursos, Palestras, Etc., informar nome e data:"
- [x] 5.5 Atualizar `montarCompletude()`/`CAMPOS_COMPLETUDE` para calcular a pendência de formação a partir do array (existe ao menos uma formação com todos os campos preenchidos) em vez das chaves escalares antigas
- [x] 5.6 Ajustar exibição de erros de validação, já que os erros de `formacoes.*.campo` vêm indexados por posição no array

## 6. Frontend — telas que exibem formação

- [x] 6.1 `Publico/Candidatura.jsx`: trocar os dois `<Dado>` de Curso/Instituição por uma lista das formações do `perfil`
- [x] 6.2 `Coord/Candidaturas/Show.jsx`: trocar os dois `<Info>` de Curso/Instituição por uma lista das formações da candidatura
- [x] 6.3 `Candidato/Candidaturas/Show.jsx`: adicionar a mesma exibição em lista, se a tela já mostra dados de formação, ou confirmar que não é necessário caso não mostre
- [x] 6.4 Nas três telas acima, exibir também `outras_formacoes_mec` e `outros_cursos` quando preenchidos

## 7. Seeders, factories e testes

- [x] 7.1 Atualizar `database/factories/CandidatoFactory.php` para criar formação(ões) via relação em vez das colunas antigas
- [x] 7.2 Atualizar `database/seeders/CandidatoSeeder.php` e `database/seeders/CenariosTesteSeeder.php` para usar a nova relação
- [x] 7.3 Atualizar `tests/Feature/CandidatoRegistroCpfTest.php` (e qualquer outro teste que referencie as colunas antigas) para o novo formato
- [x] 7.4 Rodar a suíte de testes e corrigir quebras remanescentes ligadas a formação

## 8. Validação final

- [x] 8.1 Rodar as migrations do zero em ambiente local e conferir que candidatos existentes com formação preenchida aparecem corretamente na nova tabela
- [x] 8.2 Testar manualmente: cadastrar duas formações, remover uma, salvar, e confirmar que a candidatura, a visão do coordenador e a exportação de dados refletem a lista correta
- [x] 8.3 Testar manualmente os dois campos de texto livre (preenchidos e vazios) e confirmar que não bloqueiam a completude do perfil
