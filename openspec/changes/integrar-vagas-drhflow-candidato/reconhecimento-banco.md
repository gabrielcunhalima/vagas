# Reconhecimento do banco `DB_DRHFLOW_TESTE`

Levantamento executado contra `150.162.78.4:1433` / `DB_DRHFLOW_TESTE` em 2026-08-17 (tarefa 1.1).
Registra o que foi confirmado e o que diverge de `design.md` e do `DocVagaEmpregoFAPEU-1.pdf`.

## Confirmado como o design supunha

| Item | Resultado |
| --- | --- |
| `DE_DOCUMENTACAO_NECESSARIA` vs. `DE_DOCUMENTACAO_OBRIGATORIA` | **`DE_DOCUMENTACAO_NECESSARIA`** é o nome real (`text`). A consulta do RH estava certa; o item 17 da tela de cadastro no documento está errado. |
| `DE_ATIVIDADES` | Existe em `EN_VAGA_EMPREGO`, tipo `text`. |
| `CD_TIPO_ADMISSAO` | Existe em `EN_VAGA_EMPREGO`, `varchar(1)`. Valores em uso: `U` (240), `N` (78), `A` (25), `T` (17), nulo (167). |
| `NU_CPF` | `varchar(25)`, `NOT NULL`. Gravado com **11 dígitos, sem máscara**, com zeros à esquerda (ex.: `00001594923`). |
| Chave primária de `EN_CANDIDATO_VAGA_EMPREGO` | Composta: `NU_CPF` (1) + `CD_VAGA_EMPREGO` (2). Confirma D8. |
| `CD_SITUACAO` | `EN_SITUACAO_VAGA_EMPREGO`: `1` = Vaga em Aberto, `2` = Vaga Finalizada, `3` = Vaga Cancelada. |
| `CD_MUNICIPIO` da **vaga** | `int`, casa com `EN_MUNICIPIO.CD_MUNICIPIO` (Florianópolis = `1653`). |
| `CD_PAIS` | `int`, casa com `EN_PAIS_IBGE.CD_PAIS`. Brasil = `76`. |
| `CD_FUNCAO` | `varchar(10)`, casa com `VW_FUNCAO_5ANOS.CODIGO`. |
| `CD_PROJETO` | `varchar(25)`, casa com `VW_PROJETO.ano_codigo` (formato `2025.114`). |

## Divergências que afetam o desenho

### 1. `CD_MUNICIPIO_ENDERECO` não é `EN_MUNICIPIO` — D7 está incorreto

`EN_CANDIDATO_VAGA_EMPREGO.CD_MUNICIPIO_ENDERECO` é `varchar(15)` e usa o **código do RM**, não o de `EN_MUNICIPIO`:

| Município | `VW_MUNICIPIO_RM.CODMUNICIPIO` | `EN_MUNICIPIO.CD_MUNICIPIO` |
| --- | --- | --- |
| Florianópolis/SC | `05407` (2948 registros usam esse valor) | `1653` |
| Santo Antônio do Amparo/MG | — | `5407` |

Traduzir o endereço do candidato por `EN_MUNICIPIO`, como D7 manda, gravaria **o município errado** — `5407` em `EN_MUNICIPIO` é Santo Antônio do Amparo/MG.

A tradução correta é por **`VW_MUNICIPIO_RM`** (`CODMUNICIPIO`, `CODETDMUNICIPIO`, `NOMEMUNICIPIO`), preservando os zeros à esquerda. O `CODMUNICIPIO` **não é único sozinho**: `05407` existe em CE (Icó) e SC (Florianópolis). A busca precisa casar nome **e** UF.

`EN_MUNICIPIO` continua correto para a localização **da vaga** (`CD_MUNICIPIO`). São dois domínios diferentes na mesma base.

### 2. `EN_TIPO_ADMISSAO` não casa pela própria chave

A tabela de domínio tem chave `CD_TIPO_ADMISSAO` com valores `A`, `N`, `O`, `T` — mas `EN_VAGA_EMPREGO.CD_TIPO_ADMISSAO` usa `A`, `N`, `U`, `T`. A coluna que faz a ponte é **`CD_RM`**:

| `EN_TIPO_ADMISSAO.CD_TIPO_ADMISSAO` | `NM_TIPO_ADMISSAO` | `CD_RM` (= o valor gravado na vaga) |
| --- | --- | --- |
| `A` | AUTÔNOMO | `A` |
| `N` | CELETISTA | `N` |
| `O` | BOLSISTA | **`U`** |
| `T` | ESTAGIÁRIO | `T` |

O de-para `A`/`N`/`U`/`T` do design está certo nos rótulos; o que muda é que o join, se houver, é por `CD_RM`.

### 3. `CorporeRM` não é necessário para escolaridade

D1 e o Impact da proposal exigem que o usuário do banco tenha `SELECT` em `CorporeRM.dbo.PCODINSTRUCAO`. **Não é preciso**: o próprio `DB_DRHFLOW_TESTE` expõe `VW_GRAU_INSTRUCAO_RM` (e `VW_ESCOLARIDADE`) com exatamente os mesmos 17 registros.

Usar a view local elimina a dependência cross-database e simplifica a permissão pedida ao DBA (tarefa 1.5).

Códigos de grau de instrução (`CODINTERNO` → `DESCRICAO`):

```
1 Analfabeto                                    9 Educação superior completo
2 Até o 5º ano incompleto do ensino fundamental A Pós Grad. incompleto
3 5º ano completo do ensino fundamental         B Pós Grad. completo
4 Do 6º ao 9º ano do ensino fundamental         C Mestrado incompleto
5 Ensino fundamental completo                   D Mestrado completo
6 Ensino médio incompleto                       E Doutorado incompleto
7 Ensino médio completo                         F Doutorado completo
8 Educação superior incompleto                  G Pós Dout.incompleto
                                                H Pós Dout.completo
```

O mesmo domínio serve `EN_VAGA_EMPREGO.CD_ESCOLARIDADE_EXIGIDA` (`varchar(10)`, valores em uso: 5, 7, 8, 9, B, C, D, E, F, G) e `EN_CANDIDATO_VAGA_EMPREGO.CD_GRAU_INSTRUCAO` (`varchar(5)`, valores em uso: 2–9, A–H).

## Divergências menores

| Item | Achado |
| --- | --- |
| Flags `FG_*` | São `varchar(1)` com **`S`/`N`**, não booleano nem 1/0. Confirmado em `FG_PCD` (140 `S`, 4329 `N`) e `FG_LEU_CODIGO_CONDUTA_FAPEU` (4349 `S`, 120 `N`). |
| `ID_USUARIO_CAD` | `varchar(25)`, **vazio nas 4469 linhas existentes** — o DRHFlow nunca preencheu. Não há valor pré-existente a imitar; a escolha do identificador do portal é livre (questão aberta do design). |
| `CD_TIPO_CONTRATACAO` | Coluna `int` existe em `EN_VAGA_EMPREGO` mas está **100% nula** (527 linhas). Coluna morta — `CD_TIPO_ADMISSAO` é a viva. Não confundir as duas. |
| `VL_SALARIO` | `decimal`. **117 vagas têm valor `0`** — deve ser tratado como "não informado", não como salário zero. Uma das 3 vagas abertas hoje está assim. |
| `CD_HORARIO` | `varchar(25)`, casa com `VW_HORARIO_REQUISICAO.CODIGO` → `CARGA_SEMANAL` + `DESCRICAO` (ex.: `0021` → `20:00`, `DAS 08:00 AS 12:00`). Fonte melhor que `DE_CARGA_HORARIA`, que é só a carga (`20:00`). |
| `EN_MUNICIPIO.CD_UF` | `varchar(3)`, enquanto `EN_UF.CD_UF` é `varchar(2)`. Comparações entre as duas precisam de atenção. |
| `FG_SEL` | Populado: 39 × `1`, 73 × `0`, 4357 nulo. Existe e é usado, mas nenhuma fonte documenta a semântica. **Mantido fora do uso**, como D10 decidiu. |
| `FG_DEFICIENTE_AUTISMO` | Coluna não listada no documento. Fica nula, junto com os demais `FG_DEFICIENTE_*`. |
| `EN_CANDIDATO_VAGA_EMPREGO_DELETADO` | Tabela existe — o próprio DRHFlow arquiva exclusões em vez de apagar. Reforça D11. |
| `EN_UPLOAD_CURRICULO` | Tabela não mencionada no design: `NU_CPF`, `NM_ARQUIVO`, `NM_ENDERECO_ARQUIVO`, `DT_CADASTRO`. É onde o DRHFlow registra o currículo por CPF. Fora do escopo desta mudança, mas confirma o layout `/home/Curriculos/{cpf}/` de D9 e é candidata natural a uma mudança futura. |

## Volume e permissão

- **Apenas 3 vagas** atendem hoje ao critério `CD_SITUACAO = 1 AND DT_LIMITE_PARA_INSCRICAO >= GETDATE()-1`.
- A diferença entre a contagem bruta e a filtrada pelo `inner join` com `VW_FUNCAO_5ANOS` é **zero** hoje. O log de D3 continua justificado, mas não há vaga escondida no momento.
- `EN_CANDIDATO_VAGA_EMPREGO` tem 4469 linhas.
- **A credencial em uso é `sa`, membro de `sysadmin`.** Não há nenhuma barreira de banco hoje: o portal poderia apagar qualquer coisa. A tarefa 1.5 (usuário restrito) é a única proteção real e permanece obrigatória antes do deploy.
