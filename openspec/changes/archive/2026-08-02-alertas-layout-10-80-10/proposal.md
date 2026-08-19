## Why

O candidato chega em `/alertas` vindo do card da hero da listagem, que é larga (10-80-10). A página de destino colapsa para uma coluna estreita (`max-w-2xl`) e empilha tudo num único card: e-mail, treze áreas de interesse, três tipos de vaga, três modalidades, consentimento LGPD e botão. O resultado é um formulário alto, que exige rolagem para chegar ao botão de envio, e uma quebra visual entre a página de origem e a de destino.

O espaço horizontal para resolver isso já existe — a listagem já provou que o portal comporta a largura 10-80-10. O formulário de alerta não usa nada dele.

## What Changes

- **Página de alertas passa para a largura 10-80-10**, a mesma da listagem pública: 80% da janela em telas largas, com 10% livre de cada lado. Cabeçalho e rodapé continuam na largura máxima comum do portal.
- **O formulário se separa em duas áreas** em telas largas, em vez de uma pilha única:
  - **Área de identificação e envio** (esquerda, estreita, acompanha a rolagem): título da página, campo de e-mail, consentimento LGPD e botão "Ativar alertas".
  - **Área de preferências** (direita, larga): os três grupos de seleção — áreas de interesse, tipos de vaga e modalidades — com mais opções por linha do que hoje.
- **Botão de envio deixa de exigir rolagem** em telas largas: fica na área que acompanha a rolagem, visível enquanto o candidato percorre as preferências.
- **Mesmos campos, mesmos dados**: nenhum campo é criado, removido, renomeado ou tem sua obrigatoriedade alterada. Os mesmos textos, rótulos e a mesma validação. O envio continua um único POST para `alertas.store`.
- **Em telas estreitas o comportamento é o de hoje**: as duas áreas empilham na ordem identificação → preferências → consentimento → botão, em coluna única e largura total.

Não é escopo: alterar o fluxo de criação/cancelamento de alerta, as regras de validação, o e-mail enviado, a página de alerta cancelado, ou a largura de qualquer outra página pública.

## Capabilities

### New Capabilities

- `alertas-vagas`: a página onde o candidato assina alertas de vagas — sua largura de conteúdo, a separação entre identificação/envio e preferências, e o comportamento dessa separação conforme a largura da tela.

### Modified Capabilities

- `vagas-listagem-publica`: o requisito "Largura de conteúdo 10-80-10 na listagem" hoje declara que essa largura vale **apenas** para a listagem e que as demais páginas públicas ficam inalteradas. Passa a admitir a página de alertas como segunda página nessa largura, mantendo a exclusividade sobre as demais páginas públicas e sobre cabeçalho e rodapé.

## Impact

- **Código alterado**: [Alertas.jsx](resources/js/Pages/Publico/Alertas.jsx#L58-L133) — container da página, grid de duas áreas, distribuição dos grupos de checkbox e posição do bloco de consentimento/botão.
- **Back-end**: inalterado — [AlertaVagaController](app/Http/Controllers/Vagas/AlertaVagaController.php) segue enviando as mesmas props (`areas`, `tipos`, `modalidades`, `email`) e recebendo o mesmo payload.
- **Rotas**: inalteradas — `alertas.create` e `alertas.store` continuam como estão.
- **Documentação**: [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md#L152) — a linha da listagem afirma que ela é a "única página em 10-80-10"; precisa passar a citar a página de alertas e ganhar uma entrada própria para o padrão do formulário.
- **Restrições de design herdadas**: sem hover-lift (regra 8), seleção marcada por preenchimento e não por filete lateral (regra 10), ícones apenas `lucide-react`, tokens de cor do tema.
