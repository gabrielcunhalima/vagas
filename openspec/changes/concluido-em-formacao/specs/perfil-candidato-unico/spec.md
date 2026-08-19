## ADDED Requirements

### Requirement: Rótulo do campo de conclusão reflete a situação do curso

No formulário de edição de perfil, o rótulo do campo de data de conclusão do curso SHALL depender da situação do curso informada pelo candidato. Quando a situação do curso for "concluído", o rótulo SHALL ser "Concluído em". Quando a situação do curso for "cursando" ou ainda não estiver selecionada, o rótulo SHALL ser "Previsão de conclusão".

Essa mudança afeta apenas o rótulo exibido; o campo continua representando o mesmo dado de data do perfil, sem alteração de nome, validação ou armazenamento.

#### Scenario: Candidato marca o curso como concluído

- **WHEN** o candidato seleciona "Concluído" no campo situação do curso
- **THEN** o campo de data passa a ser rotulado "Concluído em"

#### Scenario: Candidato marca o curso como em andamento

- **WHEN** o candidato seleciona "Cursando" no campo situação do curso
- **THEN** o campo de data permanece rotulado "Previsão de conclusão"

#### Scenario: Situação do curso ainda não selecionada

- **WHEN** o candidato ainda não selecionou a situação do curso
- **THEN** o campo de data é rotulado "Previsão de conclusão" por padrão
