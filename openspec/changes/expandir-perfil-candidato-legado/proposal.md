## Why

O sistema legado de contratação da FAPEU ("Candidato Vaga", telas em anexo) coleta hoje, na inscrição de um candidato, um conjunto de dados que alimenta a tabela de pessoa física usada em produção para formalizar contratações. O portal `www/vagas` precisa passar a coletar esse mesmo conjunto de dados no perfil do candidato, para que a informação já exista quando alguém for efetivamente contratado — hoje uma parte relevante desses dados simplesmente não é perguntada.

Comparando as 5 abas do formulário legado (Identificação, Endereço, Formação, Questionário, Documentos Complementares) com o que o portal coleta hoje:

- **Identificação**: CPF, nome, nome social e e-mail já são coletados. **Telefone** hoje é um único campo genérico, mas o legado pede **Telefone Fixo** e **Telefone Celular** separadamente. As **deficiências** hoje são um único booleano (PcD) mais um texto livre; o legado pede 8 categorias específicas em checkboxes independentes (Físico, Auditivo, Fala, Visual, Mental, Intelectual, Reabilitado conforme Resolução INSS/PRES Nº118 de 04.11.2010, Autismo), que podem ser combinadas.
- **Endereço**: CEP, UF, município, logradouro, complemento, número e bairro já são coletados. Faltam **Tipo de Endereço** e **Tipo de Bairro**.
- **Formação**: Curso Superior 1/2/3, "Outras Formações Superiores reconhecidas pelo MEC" e "Outros Cursos, Palestras, Etc." já estão cobertos pela change pendente `add-multiplas-formacoes-candidatura` (formação como lista + dois campos de texto livre) — **fora do escopo desta change**, para não duplicar trabalho já proposto.
- **Questionário**: o aceite do código de conduta já é coletado a cada candidatura. A pergunta de conflito de interesse hoje é uma única pergunta genérica ("tem vínculo com alguém da equipe desta vaga?"); o legado pergunta 5 vínculos específicos com a FAPEU (servidor na direção, dirigente, cargo de direção superior, coordenador de projeto administrado pela FAPEU, fiscal de contrato).
- **Documentos Complementares**: upload de currículo já é coletado (com versionamento, inclusive).

Esta change cobre exatamente as lacunas acima: telefone fixo/celular, deficiências granulares, tipo de endereço/bairro e os 5 vínculos de conflito de interesse.

## What Changes

- **Telefone deixa de ser um único campo**: o perfil passa a ter `telefone_celular` (mesmo papel do campo `telefone` atual, inclusive como exigência de perfil completo) e um novo `telefone_fixo`, opcional. **BREAKING**: a coluna `telefone` é renomeada para `telefone_celular` — todo código, seed e teste que lê/grava `telefone` precisa acompanhar o rename.
- **Deficiência deixa de ser um único texto livre**: o perfil passa a ter 8 marcações independentes — física, auditiva, de fala, visual, mental, intelectual, reabilitado conforme Resolução INSS/PRES Nº118/2010, e autismo. O campo agregado `pcd` passa a ser calculado a partir dessas marcações (verdadeiro se qualquer uma estiver marcada) em vez de editável diretamente. O campo de texto livre `pcd_tipo` é removido, substituído pelas marcações específicas.
- **Endereço ganha dois campos novos**, opcionais: tipo de endereço (ex.: Rua, Avenida, Rodovia) e tipo de bairro (ex.: Bairro, Distrito, Zona Rural). Como não há visibilidade sobre uma lista fixa usada em produção, ambos são campos de texto livre — não listas fechadas — para não impor uma categorização que pode não bater com a da produção.
- **Conflito de interesse deixa de ser uma única pergunta**: a candidatura passa a perguntar, uma a uma, se o candidato tem os 5 vínculos específicos com a FAPEU (servidor na direção, dirigente, cargo de direção superior, coordenador de projeto administrado pela FAPEU, fiscal de contrato entre a FAPEU e terceiros). O campo agregado `conflito_interesse` passa a ser calculado (verdadeiro se qualquer vínculo estiver marcado) e continua exigindo o detalhamento (`conflito_interesse_detalhe`) quando algum vínculo for declarado.

## Capabilities

### Modified Capabilities

- `perfil-candidato-unico`: o perfil passa a guardar telefone fixo e celular separadamente (a definição de perfil completo passa a exigir telefone celular em vez de telefone), deficiência como um conjunto de marcações específicas em vez de um texto livre, e tipo de endereço/tipo de bairro como dados opcionais do endereço.
- `candidatura-vinculada-a-conta`: a declaração de conflito de interesse por candidatura deixa de ser uma única pergunta Sim/Não e passa a ser 5 perguntas específicas sobre vínculos com a FAPEU, mantendo a exigência de detalhamento quando qualquer uma for afirmativa.

## Impact

- **Banco de dados**: em `candidatos` — novas colunas `telefone_fixo`, `deficiencia_fisica`, `deficiencia_auditiva`, `deficiencia_fala`, `deficiencia_visual`, `deficiencia_mental`, `deficiencia_intelectual`, `reabilitado_inss`, `deficiencia_autismo`, `tipo_logradouro`, `tipo_bairro`; remoção de `pcd_tipo`. Em `candidaturas` — novas colunas `vinculo_servidor_direcao`, `vinculo_dirigente`, `vinculo_cargo_direcao_superior`, `vinculo_coordenador_projeto`, `vinculo_fiscal_contrato`.
- **Backend**: `App\Models\Candidato` (`$fillable`, `CAMPOS_OBRIGATORIOS`, casts), `App\Models\Vagas\Candidatura` (`$fillable`, `CAMPOS_DO_PERFIL`, casts), `App\Http\Controllers\Candidato\PerfilController` (edit/update/exportarDados), `App\Http\Requests\Vagas\InscricaoRequest`, `App\Http\Controllers\Vagas\InscricaoController` (`perfilParaConferencia`), `App\Http\Controllers\Vagas\CandidaturaController`, `App\Http\Controllers\Candidato\MinhaCandidaturaController`, `App\Services\AnonimizacaoService`.
- **Frontend**: `resources/js/Pages/Candidato/Perfil/Edit.jsx` (telefone fixo/celular, checkboxes de deficiência, tipo de endereço/bairro), `resources/js/Pages/Publico/Candidatura.jsx` (checkboxes de vínculo FAPEU), `resources/js/Pages/Coord/Candidaturas/Show.jsx` e `resources/js/Pages/Candidato/Candidaturas/Show.jsx` (exibição dos vínculos declarados).
- **Seeders/factories/testes**: `database/seeders/CandidatoSeeder.php`, `database/seeders/CenariosTesteSeeder.php`, `database/factories/CandidatoFactory.php`, `tests/Unit/PerfilCompletudeTest.php`, `tests/Unit/CandidaturaModelTest.php`.
- **Fora de escopo**: formação acadêmica (curso superior 1/2/3, outras formações MEC, outros cursos/palestras) — coberta pela change pendente `add-multiplas-formacoes-candidatura`; upload de currículo e aceite do código de conduta — já atendidos hoje sem alteração; qualquer integração direta com a tabela de produção em SQL Server (`FAP_SOLICITACAO_CONTRATACAO`) — esta change só amplia a coleta no portal, não implementa exportação/sincronização para aquela base.