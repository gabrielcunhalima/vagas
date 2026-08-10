## REMOVED Requirements

### Requirement: Adoção de candidaturas anteriores sem vínculo

**Reason**: A adoção retroativa dependia de comparar `cpf` e `email` gravados diretamente em `candidaturas` com os dados da conta nova. Esta change remove essas colunas de `candidaturas` — elas eram cópias do perfil do candidato, e toda leitura da aplicação já as ignora em favor do relacionamento com `candidato`. Sem as colunas, não sobra o que casar: a capacidade de reivindicar uma candidatura antiga deixa de fazer sentido, não só de funcionar.

**Migration**: Toda candidatura hoje exige conta autenticada no envio (`candidatura-vinculada-a-conta` — "Candidatura exige conta autenticada com e-mail verificado"), então nenhuma candidatura nova nasce sem `candidato_id`. As candidaturas antigas que ainda estivessem sem vínculo no momento deste deploy são removidas pela migration de limpeza que precede o corte de colunas — não há caminho de adoção alternativo a oferecer.

#### Scenario: Candidatura antiga sem conta associada

- **WHEN** existir uma candidatura cujo `candidato_id` nunca foi preenchido
- **THEN** ela é removida antes de as colunas de identidade saírem de `candidaturas`, e nenhum cadastro futuro pode mais reivindicá-la
