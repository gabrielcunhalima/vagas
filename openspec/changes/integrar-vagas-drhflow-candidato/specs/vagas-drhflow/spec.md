## Purpose

Define o DRHFlow como fonte de dados do portal no lado do candidato: de onde as vagas disponíveis são lidas, quais colunas da origem sustentam cada informação apresentada, onde a inscrição do candidato é gravada, como o andamento do processo é derivado do que o RH registra, e quais garantias o portal oferece de que não destrói dados do banco de origem.

## ADDED Requirements

### Requirement: Conexão dedicada e isolada ao DRHFlow

O portal SHALL acessar o DRHFlow por uma conexão de banco dedicada, distinta da conexão onde vivem as contas de candidato, as sessões e os dados próprios do portal. As credenciais e o nome do banco SHALL vir de configuração de ambiente, sem valor de produção embutido no código.

A indisponibilidade do DRHFlow SHALL produzir uma falha explícita e registrada, nunca uma lista de vagas silenciosamente vazia que o candidato leia como "não há vagas abertas".

#### Scenario: Credenciais vêm do ambiente

- **WHEN** o portal é iniciado em um ambiente qualquer
- **THEN** o banco, o host e as credenciais do DRHFlow são lidos da configuração daquele ambiente
- **AND** nenhum desses valores está fixo no código-fonte

#### Scenario: DRHFlow indisponível na listagem

- **WHEN** o candidato abre a listagem de vagas e a conexão com o DRHFlow falha
- **THEN** o portal informa que as vagas estão temporariamente indisponíveis
- **AND** registra a falha para diagnóstico
- **AND** não apresenta a página como se o resultado fosse zero vagas

#### Scenario: DRHFlow indisponível no envio da inscrição

- **WHEN** o candidato envia uma inscrição e a gravação no DRHFlow falha
- **THEN** o portal informa que o envio não foi concluído e que ele pode tentar novamente
- **AND** não apresenta a inscrição como recebida

### Requirement: Critério de vaga disponível ao candidato

O portal SHALL apresentar ao candidato exclusivamente as vagas do DRHFlow cuja situação seja "aberta" (`EN_VAGA_EMPREGO.CD_SITUACAO = 1`) e cujo limite de inscrição (`DT_LIMITE_PARA_INSCRICAO`) não tenha passado, aplicando a mesma tolerância de um dia usada pelo DRHFlow.

Vagas fora desse critério SHALL NOT aparecer na listagem nem ser acessíveis por endereço direto, e SHALL NOT aceitar inscrição.

#### Scenario: Vaga aberta dentro do prazo

- **WHEN** existe no DRHFlow uma vaga com situação aberta e limite de inscrição futuro
- **THEN** ela aparece na listagem pública

#### Scenario: Vaga com situação diferente de aberta

- **WHEN** existe no DRHFlow uma vaga cuja situação não é "aberta"
- **THEN** ela não aparece na listagem
- **AND** o acesso direto ao seu endereço resulta em vaga não encontrada

#### Scenario: Vaga com prazo vencido

- **WHEN** o limite de inscrição de uma vaga já passou
- **THEN** ela deixa de aparecer na listagem
- **AND** uma tentativa de inscrição nela é recusada

#### Scenario: Vaga no último dia do prazo

- **WHEN** o limite de inscrição de uma vaga é a data de hoje
- **THEN** ela continua disponível para inscrição

### Requirement: Vaga identificada pelo código do DRHFlow

Cada vaga apresentada ao candidato SHALL ser identificada pelo seu código no DRHFlow (`CD_VAGA_EMPREGO`). Os endereços de detalhe e de candidatura SHALL usar esse código.

#### Scenario: Endereço da vaga usa o código do DRHFlow

- **WHEN** o candidato abre o detalhe de uma vaga
- **THEN** o endereço da página contém o código da vaga no DRHFlow

#### Scenario: Código inexistente

- **WHEN** o candidato acessa o endereço de um código de vaga que não existe no DRHFlow
- **THEN** o portal responde vaga não encontrada

### Requirement: Correspondência entre as colunas do DRHFlow e o que o candidato vê

Cada informação de vaga apresentada ao candidato SHALL ter origem determinada nas colunas do DRHFlow, a saber:

| Informação apresentada | Origem |
| --- | --- |
| Título do cargo | `VW_FUNCAO_5ANOS.NOME_E_CBO` |
| Descrição das atividades | `EN_VAGA_EMPREGO.DE_ATIVIDADES` |
| Tipo de contratação | `EN_VAGA_EMPREGO.CD_TIPO_ADMISSAO` |
| Requisitos exigidos | `EN_VAGA_EMPREGO.DE_REQUISITOS_EXIGIDOS` |
| Benefícios | `EN_VAGA_EMPREGO.DE_BENEFICIOS` |
| Documentação necessária | `EN_VAGA_EMPREGO.DE_DOCUMENTACAO_NECESSARIA` |
| Remuneração | `EN_VAGA_EMPREGO.VL_SALARIO` |
| Carga horária | `EN_VAGA_EMPREGO.DE_CARGA_HORARIA` |
| Escolaridade exigida | `EN_VAGA_EMPREGO.CD_ESCOLARIDADE_EXIGIDA` |
| Experiência exigida | `EN_VAGA_EMPREGO.CD_TIPO_EXPERIENCIA` |
| Localização | `EN_MUNICIPIO.NM_MUNICIPIO` e `EN_VAGA_EMPREGO.CD_UF` |
| Projeto | `VW_PROJETO.projeto_rubrica_nome` |
| Prazo de inscrição | `EN_VAGA_EMPREGO.DT_LIMITE_PARA_INSCRICAO` |

Códigos SHALL ser apresentados ao candidato por sua descrição legível, nunca pelo valor bruto. Uma informação ausente na origem SHALL ser omitida por completo, sem rótulo órfão.

#### Scenario: Tipo de contratação traduzido

- **WHEN** uma vaga tem tipo de admissão `N` no DRHFlow
- **THEN** o candidato vê "Celetista", e não a letra `N`

#### Scenario: Escolaridade traduzida

- **WHEN** uma vaga tem código de escolaridade exigida
- **THEN** o candidato vê a descrição correspondente do cadastro de grau de instrução, e não o código

#### Scenario: Vaga sem projeto associado

- **WHEN** uma vaga do DRHFlow não tem projeto correspondente
- **THEN** o portal omite o projeto por completo, sem rótulo nem espaço reservado

#### Scenario: Vaga sem município informado

- **WHEN** uma vaga do DRHFlow não tem município correspondente
- **THEN** o portal apresenta a localização com a informação que tiver, sem exibir campo vazio

### Requirement: Inscrição gravada no DRHFlow por CPF e vaga

O envio de uma inscrição SHALL gravar um registro em `EN_CANDIDATO_VAGA_EMPREGO`, identificado pelo CPF do candidato autenticado e pelo código da vaga. O CPF SHALL ser gravado somente com dígitos, sem máscara.

O portal SHALL gravar os campos que o perfil do candidato já coleta. Os demais campos da tabela SHALL permanecer nulos, sem valor inventado para preencher a coluna.

O registro SHALL identificar o portal como origem do cadastro nos campos de auditoria da tabela.

#### Scenario: Inscrição cria o registro no DRHFlow

- **WHEN** um candidato com perfil completo envia uma inscrição em uma vaga aberta
- **THEN** existe em `EN_CANDIDATO_VAGA_EMPREGO` um registro com o CPF dele e o código daquela vaga
- **AND** o registro contém nome, nome social, e-mail, telefone, endereço, país, grau de instrução e cursos superiores tal como estão no perfil

#### Scenario: CPF gravado sem máscara

- **WHEN** o candidato tem o CPF cadastrado com pontuação no perfil
- **THEN** o registro no DRHFlow contém apenas os onze dígitos

#### Scenario: Campos não coletados permanecem nulos

- **WHEN** uma inscrição é gravada e o perfil não coleta data de nascimento, carteira de trabalho, PIS/PASEP nem naturalidade
- **THEN** essas colunas ficam nulas no registro
- **AND** nenhum valor de preenchimento é gravado no lugar

#### Scenario: Auditoria do registro

- **WHEN** uma inscrição é gravada pelo portal
- **THEN** o registro indica a data de cadastro e identifica o portal como autor

### Requirement: Andamento apresentado ao candidato deriva do DRHFlow

O andamento de uma inscrição apresentado ao candidato SHALL ser derivado do que o DRHFlow registra no seu registro de `EN_CANDIDATO_VAGA_EMPREGO`, e não de um campo de status próprio do portal:

- registro existente sem data de entrevista: inscrição recebida;
- registro com data de entrevista preenchida: entrevista marcada, com data, horário e local;
- registro com média de avaliação preenchida: avaliação concluída.

O portal SHALL NOT apresentar ao candidato um desfecho de aprovação ou reprovação enquanto o DRHFlow não expuser um campo que o determine.

#### Scenario: Inscrição recém-enviada

- **WHEN** o candidato consulta uma inscrição cujo registro não tem data de entrevista
- **THEN** o portal apresenta a inscrição como recebida

#### Scenario: Entrevista marcada pelo RH

- **WHEN** o RH preenche data, horário e local de entrevista no DRHFlow
- **THEN** o candidato passa a ver a entrevista marcada com essas informações, sem qualquer ação do portal

#### Scenario: Desfecho não inventado

- **WHEN** o candidato consulta uma inscrição já avaliada
- **THEN** o portal apresenta que a avaliação foi concluída
- **AND** não afirma aprovação nem reprovação

### Requirement: Preservação dos dados do banco de origem

O portal SHALL NOT apagar nenhum registro do DRHFlow. As escritas do portal SHALL se restringir a `EN_CANDIDATO_VAGA_EMPREGO`, e apenas ao registro correspondente ao CPF do candidato autenticado e à vaga em questão.

O portal SHALL NOT escrever em `EN_VAGA_EMPREGO`, nas views (`VW_PROJETO`, `VW_FUNCAO_5ANOS`, `VW_HORARIO_REQUISICAO`), nas tabelas de domínio (`EN_MUNICIPIO`, `EN_UF`, `EN_TIPO_EXPERIENCIA`, `EN_SITUACAO_VAGA_EMPREGO`, `EN_CURSO_SUPERIOR`, `EN_PAIS_IBGE`) nem em qualquer objeto do banco `CorporeRM`.

As rotinas de criação e alteração de esquema do portal SHALL NOT ser executáveis sobre a conexão do DRHFlow.

#### Scenario: Nenhuma remoção

- **WHEN** qualquer fluxo do portal é executado
- **THEN** nenhum registro do DRHFlow é removido

#### Scenario: Escrita restrita ao próprio registro

- **WHEN** um candidato autenticado envia ou atualiza uma inscrição
- **THEN** a escrita atinge apenas o registro de `EN_CANDIDATO_VAGA_EMPREGO` com o CPF dele e o código daquela vaga

#### Scenario: Tabela de vagas nunca alterada

- **WHEN** um candidato se inscreve em uma vaga
- **THEN** o registro daquela vaga em `EN_VAGA_EMPREGO` permanece inalterado

#### Scenario: Esquema do portal não alcança o DRHFlow

- **WHEN** as rotinas de esquema do portal são executadas
- **THEN** elas atuam somente sobre o banco próprio do portal
- **AND** nenhum objeto do DRHFlow é criado, alterado ou removido
