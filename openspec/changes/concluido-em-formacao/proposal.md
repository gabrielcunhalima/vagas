## Why

No formulário de edição de perfil, o campo de data na seção "Formação" sempre exibe o rótulo "Previsão de conclusão", mesmo quando o candidato já marcou a situação do curso como "Concluído". Para quem já concluiu, "previsão" é um rótulo enganoso — sugere uma data futura estimada quando, na prática, o candidato deve informar a data em que o curso terminou.

## What Changes

- No formulário de edição de perfil (`Formação`), quando a situação do curso for "Concluído", o rótulo do campo de data passa de "Previsão de conclusão" para "Concluído em".
- Quando a situação do curso for "Cursando", o rótulo permanece "Previsão de conclusão".
- O campo continua representando o mesmo dado armazenado no perfil (`previsao_conclusao`); não há mudança de schema, validação de backend ou de nome do campo — apenas do rótulo exibido, condicionado à situação do curso já selecionada.

## Capabilities

### Modified Capabilities
- `perfil-candidato-unico`: o rótulo do campo de conclusão do curso, no formulário de edição de perfil, passa a refletir a situação do curso informada pelo candidato (cursando vs. concluído).

## Impact

- `resources/js/Pages/Candidato/Perfil/Edit.jsx`: rótulo do campo `previsao_conclusao` passa a ser condicional a `situacao_curso`.
- Nenhum impacto em backend, banco de dados ou demais telas (as telas de exibição de candidatura já tratam o valor apenas como data formatada, sem depender do rótulo do formulário).
