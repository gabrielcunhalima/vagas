## Why

No formulário de criação de conta, o link "Política de Privacidade" navega para uma página separada (`/politica-privacidade`), fazendo o candidato perder o preenchimento em andamento (CPF, e-mail, senha) para poder ler os termos antes de marcar o consentimento. Exibir o conteúdo em um pop-up mantém o candidato no formulário e reduz o atrito no cadastro.

## What Changes

- O link "Política de Privacidade" no formulário de criação de conta passa a abrir um pop-up (modal) sobre a própria tela de cadastro, em vez de navegar para a página `/politica-privacidade`.
- O pop-up exibe o mesmo conteúdo da Política de Privacidade já publicado na página dedicada.
- O candidato pode fechar o pop-up (botão de fechar, tecla Esc ou clique fora) e retomar o preenchimento do formulário exatamente como estava, sem perda de dados digitados.
- A página pública `/politica-privacidade` continua existindo e acessível normalmente (rodapé, menu mobile e demais pontos de entrada não são alterados) — a mudança é restrita ao link dentro do formulário de criação de conta.

## Capabilities

### Modified Capabilities
- `cadastro-candidato-conta`: o acesso à Política de Privacidade a partir do formulário de criação de conta passa a ocorrer via pop-up sobreposto, preservando o estado do formulário, em vez de navegação para outra página.

## Impact

- **Frontend**: [resources/js/Pages/Candidato/Auth/Registro.jsx](resources/js/Pages/Candidato/Auth/Registro.jsx) — troca do `Link` de navegação por um gatilho de pop-up (componente `Dialog` já disponível em [resources/js/components/ui/dialog.jsx](resources/js/components/ui/dialog.jsx)).
- **Reuso de conteúdo**: o texto da política em [resources/js/Pages/Publico/PoliticaPrivacidade.jsx](resources/js/Pages/Publico/PoliticaPrivacidade.jsx) precisa ficar acessível tanto pela página quanto pelo pop-up, sem duplicar o texto.
- Nenhuma alteração de backend, rotas ou banco de dados é necessária.
