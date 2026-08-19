## Why

O perfil do candidato hoje só guarda uma formação (nível, situação, curso, instituição, semestre e previsão de conclusão) em colunas únicas na tabela `candidatos`. Na prática muitos candidatos têm mais de uma formação (ex.: técnico + graduação, ou duas graduações) e qualificações complementares — pós-graduação, mestrado, doutorado, cursos e palestras — que hoje não têm onde entrar. Coordenadores avaliando candidaturas ficam sem essa informação, que fica perdida em anexos ou não é informada.

## What Changes

- O perfil passa a suportar **múltiplas formações**, cada uma com nível de escolaridade, situação do curso, curso, instituição, semestre (se cursando) e previsão/data de conclusão — mesmo conjunto de campos de hoje, agora repetível. O candidato pode adicionar e remover formações livremente, com pelo menos uma exigida para o perfil ficar completo.
- Novo campo de texto livre **"Outras Formações Superiores reconhecidas pelo MEC"** (ex.: especialização, mestrado, doutorado — nome e ano), opcional.
- Novo campo de texto livre **"Outros Cursos, Palestras, Etc."** (nome e data), opcional.
- Esses dados passam a aparecer em todos os lugares que já leem formação do perfil: conferência antes de enviar candidatura, visão do candidato sobre sua própria candidatura, visão do coordenador, e exportação de dados (LGPD).
- **BREAKING**: a formação deixa de ser um conjunto de colunas únicas em `candidatos` (`curso`, `instituicao`, `nivel_escolaridade`, `situacao_curso`, `semestre`, `previsao_conclusao`) e passa a viver em uma tabela relacionada (1:N). A formação única já cadastrada por candidatos existentes é migrada como a primeira entrada da lista.

## Capabilities

### New Capabilities

(nenhuma — a formação acadêmica já é parte do perfil único do candidato, coberto pela capability existente)

### Modified Capabilities

- `perfil-candidato-unico`: a definição de "perfil completo" passa a exigir pelo menos uma formação completa (em vez de um único conjunto fixo de campos de formação), e o perfil passa a incluir também os dois campos de texto livre sobre outras formações/cursos. As regras de fonte única, edição a partir de qualquer contexto e exclusão/anonimização passam a se aplicar também à lista de formações.

## Impact

- **Banco de dados**: nova tabela `candidato_formacoes` (FK `candidato_id`); migração de dados das colunas atuais de `candidatos` para a nova tabela; remoção das colunas antigas de formação de `candidatos`; duas novas colunas de texto em `candidatos` (outras formações MEC, outros cursos/palestras).
- **Backend**: `App\Models\Candidato` (relação `formacoes()`, `CAMPOS_OBRIGATORIOS`, `pendencias()`, `estadoCompletude()`), novo model `CandidatoFormacao`, `App\Http\Controllers\Candidato\PerfilController` (edit/update/exportarDados), `App\Services\AnonimizacaoService`, `App\Http\Controllers\Vagas\InscricaoController` (`perfilParaConferencia`), `App\Models\Vagas\Candidatura` (`CAMPOS_DO_PERFIL`/`getAttribute`, `scopeBusca`).
- **Frontend**: `resources/js/Pages/Candidato/Perfil/Edit.jsx` (lista repetível de formações + dois novos campos), `resources/js/Pages/Publico/Candidatura.jsx`, `resources/js/Pages/Coord/Candidaturas/Show.jsx`, `resources/js/Pages/Candidato/Candidaturas/Show.jsx`.
- **Seeders/factories/testes**: `database/seeders/CandidatoSeeder.php`, `database/seeders/CenariosTesteSeeder.php`, `database/factories/CandidatoFactory.php`, `tests/Feature/CandidatoRegistroCpfTest.php`.
