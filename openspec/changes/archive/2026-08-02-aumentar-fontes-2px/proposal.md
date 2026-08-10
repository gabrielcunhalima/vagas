## Why

A tipografia atual do portal parte de `text-xs` (12px) e `text-sm` (14px) como tamanhos dominantes — juntos são 248 das 302 ocorrências de classes de tamanho no código. Isso deixa a maior parte do conteúdo (descrições de vagas, tabelas de candidaturas, labels de formulário, badges) abaixo do confortável para leitura prolongada, principalmente para o público de candidatos e para gestores que revisam listas densas. Aumentar toda a escala em 2px melhora a legibilidade sem redesenhar nada.

## What Changes

- Toda a escala tipográfica do Tailwind (`text-xs` … `text-9xl`) passa a ser 2px maior que o padrão, via override das variáveis `--text-*` no tema. Isso reajusta de uma vez toda a interface que usa degraus nomeados, sem tocar em nenhum componente.
- As alturas de linha continuam sendo as razões padrão do Tailwind (valores sem unidade), então o entrelinhamento cresce proporcionalmente junto com a fonte.
- Os 15 tamanhos de fonte declarados fora da escala (valores arbitrários como `text-[0.65rem]`) recebem o mesmo acréscimo absoluto de 2px, para que não fiquem para trás. Isso inclui o lockup de identidade do cabeçalho, que é padrão formalizado no `DESIGN_SYSTEM.md` — o documento é atualizado junto.
- Os templates de e-mail transacional, que têm `font-size` fixo em CSS inline (fora do alcance do Tailwind), recebem o mesmo acréscimo de 2px em cada declaração.
- As alturas fixas dos controles de formulário sobem para comportar a linha de texto maior: a família de 32px (botão, campo, seletor, grupo de entrada, busca, abas) vai para 36px, e o badge de 20px vai para 26px. A escada de tamanhos de cada controle sobe junto, para nenhum tamanho nomeado empatar com outro.
- Ajustes adicionais de layout onde o texto maior force truncamento novo — corrigidos caso a caso após verificação visual, sem reverter o aumento.

Não é uma mudança quebrante: nenhuma API, rota ou contrato de dados muda. O risco é puramente visual.

## Capabilities

### New Capabilities
- `tipografia-base`: define a escala tipográfica do portal — quais tamanhos existem, qual a relação entre eles, como o entrelinhamento acompanha, e que a escala é a única fonte de verdade para tamanho de texto em toda a interface.

### Modified Capabilities

Nenhuma. `vagas-listagem-publica` descreve comportamento de listagem e filtros; o tamanho da fonte não altera nenhum requisito dela.

## Impact

**Código afetado:**
- [resources/css/app.css](resources/css/app.css) — novo bloco `@theme` com os 13 tamanhos redefinidos. É a mudança central; sozinha ela cobre a maior parte das 72 telas/componentes React.
- [resources/views/emails/vagas/layout.blade.php](resources/views/emails/vagas/layout.blade.php) — 8 declarações de `font-size`; mais 5 declarações inline em [alerta-nova-vaga.blade.php](resources/views/emails/vagas/alerta-nova-vaga.blade.php), [vaga-recusada.blade.php](resources/views/emails/vagas/vaga-recusada.blade.php), [redefinir-senha-candidato.blade.php](resources/views/emails/vagas/redefinir-senha-candidato.blade.php) e [verificar-email-candidato.blade.php](resources/views/emails/vagas/verificar-email-candidato.blade.php). 13 no total.
- 15 tamanhos arbitrários em 10 arquivos de [resources/js/](resources/js/) — ver design.md, Decisão 7, para a lista completa.
- Alturas fixas em [resources/js/Components/ui/](resources/js/Components/ui/): `badge`, `button`, `input`, `select`, `input-group`, `command`, `tabs`.
- [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) — os valores do lockup de cabeçalho documentados ali mudam junto com o código.
- Outras páginas em [resources/js/Pages/](resources/js/Pages/) — apenas se a verificação visual apontar quebra concreta.

**Fora de escopo / sem impacto:**
- Não há CSS solto em `public/`; todo o estilo passa pelo Vite.
- Nenhum arquivo define tamanho de fonte por `style={{ fontSize }}` no React.
- Nada de espaçamento, raio de borda ou cor é alterado. As únicas dimensões que mudam são as alturas de controle listadas acima, e mudam por necessidade de caber o texto.

**Efeito colateral em outros sistemas FAPEU:** o lockup de cabeçalho é padrão declarado para replicação. Ao alterá-lo aqui, o portal de vagas passa a divergir dos demais sistemas até que o novo valor seja replicado neles. Decisão tomada conscientemente.

**Dependências:** nenhuma nova. Tailwind v4 já suporta override de `--text-*` via `@theme`.

**Verificação:** exige passada visual nas telas de maior densidade (listagem de vagas, tabelas de candidaturas, sidebar, formulário de vaga) em desktop e mobile, claro e escuro — não há teste automatizado que capture regressão tipográfica.
