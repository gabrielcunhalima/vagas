# navegacao-publica Specification

## Purpose

Define o que aparece na navbar fixa exibida no topo de todas as páginas públicas do portal (visitante e candidato logado), sua ordem, e o que continua acessível fora dela depois da simplificação.

## Requirements

### Requirement: Composição da navbar de desktop
A navbar fixa de desktop SHALL exibir, da esquerda para a direita, apenas: (1) a logo da FAPEU, (2) o lockup "Portal de Vagas", (3) o alternador de tema claro/escuro e (4) as ações de conta. A navbar NÃO SHALL exibir botões de atalho separados para "Vagas" ou "Alertas".

#### Scenario: Visitante não autenticado vê Entrar e Criar conta
- **WHEN** um visitante sem sessão de candidato abre qualquer página pública em uma tela de desktop
- **THEN** a navbar exibe os botões "Entrar" e "Criar conta" como ações de conta, sem os atalhos "Vagas" ou "Alertas" ao lado da logo

#### Scenario: Candidato autenticado vê o menu de conta
- **WHEN** um candidato autenticado abre qualquer página pública em uma tela de desktop
- **THEN** a navbar exibe o menu de conta (nome/iniciais e opções de conta) no lugar de "Entrar"/"Criar conta", sem os atalhos "Vagas" ou "Alertas" ao lado da logo

### Requirement: Lockup "Portal de Vagas" com hierarquia correta
O lockup ao lado da logo SHALL exibir a categoria ("Portal de") e o nome do sistema ("Vagas") em uma única linha, lado a lado: categoria em peso médio e cor secundária (muted); nome do sistema maior, em negrito e na cor de texto principal (foreground). O lockup inteiro SHALL funcionar como link para a listagem pública de vagas.

#### Scenario: Nome do sistema usa a cor de texto principal
- **WHEN** a navbar é renderizada em qualquer tema (claro ou escuro)
- **THEN** a linha "Vagas" do lockup usa a cor de texto principal (foreground), nunca uma classe de cor que não resolve a um token válido

#### Scenario: Clicar no lockup leva à listagem de vagas
- **WHEN** o usuário clica na logo ou no texto "Portal de Vagas"
- **THEN** o portal navega para a listagem pública de vagas

### Requirement: Alertas e navegação completa continuam acessíveis fora da navbar
Remover os atalhos "Vagas" e "Alertas" da navbar de desktop NÃO SHALL reduzir o acesso a essas páginas: elas SHALL continuar alcançáveis pelo menu mobile (hambúrguer), pelo rodapé e pelos pontos de entrada já existentes nas próprias páginas (banner de alertas na listagem e na página de vaga).

#### Scenario: Menu mobile mantém a navegação completa
- **WHEN** um visitante ou candidato abre o menu mobile (ícone de hambúrguer) em uma tela estreita
- **THEN** o menu exibe os links "Vagas" e "Alertas" e as opções de conta, sem alteração em relação ao comportamento anterior

#### Scenario: Alertas continua acessível a partir da listagem de vagas
- **WHEN** um visitante está na listagem pública de vagas em uma tela de desktop
- **THEN** ele encontra um caminho para criar um alerta de vagas sem depender de um atalho na navbar
