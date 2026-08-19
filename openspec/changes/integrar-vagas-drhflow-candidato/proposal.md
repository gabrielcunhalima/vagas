## Why

O portal hoje é uma ilha: as vagas vivem na tabela `vagas` do MySQL local e as inscrições em `candidaturas`, sem nenhuma relação com o DRHFlow — o sistema onde o RH da FAPEU realmente cadastra vagas e conduz o processo seletivo. Isso obriga o RH a manter dois cadastros da mesma vaga e deixa as inscrições recebidas pelo portal fora do fluxo de seleção, entrevista e avaliação que o DRHFlow já implementa.

Esta mudança liga o portal ao banco `DB_DRHFLOW_TESTE` (SQL Server), começando pelo lado do candidato — a parte que está em produção hoje e que precisa ser substituída primeiro. Gestor e coordenador permanecem no caminho legado e serão migrados em mudanças posteriores.

## What Changes

**Fonte das vagas**

- Nova conexão de banco `drhflow` (SQL Server, `DB_DRHFLOW_TESTE`), separada da conexão MySQL da aplicação.
- A listagem pública e a página de detalhe da vaga passam a ler de `EN_VAGA_EMPREGO`, com os joins de `VW_PROJETO`, `VW_FUNCAO_5ANOS` e `EN_MUNICIPIO`, filtrando `CD_SITUACAO = 1` e `DT_LIMITE_PARA_INSCRICAO >= getdate()-1`.
- **BREAKING**: a vaga passa a ser identificada por `CD_VAGA_EMPREGO`, não pelo `id` da tabela MySQL. Os links `/vagas/{id}` existentes deixam de resolver.
- **BREAKING**: os filtros `área`, `modalidade` e `curso desejado` deixam de existir na listagem pública — `EN_VAGA_EMPREGO` não tem coluna equivalente. Entram no lugar filtros que a origem sustenta: função/CBO, tipo de admissão, escolaridade exigida, município/UF, faixa salarial e projeto.

**Destino das inscrições**

- A inscrição do candidato passa a ser gravada em `EN_CANDIDATO_VAGA_EMPREGO`, chaveada por `NU_CPF` + `CD_VAGA_EMPREGO`.
- O acompanhamento do candidato ("Minhas candidaturas") passa a ler dessa tabela, com o status derivado do que o DRHFlow registra: inscrição recebida, entrevista marcada (`DT_ENTREVISTA`) e avaliação concluída (`VL_MEDIA_AVALIACAO`).
- Só os campos que o perfil já coleta são mapeados; as demais ~50 colunas de `EN_CANDIDATO_VAGA_EMPREGO` (RG, CTPS, PIS/PASEP, naturalidade, cor/raça, tipos de deficiência, parentesco, dívida) ficam nulas e serão preenchidas quando o formulário for expandido, em mudança separada.
- Uma tabela local de complemento guarda o que o DRHFlow não tem campo para receber (carta de apresentação, vínculo com a versão de currículo enviada, eventos da candidatura), preservando as funcionalidades atuais do portal.

**Currículo**

- Novo disco de arquivos `curriculos`, com raiz configurável por ambiente, organizando os PDFs em uma pasta por CPF — o layout que o DRHFlow espera (`/home/Curriculos/{cpf}/`). Hoje eles são gravados em `storage/app/private/candidatos/curriculos`, sem separação por candidato.

**Garantias sobre o banco de testes**

- A conexão `drhflow` grava exclusivamente em `EN_CANDIDATO_VAGA_EMPREGO`, e apenas na linha do próprio candidato autenticado. Nenhum `DELETE`, nenhuma escrita em `EN_VAGA_EMPREGO` ou nas views, nenhuma migration do Laravel apontando para essa conexão.

## Capabilities

### New Capabilities

- `vagas-drhflow`: a integração com o DRHFlow como fonte de dados do lado do candidato — a conexão dedicada, o contrato de leitura das vagas disponíveis, o mapeamento entre as colunas da origem e o que o portal apresenta, a persistência da inscrição em `EN_CANDIDATO_VAGA_EMPREGO`, e as garantias de preservação dos dados do banco de testes.

### Modified Capabilities

- `vagas-listagem-publica`: os campos exibidos em cada vaga e os filtros disponíveis passam a ser os que o DRHFlow sustenta; área, modalidade e curso desejado deixam de existir.
- `candidatura-vinculada-a-conta`: a inscrição passa a ser gravada no DRHFlow, a duplicidade passa a ser aferida por CPF + vaga, e o status apresentado ao candidato passa a ser derivado do que o DRHFlow registra em vez de um campo próprio do portal.
- `perfil-candidato-unico`: o currículo passa a ser armazenado em pasta por CPF, em disco de raiz configurável.

## Impact

**Código afetado**

- `config/database.php`, `config/filesystems.php`, `.env` / `.env.example` — conexão `drhflow` e disco `curriculos`.
- `app/Http/Controllers/Vagas/VagaPublicaController.php`, `InscricaoController.php` — leitura e escrita passam para o DRHFlow.
- `app/Http/Controllers/Candidato/MinhaCandidaturaController.php` — acompanhamento lê do DRHFlow.
- `app/Models/Candidato.php` (`adicionarCurriculo`) e `app/Http/Controllers/Candidato/PerfilController.php` — novo disco e caminho por CPF.
- `resources/js/Pages/Publico/Vagas/Index.jsx` e `Show.jsx`, `Publico/Candidatura.jsx`, `Candidato/Candidaturas/*.jsx` — campos e filtros novos.
- `routes/web.php` — binding da vaga por `CD_VAGA_EMPREGO`.
- Novos: model/repositório de leitura das vagas do DRHFlow, model da inscrição, migration da tabela local de complemento.

**Dependências e ambiente**

- Extensões `sqlsrv` e `pdo_sqlsrv` (já carregadas no ambiente local).
- O usuário da conexão precisa de leitura em `DB_DRHFLOW_TESTE`. O acesso a `CorporeRM.dbo` que o documento sugeria **não é necessário** — `VW_GRAU_INSTRUCAO_RM` e `VW_MUNICIPIO_RM`, no próprio `DB_DRHFLOW_TESTE`, entregam os mesmos dados (ver `reconhecimento-banco.md`).

**Fora de escopo (permanecem no caminho legado MySQL)**

- Painel do coordenador e do gestor, incluindo autorização de vagas e gestão de candidaturas.
- Alertas de vagas por e-mail: são disparados pela autorização de vaga no painel do coordenador e filtram por área/modalidade/tipo. Continuam funcionando sobre as vagas MySQL, mas **não dispararão para vagas vindas do DRHFlow** até que a mudança do coordenador seja feita.
- Expansão do formulário do candidato para os demais campos de `EN_CANDIDATO_VAGA_EMPREGO`.
- Migração das vagas e candidaturas já existentes no MySQL — nenhum dado é apagado de nenhum dos dois lados.
