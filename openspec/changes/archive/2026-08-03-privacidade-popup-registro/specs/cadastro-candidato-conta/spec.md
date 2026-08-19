## ADDED Requirements

### Requirement: Consulta à Política de Privacidade sem sair do cadastro

Ao acionar o link "Política de Privacidade" exibido junto ao consentimento LGPD no formulário de criação de conta, o portal SHALL exibir o conteúdo da política em um pop-up sobreposto à tela atual, em vez de navegar para outra página. O pop-up SHALL poder ser fechado sem submeter nem descartar o formulário, e os valores já preenchidos (CPF, e-mail, senha e confirmação) SHALL permanecer inalterados após o fechamento.

#### Scenario: Abrir a Política de Privacidade a partir do cadastro

- **WHEN** o candidato aciona o link "Política de Privacidade" no formulário de criação de conta
- **THEN** o portal exibe um pop-up sobreposto à tela com o conteúdo da Política de Privacidade
- **AND** o candidato permanece na mesma tela de criação de conta, sem navegação de página

#### Scenario: Fechar o pop-up preserva o formulário

- **WHEN** o candidato fecha o pop-up da Política de Privacidade (pelo botão de fechar, pela tecla Esc ou clicando fora do pop-up)
- **THEN** o portal retorna o foco ao formulário de criação de conta
- **AND** todos os campos previamente preenchidos mantêm os valores digitados antes da abertura do pop-up

#### Scenario: Conteúdo do pop-up consistente com a página dedicada

- **WHEN** o pop-up da Política de Privacidade é exibido
- **THEN** o texto apresentado é o mesmo conteúdo vigente na página pública dedicada à Política de Privacidade
