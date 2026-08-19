## MODIFIED Requirements

### Requirement: Dados próprios da candidatura

Os dados que variam a cada inscrição SHALL pertencer à candidatura e não ao perfil: carta de apresentação, a declaração de vínculo com a FAPEU com o respectivo detalhamento, e aceite do código de conduta.

A declaração de vínculo com a FAPEU SHALL ser composta de 5 perguntas específicas, respondidas em relação à vaga pretendida: se o candidato é servidor das instituições apoiadas pela FAPEU que atue na direção da Fundação; se é dirigente das instituições apoiadas pela FAPEU; se ocupa cargo de direção superior dessas instituições; se é coordenador de projeto administrado pela FAPEU; e se é fiscal de contrato entre a FAPEU e terceiros. Mais de uma dessas perguntas SHALL poder ser respondida afirmativamente na mesma candidatura.

O portal SHALL derivar um indicador agregado de conflito de interesse a partir dessas 5 respostas — verdadeiro quando qualquer uma for afirmativa. Quando o indicador agregado for verdadeiro, o detalhamento da relação SHALL ser exigido. O aceite do código de conduta SHALL ser registrado a cada inscrição.

#### Scenario: Conflito de interesse respondido por vaga

- **WHEN** um candidato que já respondeu não a todos os vínculos com a FAPEU em uma vaga se inscreve em outra
- **THEN** o portal solicita novamente as 5 perguntas, referidas à nova vaga

#### Scenario: Mais de um vínculo declarado

- **WHEN** o candidato responde afirmativamente a mais de um dos 5 vínculos com a FAPEU
- **THEN** o portal grava cada resposta de forma independente
- **AND** o indicador agregado de conflito de interesse passa a ser verdadeiro

#### Scenario: Aceite do código de conduta a cada inscrição

- **WHEN** o candidato envia uma inscrição
- **THEN** o aceite do código de conduta é exigido e registrado para aquela candidatura

#### Scenario: Conflito declarado sem detalhamento

- **WHEN** o candidato responde afirmativamente a qualquer um dos 5 vínculos e não descreve a relação
- **THEN** o portal recusa o envio e solicita o detalhamento

#### Scenario: Nenhum vínculo declarado

- **WHEN** o candidato responde não às 5 perguntas de vínculo com a FAPEU
- **THEN** o indicador agregado de conflito de interesse é falso
- **AND** o portal não exige detalhamento
