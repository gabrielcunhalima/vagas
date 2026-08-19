## ADDED Requirements

### Requirement: CTA de alerta na área direita da hero

Em telas largas, a hero da listagem pública SHALL apresentar duas áreas lado a lado: à esquerda o título, a contagem de vagas e a busca; à direita o convite para criar alerta de vagas. O convite SHALL ocupar a área direita da hero — a mesma faixa hoje vazia sobre a foto — e SHALL não empurrar a busca para cima, para baixo ou para fora da largura de conteúdo.

A largura de conteúdo da hero SHALL permanecer a mesma já exigida para a listagem (10-80-10), sem alteração.

#### Scenario: Hero em tela larga

- **WHEN** o candidato abre a listagem pública em uma janela larga
- **THEN** o título, a contagem de vagas e a busca aparecem na área esquerda da hero e o convite de alerta aparece na área direita, na mesma faixa vertical, sem sobreposição entre eles

#### Scenario: Largura da hero inalterada

- **WHEN** o candidato abre a listagem pública em uma janela larga
- **THEN** a hero ocupa a mesma largura de conteúdo da listagem abaixo dela, exatamente como antes desta mudança

#### Scenario: Busca preservada

- **WHEN** o candidato usa o campo de busca da hero em tela larga
- **THEN** o campo e o botão de buscar continuam com o mesmo comportamento e alcance de clique, sem que o convite de alerta os cubra ou reduza

### Requirement: Hero mais baixa que a lista de vagas

Com o convite de alerta fora da pilha vertical, a hero SHALL ocupar menos altura do que ocupava antes desta mudança, aproximando o início da lista de vagas do topo da página. A redução SHALL valer tanto em telas largas quanto estreitas e SHALL não cortar, truncar nem sobrepor nenhum conteúdo da hero.

#### Scenario: Lista mais próxima do topo

- **WHEN** o candidato abre a listagem pública em uma janela larga
- **THEN** a hero ocupa menos altura vertical do que ocupava antes desta mudança e o início da lista de vagas fica mais próximo do topo da página

#### Scenario: Nada é cortado

- **WHEN** a hero é exibida com a altura reduzida, em qualquer largura de tela
- **THEN** título, contagem de vagas, busca e convite de alerta permanecem inteiramente legíveis, sem texto cortado, truncado ou sobreposto

### Requirement: CTA de alerta empilhado em telas estreitas

Em telas onde as duas áreas da hero não cabem lado a lado, o convite de alerta SHALL aparecer empilhado abaixo da busca, com o mesmo comportamento que já tinha antes desta mudança, e SHALL permanecer inteiramente visível sem rolagem horizontal.

#### Scenario: Hero em tela estreita

- **WHEN** o candidato abre a listagem pública em uma tela estreita
- **THEN** o convite de alerta aparece abaixo da busca, ocupando a largura disponível, sem rolagem horizontal

#### Scenario: Transição entre larguras

- **WHEN** a janela é redimensionada entre uma largura larga e uma estreita
- **THEN** o convite de alerta alterna entre a área direita e a posição empilhada sem perder conteúdo, sem sobrepor a busca e sem deixar espaço vazio no lugar dele

### Requirement: Conteúdo e destino do convite de alerta preservados

O convite de alerta SHALL manter, em qualquer posição ou largura de tela, o mesmo conteúdo de antes desta mudança: o título "Não perca nenhuma vaga", a explicação de que as vagas compatíveis com o perfil chegam por e-mail assim que publicadas, e uma ação única que leva à criação de alerta de vagas.

#### Scenario: Ação leva à criação de alerta

- **WHEN** o candidato aciona o botão do convite de alerta, em qualquer largura de tela
- **THEN** ele é levado à página de criação de alerta de vagas, o mesmo destino de antes desta mudança

#### Scenario: Texto preservado

- **WHEN** o convite de alerta é exibido na área direita da hero
- **THEN** o título e a explicação exibidos são os mesmos que apareciam quando o convite ficava abaixo da busca
