## MODIFIED Requirements

### Requirement: Largura de conteúdo 10-80-10 na listagem

A listagem pública de vagas SHALL ocupar 80% da largura da janela, com 10% de margem livre de cada lado, em telas largas. Essa largura SHALL valer para a listagem e para a página de alertas de vagas — as demais páginas públicas e os elementos comuns de navegação (cabeçalho e rodapé) mantêm a largura máxima já em uso no portal.

#### Scenario: Tela larga

- **WHEN** o candidato abre a listagem pública em uma janela larga
- **THEN** o conteúdo da listagem ocupa 80% da largura da janela, centralizado, com 10% de espaço livre à esquerda e 10% à direita

#### Scenario: Cabeçalho e rodapé inalterados

- **WHEN** o candidato abre a listagem pública em uma janela larga
- **THEN** o cabeçalho e o rodapé do portal permanecem na mesma largura máxima que apresentam nas demais páginas públicas, sem acompanhar a largura da listagem

#### Scenario: Outras páginas públicas inalteradas

- **WHEN** o candidato navega para qualquer outra página pública do portal que não seja a listagem ou a página de alertas de vagas
- **THEN** a largura de conteúdo dessa página permanece exatamente como antes desta mudança
