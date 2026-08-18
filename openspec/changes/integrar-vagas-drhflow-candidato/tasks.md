## 1. Reconhecimento do banco e conexão

- [x] 1.1 Confirmar contra `DB_DRHFLOW_TESTE` os nomes e tipos reais das colunas de `EN_VAGA_EMPREGO` e `EN_CANDIDATO_VAGA_EMPREGO`, resolvendo em particular `DE_DOCUMENTACAO_NECESSARIA` vs. `DE_DOCUMENTACAO_OBRIGATORIA`, o tipo e formato de `NU_CPF`, a existência de `DE_ATIVIDADES` e `CD_TIPO_ADMISSAO`, e o significado de `FG_SEL`. Registrar as divergências encontradas em relação ao documento
- [x] 1.2 Adicionar a conexão `drhflow` em `config/database.php` com as variáveis `DRHFLOW_*`, e remover a entrada `sqlsrv` morta que aponta para as variáveis do MySQL
- [x] 1.3 Adicionar as variáveis `DRHFLOW_*` ao `.env` local e ao `.env.example` (sem credenciais reais no exemplo), e remover as `DB_*2` não usadas
- [x] 1.4 Confirmar que `config/database.php` → `migrations.connection` está fixo na conexão do portal e escrever um teste que falha se alguma migration declarar a conexão `drhflow`
- [ ] 1.5 Solicitar ao DBA o usuário com `SELECT` amplo (incluindo `CorporeRM.dbo`) e escrita restrita a `EN_CANDIDATO_VAGA_EMPREGO`, e registrar no README qual permissão o ambiente exige

## 2. Camada de leitura das vagas

- [x] 2.1 Criar o DTO `VagaDrhflow` com os campos apresentados ao candidato e os acessores derivados (tipo de contratação legível, localização, prazo restante, remuneração formatada)
- [x] 2.2 Criar `VagaDrhflowRepository` com a consulta do design (D3), incluindo `DE_ATIVIDADES` e `CD_TIPO_ADMISSAO`, o filtro `CD_SITUACAO = 1` e `DT_LIMITE_PARA_INSCRICAO >= GETDATE()-1`, e a ordenação por `NOME_E_CBO`
- [x] 2.3 Implementar paginação via `OFFSET ... FETCH NEXT` e os filtros de D4 (busca, tipo de admissão, escolaridade, município, UF, faixa salarial, projeto)
- [x] 2.4 Implementar `buscarPorCodigo(int $cdVagaEmprego)` aplicando o mesmo critério de disponibilidade, devolvendo nulo para vaga inexistente ou fora do critério
- [x] 2.5 Implementar o log da diferença entre a contagem bruta de `CD_SITUACAO = 1` e o resultado após o `inner join` com `VW_FUNCAO_5ANOS`
- [x] 2.6 Criar o repositório de domínios (grau de instrução, UF, município, tipo de experiência, projeto, tipo de admissão) com as consultas do documento e cache de algumas horas
- [x] 2.7 Cobrir com testes: vaga aberta no prazo aparece, situação diferente de aberta não aparece, prazo vencido não aparece, vaga no último dia ainda aparece, vaga sem projeto e sem município não quebram

## 3. Listagem e detalhe públicos

- [x] 3.1 Trocar `VagaPublicaController::index` para o repositório, com os novos filtros, mantendo a forma do payload que o React já consome
- [x] 3.2 Trocar `VagaPublicaController::show` para resolver por `CD_VAGA_EMPREGO`, respondendo 404 para código inexistente ou fora do critério
- [x] 3.3 Ajustar `routes/web.php`: `/vagas/{vaga}` e `/candidatura/{vaga}` recebem o código do DRHFlow em vez de route-model binding
- [x] 3.4 Atualizar `resources/js/Pages/Publico/Vagas/Index.jsx`: remover os filtros de área, modalidade e curso; acrescentar escolaridade e projeto; manter busca, tipo, município, UF e faixa salarial
- [x] 3.5 Atualizar o painel de detalhe e `Publico/Vagas/Show.jsx` para os campos do DRHFlow (atividades, requisitos exigidos, benefícios, documentação necessária, escolaridade, experiência, carga horária, projeto, localização, prazo), omitindo por completo o que vier vazio
- [x] 3.6 Traduzir os códigos na apresentação: tipo de admissão (`A`/`N`/`U`/`T`), escolaridade e tipo de experiência nunca chegam à tela como código bruto
- [x] 3.7 Implementar o tratamento de indisponibilidade do DRHFlow: mensagem explícita e log, nunca uma listagem vazia silenciosa
- [x] 3.8 Verificar que o item de lista continua exibindo apenas cargo, localização, projeto e tempo restante, como já exige a spec da listagem

## 4. Armazenamento do currículo

- [x] 4.1 Adicionar o disco `curriculos` em `config/filesystems.php` com raiz `CURRICULOS_ROOT`, visibilidade privada e `throw => true`; documentar a variável no `.env.example`
- [x] 4.2 Alterar `Candidato::adicionarCurriculo()` para gravar em `{cpf}/{uuid}.pdf` no disco `curriculos`, criando a pasta quando não existir
- [x] 4.3 Atualizar os três pontos de download (`PerfilController`, `MinhaCandidaturaController`, `CandidaturaController`) para lerem do novo disco, mantendo a verificação de quem está pedindo
- [x] 4.4 Criar migration de dados que move os arquivos de `candidatos/curriculos` para a pasta do CPF e reescreve `candidato_curriculos.path`, registrando (sem apagar) os registros cujo arquivo não for encontrado, com `down()` que desfaz a movimentação
- [x] 4.5 Confirmar que a raiz configurada não é servível pela web e cobrir com teste que o download só responde ao dono autenticado

## 5. Complemento local da inscrição

- [x] 5.1 Criar a migration de `inscricao_complementos` conforme D6, com unique em (`cpf`, `cd_vaga_emprego`) e cascade a partir de `candidatos`
- [x] 5.2 Criar o model `InscricaoComplemento` com a relação para `Candidato` e para `CandidatoCurriculo`
- [x] 5.3 Confirmar que nenhuma coluna do complemento duplica dado já gravado no DRHFlow

## 6. Gravação da inscrição

- [x] 6.1 Criar o mapeador perfil → `EN_CANDIDATO_VAGA_EMPREGO` com o de-para direto de D7, gravando `NU_CPF` só com dígitos
- [x] 6.2 Implementar as traduções de D7: município do endereço por `EN_MUNICIPIO`, país por `EN_PAIS_IBGE`, grau de instrução por `PCODINSTRUCAO` (formação de maior grau) e cursos por `EN_CURSO_SUPERIOR` (até três), todas com normalização sem acento e sem caixa, e nulo quando não houver correspondência
- [x] 6.3 Levantar e centralizar em um único lugar a tabela de-para entre os rótulos de escolaridade do portal e os códigos de `PCODINSTRUCAO`
- [x] 6.4 Implementar `InscricaoDrhflowRepository::criar()` com verificação de existência, `INSERT` com lista explícita de colunas e captura do erro de chave duplicada como "já inscrito"
- [x] 6.5 Implementar a atualização de reenvio como `UPDATE` de colunas explícitas do candidato, com `WHERE NU_CPF = ? AND CD_VAGA_EMPREGO = ?`, nunca tocando nas colunas de entrevista, avaliação e nota
- [x] 6.6 Trocar `InscricaoController::store` para gravar primeiro no DRHFlow e depois o complemento local, com a falha do complemento registrada sem invalidar a inscrição
- [x] 6.7 Trocar a verificação de duplicidade de `jaSeInscreveuNa()` para consultar o DRHFlow por CPF + vaga, cobrindo o caso de inscrição preexistente feita fora do portal
- [x] 6.8 Manter o bloqueio de perfil incompleto e o gate de vaga aberta antes de qualquer escrita
- [x] 6.9 Implementar o tratamento de falha de gravação: a inscrição não é apresentada como recebida e o candidato é convidado a tentar novamente
- [x] 6.10 Cobrir com testes: inscrição cria a linha, CPF gravado sem máscara, colunas não coletadas ficam nulas, segunda tentativa é recusada, envio concorrente cria uma linha só, e `EN_VAGA_EMPREGO` permanece inalterada

## 7. Acompanhamento do candidato

- [x] 7.1 Implementar a derivação do andamento a partir de `DT_ENTREVISTA`, `HR_ENTREVISTA`, `DE_LOCAL_ENTREVISTA` e `VL_MEDIA_AVALIACAO`, sem apresentar aprovação ou reprovação
- [x] 7.2 Trocar `MinhaCandidaturaController::index` e `show` para listar as inscrições do CPF no DRHFlow, unindo os dados do complemento local
- [x] 7.3 Atualizar `Candidato/Candidaturas/Index.jsx` e `Show.jsx` para os novos estados e para exibir data, horário e local da entrevista quando o RH os preencher
- [x] 7.4 Cobrir com testes: inscrição recém-enviada aparece como recebida, entrevista preenchida no DRHFlow aparece para o candidato, e nenhum desfecho de aprovação é exibido

## 8. Exclusão de conta

- [ ] 8.1 Confirmar com o RH a decisão D11 (anonimizar a linha do DRHFlow em vez de apagá-la) antes de implementar; se recusada, trocar por bloqueio de exclusão com inscrição já enviada
- [x] 8.2 Estender `AnonimizacaoService` para substituir por marcadores os campos de identificação de todas as linhas do CPF em `EN_CANDIDATO_VAGA_EMPREGO`, preservando `NU_CPF`, `CD_VAGA_EMPREGO` e tudo que o RH preencheu
- [x] 8.3 Estender a exclusão para remover os arquivos da pasta do CPF no disco `curriculos`
- [x] 8.4 Cobrir com testes: nenhuma linha do DRHFlow é removida, os campos de identificação saem, e a exclusão continua recusada com processo em aberto

## 9. Verificação e entrega

- [x] 9.1 Escrever o teste que garante que nenhum caminho do portal emite `DELETE` na conexão `drhflow`, nem `INSERT`/`UPDATE` fora de `EN_CANDIDATO_VAGA_EMPREGO`
- [x] 9.2 Rodar a suíte completa e o Pint
- [x] 9.3 Executar uma inscrição real ponta a ponta em homologação, conferindo a linha criada no DRHFlow e o PDF na pasta do CPF
- [x] 9.4 Conferir a contagem de vagas do portal contra a consulta do documento executada direto no banco
- [ ] 9.5 Comunicar ao RH, antes do deploy: a colisão de identificadores com os links antigos, os alertas que não disparam para vagas do DRHFlow, e o valor escolhido para `ID_USUARIO_CAD`
