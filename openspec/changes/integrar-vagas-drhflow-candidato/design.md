## Context

Ver `proposal.md` — Why para a motivação. O que importa para o desenho:

- O portal é Laravel 13 + Inertia/React, hoje inteiramente sobre MySQL (`vagas`, `candidaturas`, `candidatos`, `candidato_curriculos`, `candidato_formacoes`, `candidatura_eventos`, `vaga_alertas`).
- As extensões `sqlsrv` e `pdo_sqlsrv` já estão carregadas no ambiente local; a entrada `sqlsrv` em `config/database.php` é boilerplate morto que aponta para as variáveis do MySQL e não é usada em lugar nenhum do código.
- Já existem no `.env` variáveis `DB_*2` apontando para um SQL Server (`manager_teste`) — também sem uso no código.
- `EN_CANDIDATO_VAGA_EMPREGO` não é um perfil: é um registro por par CPF + vaga, com os dados do candidato copiados dentro dele. Isso é o oposto do modelo do portal, onde o perfil é fonte única e viva (capacidade `perfil-candidato-unico`). A tensão entre os dois modelos é a decisão central deste desenho.
- O DRHFlow não tem tabela de conta, senha, verificação de e-mail, versionamento de currículo nem alertas. Nada disso pode sair do MySQL.
- O documento `DocVagaEmpregoFAPEU-1.pdf` descreve as consultas de todas as telas (cadastro de vaga, seleção, avaliação, processo agregado). Só a parte do candidato entra aqui.

## Goals / Non-Goals

**Goals:**

- Vagas lidas do DRHFlow em uma única fronteira de código, para que a troca da origem não vaze para controllers e componentes React.
- Inscrição gravada em `EN_CANDIDATO_VAGA_EMPREGO` de forma idempotente por CPF + vaga.
- Impossibilitar por construção — não por disciplina — que o portal apague ou altere dados de origem do banco de testes.
- Manter as funcionalidades atuais do candidato que o DRHFlow não comporta (carta de apresentação, versionamento de currículo, eventos), sem duplicar dado que o DRHFlow já guarda.

**Non-Goals:**

- Cache ou sincronização das vagas para o MySQL. A leitura é direta e ao vivo; se o desempenho exigir cache, é outra mudança.
- Escrita em qualquer tabela do DRHFlow que não seja `EN_CANDIDATO_VAGA_EMPREGO`.
- Eloquent com relacionamentos sobre as tabelas do DRHFlow. As consultas do documento envolvem views e joins entre bancos (`CorporeRM.dbo`) que não mapeiam bem para modelos.
- Remover as tabelas MySQL `vagas` e `candidaturas`. Coordenador e gestor ainda dependem delas.

## Decisions

### D1 — Conexão `drhflow` separada, com usuário só-leitura ampliado por exceção

Nova entrada `drhflow` em `config/database.php`, driver `sqlsrv`, alimentada por variáveis próprias:

```
DRHFLOW_HOST=150.162.78.4
DRHFLOW_PORT=1433
DRHFLOW_DATABASE=DB_DRHFLOW_TESTE
DRHFLOW_USERNAME=
DRHFLOW_PASSWORD=
DRHFLOW_ENCRYPT=yes
DRHFLOW_TRUST_SERVER_CERTIFICATE=true
```

Prefixo próprio (`DRHFLOW_*`) em vez de reaproveitar `DB_*2`: as variáveis numeradas não dizem para onde apontam e já apontam para outro banco (`manager_teste`).

A entrada `sqlsrv` morta é removida no mesmo passo — deixá-la ao lado de uma conexão SQL Server real é um convite a erro.

**Alternativa descartada:** trocar a conexão padrão da aplicação para o DRHFlow. Quebraria contas, sessões, filas e cache, que não têm tabela lá.

**Proteção de esquema:** a garantia da spec "Esquema do portal não alcança o DRHFlow" é obtida deixando `config/database.php` → `migrations.connection` fixo na conexão do portal e não declarando `$connection = 'drhflow'` em nenhuma migration. Além disso, o ideal é que o usuário do banco tenha apenas `SELECT` em tudo e `INSERT`/`UPDATE` restrito a `EN_CANDIDATO_VAGA_EMPREGO` — pedir isso ao DBA é tarefa da entrega, e é a única barreira que não depende do código.

> **Corrigido pelo reconhecimento (ver `reconhecimento-banco.md`):** o acesso a `CorporeRM.dbo` **não é necessário**. O próprio `DB_DRHFLOW_TESTE` expõe `VW_GRAU_INSTRUCAO_RM` e `VW_MUNICIPIO_RM` com os dados do RM. A permissão pedida ao DBA se restringe a `DB_DRHFLOW_TESTE`.
>
> A credencial em uso hoje é `sa` (membro de `sysadmin`), sem nenhuma restrição de banco. Enquanto a tarefa 1.5 não for cumprida, a única proteção contra escrita indevida é o código.

### D2 — Repositórios de consulta, não models Eloquent

`App\Support\Drhflow\VagaDrhflowRepository` e `InscricaoDrhflowRepository`, usando `DB::connection('drhflow')` com query builder, devolvendo DTOs (`VagaDrhflow`, `InscricaoDrhflow`) — objetos simples, não models.

Motivos: as consultas do documento envolvem `left outer join` com views e um join cruzando bancos (`CorporeRM.dbo.PSECAO`); a chave da inscrição é composta (`NU_CPF` + `CD_VAGA_EMPREGO`), que o Eloquent não suporta nativamente; e um model Eloquent traz `save()`, `delete()` e `truncate()` para perto de um banco onde nada disso pode acontecer.

**Alternativa descartada:** models Eloquent com `$connection = 'drhflow'` e `$table = 'EN_VAGA_EMPREGO'`. Mais familiar, mas expõe as operações destrutivas e não resolve a chave composta.

### D3 — Consulta de vagas: a do documento, mais duas colunas

A consulta base é a do documento (`Vagas Disponíveis`), com dois acréscimos de colunas da própria `EN_VAGA_EMPREGO` que o documento descreve na tela de cadastro mas omitiu do SELECT:

- `DE_ATIVIDADES` — é a descrição da vaga (item 9 do cadastro). Sem ela o candidato veria uma vaga sem texto.
- `CD_TIPO_ADMISSAO` — é o tipo de contratação (item 10: `A` autônomo, `N` celetista, `U` bolsista, `T` estagiário). É o que substitui o filtro `tipo` do portal.

Nota sobre o documento: a tela de cadastro chama o campo de `DE_DOCUMENTACAO_OBRIGATORIA` (item 17) e a consulta de vagas disponíveis chama de `DE_DOCUMENTACAO_NECESSARIA`. **Confirmado contra o banco: `DE_DOCUMENTACAO_NECESSARIA` é o nome real.** A consulta do RH estava certa.

Também confirmado: `CD_TIPO_ADMISSAO` (`varchar(1)`) é a coluna viva. Existe uma `CD_TIPO_CONTRATACAO` (`int`) em `EN_VAGA_EMPREGO` que está **100% nula** — coluna morta, não confundir.

`VL_SALARIO` vem `0` em 117 vagas; `0` significa "não informado", não salário zero.

```sql
SELECT
  v.CD_VAGA_EMPREGO, v.CD_SITUACAO, v.FG_ATIVA,
  v.CD_CENTRO, v.CD_DEPARTAMENTO, v.CD_PROJETO, v.CD_FUNCAO,
  v.CD_ESCOLARIDADE_EXIGIDA, v.CD_TIPO_EXPERIENCIA, v.CD_HORARIO,
  v.CD_TIPO_ADMISSAO, v.DE_ATIVIDADES,
  v.DE_BENEFICIOS, v.DE_CARGA_HORARIA, v.DE_DOCUMENTACAO_NECESSARIA,
  v.DE_REQUISITOS_EXIGIDOS, v.VL_SALARIO,
  v.DT_CADASTRO, v.DT_ALTERACAO, v.DT_LIMITE_PARA_INSCRICAO,
  v.CD_UF, v.CD_MUNICIPIO, m.NM_MUNICIPIO,
  p.projeto_rubrica_nome, f.NOME_E_CBO
FROM EN_VAGA_EMPREGO v
LEFT OUTER JOIN VW_PROJETO       p ON v.CD_PROJETO  = p.ano_codigo
INNER      JOIN VW_FUNCAO_5ANOS  f ON v.CD_FUNCAO   = f.CODIGO
LEFT OUTER JOIN EN_MUNICIPIO     m ON v.CD_MUNICIPIO = m.CD_MUNICIPIO
WHERE v.CD_SITUACAO = 1
  AND v.DT_LIMITE_PARA_INSCRICAO >= GETDATE() - 1
ORDER BY f.NOME_E_CBO ASC
```

O `inner join` com `VW_FUNCAO_5ANOS` significa que uma vaga com função inexistente na view **desaparece da listagem**. É o comportamento definido pelo RH e é mantido, mas a diferença entre a contagem bruta de `CD_SITUACAO = 1` e o resultado da consulta deve ser registrada em log para que uma vaga sumida seja diagnosticável.

**Paginação:** feita no SQL Server com `OFFSET ... FETCH NEXT` sobre a consulta filtrada, não em PHP. O `ORDER BY` já existe, que é o que o `OFFSET` exige.

### D4 — Filtros: os que a origem sustenta

| Filtro atual | Destino |
| --- | --- |
| busca | `DE_ATIVIDADES` e `f.NOME_E_CBO` (`LIKE`) |
| área | **removido** — sem coluna equivalente |
| modalidade | **removido** — sem coluna equivalente |
| curso desejado | **removido** — sem coluna equivalente |
| tipo | `CD_TIPO_ADMISSAO` |
| cidade | `m.NM_MUNICIPIO` |
| estado | `v.CD_UF` |
| salário mín./máx. | `v.VL_SALARIO` (uma coluna só; o filtro de faixa vira comparação simples) |
| — | **novo:** escolaridade exigida (`CD_ESCOLARIDADE_EXIGIDA`) |
| — | **novo:** projeto (`CD_PROJETO`) |

As opções de cada select vêm de consultas de domínio no próprio DRHFlow, memorizadas em cache por algumas horas (mudam raramente): grau de instrução de **`VW_GRAU_INSTRUCAO_RM`** (não `CorporeRM.dbo.PCODINSTRUCAO` — a view local tem os mesmos 17 registros e dispensa o acesso cross-database), UFs de `EN_UF`, municípios de `EN_MUNICIPIO`, tipos de experiência de `EN_TIPO_EXPERIENCIA`, tipos de admissão de `EN_TIPO_ADMISSAO`. A ordenação de cada uma é a definida no documento.

**Atenção em `EN_TIPO_ADMISSAO`:** a chave da tabela (`CD_TIPO_ADMISSAO`) vale `A`/`N`/`O`/`T`, mas o valor gravado em `EN_VAGA_EMPREGO.CD_TIPO_ADMISSAO` é o da coluna `CD_RM`, que vale `A`/`N`/`U`/`T` — bolsista é `O` na tabela de domínio e `U` na vaga. Qualquer join é por `CD_RM`.

`Vaga::$areas` e `Vaga::$cursos` continuam existindo no model MySQL — o coordenador ainda os usa. Só saem das telas públicas.

### D5 — Identificador da vaga e rotas

`/vagas/{vaga}` deixa de usar route-model binding e passa a receber `CD_VAGA_EMPREGO` como inteiro, resolvido pelo repositório. Mesma coisa em `/candidatura/{vaga}`.

Os IDs do MySQL e os `CD_VAGA_EMPREGO` são numéricos e vão colidir. Não há como distinguir um link antigo de um novo, então links antigos passam a resolver para a vaga de mesmo código no DRHFlow ou para 404. Isso é aceitável porque as vagas do MySQL são de um portal que está sendo substituído, mas precisa ser dito ao RH antes do deploy.

### D6 — Tabela local de complemento

Migration nova (MySQL): `inscricao_complementos`.

| Coluna | Tipo | Papel |
| --- | --- | --- |
| `id` | bigint PK | |
| `candidato_id` | FK → `candidatos`, cascade on delete | dono |
| `cpf` | char(11) | chave de correlação com o DRHFlow |
| `cd_vaga_emprego` | int | chave de correlação com o DRHFlow |
| `carta_apresentacao` | text null | sem campo no DRHFlow |
| `conflito_interesse` | boolean | resposta única do portal |
| `conflito_interesse_detalhe` | text null | sem campo no DRHFlow |
| `curriculo_id_vigente` | FK → `candidato_curriculos` null | qual PDF o processo recebeu |
| `enviada_em` | timestamp | |
| — | unique(`cpf`, `cd_vaga_emprego`) | espelha a chave da origem |

Fica de fora tudo que o DRHFlow já guarda: nome, e-mail, telefone, endereço, aceite do código de conduta, data/hora/local de entrevista, notas. A spec `perfil-candidato-unico` proíbe a cópia, e uma cópia divergiria em silêncio.

`candidaturas` e `candidatura_eventos` não são tocadas nem removidas — continuam servindo o coordenador sobre as vagas MySQL.

### D7 — Mapeamento perfil → `EN_CANDIDATO_VAGA_EMPREGO`

**Direto:**

| Perfil | Coluna |
| --- | --- |
| `cpf` (11 dígitos) | `NU_CPF` |
| código da vaga | `CD_VAGA_EMPREGO` |
| `nome` | `NM_CANDIDATO` |
| `nome_social` | `NM_SOCIAL` |
| `email` | `DE_EMAIL` |
| `telefone` | `NU_TELEFONE_CELULAR` |
| `cep` | `NU_CEP` |
| `logradouro` | `NM_LOGRADOURO` |
| `numero` | `NU_LOGRADOURO` |
| `complemento` | `NM_COMPLEMENTO_LOGRADOURO` |
| `bairro` | `NM_BAIRRO` |
| `estado` | `CD_UF_ENDERECO` |
| `outras_formacoes_mec` | `DE_OUTRAS_FORMACOES` |
| `outros_cursos` | `DE_OUTROS_CURSOS` |
| `pcd` | `FG_PCD` |
| `codigo_conduta_aceito_em` preenchido | `FG_LEU_CODIGO_CONDUTA_FAPEU` |
| `now()` | `DT_CADASTRO` |
| identificador do portal | `ID_USUARIO_CAD` |

**Com tradução:**

- `cidade` + `estado` → `CD_MUNICIPIO_ENDERECO`, por busca em **`VW_MUNICIPIO_RM`** (nome normalizado, sem acento e sem caixa, dentro da UF). Sem correspondência: fica nulo; o endereço textual já foi gravado.

  > **Corrigido pelo reconhecimento:** este campo **não** usa `EN_MUNICIPIO`. É `varchar(15)` no código do RM, com zeros à esquerda — Florianópolis é `05407` aqui e `1653` em `EN_MUNICIPIO`. Traduzir por `EN_MUNICIPIO` gravaria Santo Antônio do Amparo/MG. `VW_MUNICIPIO_RM.CODMUNICIPIO` não é único sozinho (`05407` existe em CE e SC), então a busca casa nome **e** UF. `EN_MUNICIPIO` continua sendo o domínio certo para o município **da vaga** (`CD_MUNICIPIO`, `int`) — são dois domínios distintos na mesma base.

- `pais` → `CD_PAIS`, por busca em `EN_PAIS_IBGE`. Padrão do perfil é "Brasil" (`76`).
- `formacoes[].nivel_escolaridade` → `CD_GRAU_INSTRUCAO`, tomando **a formação de maior grau** e casando com **`VW_GRAU_INSTRUCAO_RM`**. Precisa de uma tabela de-para explícita entre os rótulos do portal e os 17 códigos do RM (`1`–`9`, `A`–`H`), mantida em um único lugar.

**Flags:** todas as colunas `FG_*` são `varchar(1)` com `S`/`N` — não booleano, não `1`/`0`.
- `formacoes[].curso` → `CD_CURSO_SUPERIOR1/2/3`, casando o nome do curso com `EN_CURSO_SUPERIOR`, até três, na ordem de cadastro. Curso sem correspondência é ignorado nessas colunas e continua legível em `DE_OUTROS_CURSOS`.

**Deixados nulos** (o perfil não coleta): `DT_NASCIMENTO`, `FG_SEXO`, `CD_ESTADO_CIVIL`, `NU_PISPASEP`, `NU_CTPS`, `UF_CTPS`, `NU_SERIE_CTPS`, `CD_NACIONALIDADE`, `CD_ESTADO_NATAL`, `CD_NATURALIDADE`, `CD_COR_RACA`, `NU_RG`, `NM_EMISSOR_RG`, `UF_RG`, `DT_EMISSAO_RG`, `NU_REGISTRO_PROFISSIONAL`, `NM_EMISSOR_REGISTRO_PROFISSIONAL`, `DT_EMISSAO_REGISTRO_PROFISSIONAL`, `NU_TELEFONE_FIXO`, `CD_TIPO_RUA`, `CD_TIPO_BAIRRO`, `FG_TEM_DIVIDA_BANCO`, `FG_PARENTE_*`, e todos os campos de entrevista, avaliação e nota (que pertencem ao RH).

Sobre `FG_PARENTE_*`: o portal tem uma pergunta única de conflito de interesse; o DRHFlow tem cinco sinalizadores distintos (servidor, dirigente, diretor, coordenador de projeto, fiscal de contrato). Traduzir um para cinco exigiria inventar qual. Ficam nulos, e a resposta única fica no complemento local até que o formulário seja expandido.

Sobre `FG_DEFICIENTE_*`: `pcd_tipo` é texto livre no portal e não mapeia com segurança para os sete sinalizadores. Só `FG_PCD` é gravado.

### D8 — Gravação idempotente por CPF + vaga

O envio é um `INSERT` precedido de verificação de existência, dentro de uma transação na conexão `drhflow`. Se o `INSERT` violar a chave primária por corrida, o erro de chave duplicada é capturado e tratado como "já inscrito" — não como falha.

A escrita **nunca** é um `UPDATE` cego da linha inteira: o RH grava nessa mesma linha as datas de entrevista e as notas, e um update completo do portal apagaria o trabalho dele. Reenvios atualizam apenas as colunas de dados do candidato, com `WHERE NU_CPF = ? AND CD_VAGA_EMPREGO = ?` e lista explícita de colunas.

A transação cobre a linha do DRHFlow e a linha do complemento local — que estão em bancos diferentes, portanto sem transação distribuída. A ordem é: grava no DRHFlow primeiro (é a que importa para o RH), depois o complemento local. Se o complemento falhar, a inscrição vale e a falha é registrada; o inverso perderia a inscrição.

### D9 — Disco `curriculos` com raiz configurável

```php
'curriculos' => [
    'driver'     => 'local',
    'root'       => env('CURRICULOS_ROOT', storage_path('app/private/Curriculos')),
    'visibility' => 'private',
    'throw'      => true,
],
```

`Candidato::adicionarCurriculo()` passa a gravar em `{cpf}/{uuid}.pdf` nesse disco — pasta por CPF, como o DRHFlow espera, e nome de arquivo não adivinhável. Em produção, `CURRICULOS_ROOT=/home/Curriculos`.

`throw => true` porque uma falha silenciosa de gravação hoje deixaria o perfil apontando para um arquivo que não existe.

Currículos já gravados em `candidatos/curriculos` continuam onde estão; o `path` guardado em `candidato_curriculos` já é relativo ao disco, então uma migration de dados move os arquivos e reescreve os paths. Se um arquivo antigo não for encontrado, o registro é deixado como está e a ocorrência é registrada — não se apaga nada.

### D10 — Andamento derivado, sem campo de status próprio

O candidato vê o andamento derivado das colunas do DRHFlow, conforme a spec `vagas-drhflow`. Os estados `aprovado` e `reprovado` deixam de ser apresentados: nenhuma coluna documentada os determina. `FG_SEL` existe na lista de colunas, mas o próprio documento afirma que "o sistema sabe que o candidato foi selecionado quando ele tem data de entrevista" — então `DT_ENTREVISTA` é a fonte, e `FG_SEL` não é usado até que seu significado seja confirmado.

### D11 — Exclusão de conta: anonimizar, nunca apagar

A instrução do RH é que nada seja apagado do banco de testes; a LGPD exige que a exclusão de conta remova os dados pessoais. As duas se conciliam com `UPDATE` de anonimização na própria linha do candidato — nome, nome social, e-mail, telefone e endereço substituídos por marcadores —, mantendo `NU_CPF`, `CD_VAGA_EMPREGO` e tudo que o RH preencheu. O registro do processo permanece; a identidade sai.

`AnonimizacaoService` ganha esse passo, aplicado a todas as linhas do CPF excluído, além da remoção dos arquivos da pasta do CPF.

**Esta decisão precisa de confirmação do RH antes do deploy.** É a única escrita do portal que altera dado já existente por iniciativa própria, e o RH pode preferir que o portal não toque em registro de processo — nesse caso a alternativa é não permitir exclusão de conta com inscrição já enviada ao DRHFlow, o que é uma restrição bem mais dura para o candidato.

## Risks / Trade-offs

- **Latência e disponibilidade do SQL Server na rota mais visitada do portal** → A home passa a depender de um banco externo. Mitigação: timeout curto na conexão, mensagem explícita de indisponibilidade (nunca lista vazia) e log de falha. Cache de listagem fica para uma mudança futura, se medição mostrar necessidade.
- **`inner join` com `VW_FUNCAO_5ANOS` esconde vagas** → Uma vaga com função fora da view some sem aviso. Mitigação: log da diferença entre a contagem bruta e a filtrada.
- **De-para de grau de instrução e de curso superior é frágil** → Casamento por nome erra com acentuação, abreviação e grafia. Mitigação: normalização (sem acento, sem caixa), e ausência de correspondência deixa a coluna nula em vez de gravar um código errado — um nulo é diagnosticável, um código errado não.
- **Colisão de identificadores entre o MySQL e o DRHFlow** → Links antigos podem resolver para a vaga errada. Mitigação: comunicar ao RH antes do deploy; o volume de links externos é pequeno.
- **Duas escritas sem transação distribuída** → O complemento local pode falhar depois da gravação no DRHFlow. Mitigação: ordem de gravação escolhida para que a perda seja do dado menos crítico, com log e possibilidade de reconciliação por CPF + vaga.
- **Alertas silenciosamente inertes** → Os alertas continuam ligados às vagas MySQL e não dispararão para vagas do DRHFlow. Mitigação: dito explicitamente ao RH e ao candidato na tela de alertas; resolvido na mudança do coordenador.
- **Escrita em banco compartilhado com o RH** → Ainda que de testes, o RH usa esse banco. Mitigação: permissão de banco restrita (D1), lista explícita de colunas em toda escrita (D8) e nenhuma migration na conexão.

## Migration Plan

1. Confirmar contra o banco os nomes de coluna em dúvida (`DE_DOCUMENTACAO_NECESSARIA` vs. `DE_DOCUMENTACAO_OBRIGATORIA`), o tipo e o formato de `NU_CPF`, e o significado de `FG_SEL`.
2. Pedir ao DBA o usuário com `SELECT` amplo e escrita restrita a `EN_CANDIDATO_VAGA_EMPREGO`.
3. Subir conexão, disco e migrations locais (`inscricao_complementos` + movimentação dos currículos) — nada disso muda comportamento visível ainda.
4. Trocar leitura das vagas, depois a inscrição, depois o acompanhamento.
5. Validar em homologação com uma inscrição real ponta a ponta, conferindo a linha criada no DRHFlow e o arquivo na pasta do CPF.

**Rollback:** as rotas públicas voltam ao caminho MySQL revertendo o commit — as tabelas `vagas` e `candidaturas` não são alteradas nem removidas em nenhum passo. As inscrições já gravadas no DRHFlow permanecem lá, o que é o desejado. A migration de movimentação de currículos tem `down()` que devolve os arquivos ao caminho anterior.

## Open Questions

- Qual valor colocar em `ID_USUARIO_CAD` para identificar o portal? Precisa ser um identificador que o RH reconheça nos relatórios. Não muda o desenho — é um valor de configuração. O reconhecimento mostrou que a coluna (`varchar(25)`) está **vazia nas 4469 linhas existentes**: não há convenção pré-existente a seguir, a escolha é livre. Implementado como `DRHFLOW_ID_USUARIO_CAD`, com padrão `PORTALVAGAS`.
- Existe um endereço de e-mail ou rotina do RH que deva ser notificada a cada inscrição recebida, como hoje o coordenador é notificado? Fora do escopo desta mudança, mas vale perguntar antes do deploy.
