## 1. Painel de detalhe — coluna fixa

- [x] 1.1 Em `resources/js/Pages/Publico/Vagas/Index.jsx`, remover `max-h-[calc(100dvh-6rem)]` e `overflow-y-auto` da `className` do `VagaDetalhePainel` na coluna fixa (`xl:`), mantendo `xl:sticky xl:top-20 xl:block` e o restante das classes.
- [x] 1.2 Atualizar o comentário de topo de `resources/js/Components/VagaDetalhePainel.jsx` para refletir que o cabeçalho `sticky` agora gruda em relação à rolagem da página (não mais a uma rolagem interna do painel).

## 2. Verificação manual

- [x] 2.1 Abrir a listagem pública em tela larga (`xl:` e acima), selecionar uma vaga com descrição/requisitos longos e confirmar que todo o conteúdo aparece sem barra de rolagem própria no painel.
- [x] 2.2 Confirmar que, com o painel em vista, o cabeçalho (badges, título, botão "Candidatar-se") permanece visível ao rolar a página.
- [x] 2.3 Confirmar que a lista de vagas ao lado continua rolando de forma independente do painel de detalhe.
- [x] 2.4 Confirmar que o comportamento em telas estreitas (Sheet) não mudou — continua com sua própria rolagem interna.

## 3. Specs

- [x] 3.1 Rodar `openspec validate detalhe-vaga-sem-rolagem-interna --strict` e corrigir eventuais problemas antes de arquivar.
