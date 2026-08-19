## 1. Escala tipográfica

- [x] 1.1 Em [resources/css/app.css](resources/css/app.css), após o bloco `@theme inline` das cores, adicionar um bloco `@theme` novo, com comentário de seção, redefinindo os 13 degraus com os valores da tabela de design.md — Decisão 2 (`--text-xs: 0.875rem` … `--text-9xl: 8.125rem`)
- [x] 1.2 Confirmar que nenhum `--text-*--line-height` foi redefinido no bloco novo (as razões do Tailwind devem ser herdadas — design.md, Decisão 3)
- [x] 1.3 Rodar `npm run build` e confirmar que compila sem erro
- [x] 1.4 Inspecionar o CSS gerado e confirmar que `.text-sm` saiu com `font-size: 1rem` e que sua `line-height` continua sendo a razão `calc(1.25 / 0.875)`, não um valor fixo em px

## 2. E-mails transacionais

- [x] 2.1 Em [resources/views/emails/vagas/layout.blade.php](resources/views/emails/vagas/layout.blade.php), somar `0.125rem` às 8 declarações de `font-size`: `.header h1` 1.25→1.375, `.header p` 0.82→0.945, `.footer` 0.75→0.875, `.btn` 0.95→1.075, `.info-box` 0.875→1, `.info-row` 0.875→1, `h2` 1.15→1.275, `p` 0.9→1.025
- [x] 2.2 Em [alerta-nova-vaga.blade.php](resources/views/emails/vagas/alerta-nova-vaga.blade.php), alterar o `font-size:12px` inline para `14px` (única declaração em px do conjunto — manter a unidade)
- [x] 2.3 Em [vaga-recusada.blade.php](resources/views/emails/vagas/vaga-recusada.blade.php), alterar `0.78rem`→`0.905rem` e `0.875rem`→`1rem`
- [x] 2.4 Em [redefinir-senha-candidato.blade.php](resources/views/emails/vagas/redefinir-senha-candidato.blade.php) e [verificar-email-candidato.blade.php](resources/views/emails/vagas/verificar-email-candidato.blade.php), alterar `0.8rem`→`0.925rem` em cada
- [x] 2.5 Varrer `resources/views/emails/` por `font-size` e conferir que as 13 declarações foram atualizadas e nenhum valor antigo sobrou

## 2b. Tamanhos arbitrários fora da escala

- [x] 2b.1 [candidaturas-list.jsx:74](resources/js/Components/candidaturas-list.jsx#L74) e [PublicLayout.jsx:113](resources/js/Layouts/PublicLayout.jsx#L113): `0.6rem`→`0.725rem`
- [x] 2b.2 [CandidaturaTimeline.jsx:45,65](resources/js/Components/CandidaturaTimeline.jsx#L45): `0.65rem`→`0.775rem`
- [x] 2b.3 [Registro.jsx:85](resources/js/Pages/Candidato/Auth/Registro.jsx#L85) `0.68rem`→`0.805rem`; [PasswordStrengthMeter.jsx:43](resources/js/Components/PasswordStrengthMeter.jsx#L43) e [FazendaRessacada.jsx:166,236,343](resources/js/Pages/Publico/FazendaRessacada.jsx#L166) `0.7rem`→`0.825rem`
- [x] 2b.4 [ui/button.jsx:27](resources/js/Components/ui/button.jsx#L27) (`size="sm"`): `0.8rem`→`0.925rem`
- [x] 2b.5 [Vagas/Show.jsx:25,96](resources/js/Pages/Publico/Vagas/Show.jsx#L25): `1.125rem`→`1.25rem`
- [x] 2b.6 Lockup do cabeçalho — [PublicLayout.jsx:89,90](resources/js/Layouts/PublicLayout.jsx#L89) `0.65rem`→`0.775rem` e `1.25rem`→`1.375rem`; [InternalLayout.jsx:51](resources/js/Layouts/InternalLayout.jsx#L51) `0.65rem`→`0.775rem`
- [x] 2b.7 Atualizar os valores do lockup no [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) para o padrão documentado não contradizer o código
- [x] 2b.8 Revarrer `resources/` com regex correto (`text-\[[0-9.]+(rem|px|pt|em)\]`) e conferir que nenhum valor antigo sobrou

## 2c. Alturas de controle (design.md, Decisão 6)

- [x] 2c.1 [badge.jsx:8](resources/js/Components/ui/badge.jsx#L8): `h-5`→`h-6.5` (20→26px); ajustar também o override `h-4` em [candidaturas-list.jsx:74](resources/js/Components/candidaturas-list.jsx#L74)
- [x] 2c.2 [button.jsx](resources/js/Components/ui/button.jsx): escada +4px — `xs` h-6→h-7, `sm` h-7→h-8, `default` h-8→h-9, `lg` h-9→h-10, e os `size-*` dos botões de ícone equivalentes
- [x] 2c.3 [input.jsx:15](resources/js/Components/ui/input.jsx#L15): `h-8`→`h-9`, `file:h-6`→`file:h-7`
- [x] 2c.4 [select.jsx:44](resources/js/Components/ui/select.jsx#L44): `default` h-8→h-9, `sm` h-7→h-8
- [x] 2c.5 [input-group.jsx:18,70](resources/js/Components/ui/input-group.jsx#L18): `h-8`→`h-9`, `xs` h-6→h-7; [command.jsx:65](resources/js/Components/ui/command.jsx#L65): `h-8!`→`h-9!`
- [x] 2c.6 [tabs.jsx:24](resources/js/Components/ui/tabs.jsx#L24): `h-8`→`h-9`, para a barra de abas alinhar com os demais controles
- [x] 2c.7 Rebuildar e conferir que nenhum tamanho nomeado de controle empatou com outro

## 3. Verificação visual — telas públicas

- [x] 3.1 Listagem pública ([Publico/Vagas/Index.jsx](resources/js/Pages/Publico/Vagas/Index.jsx)) em desktop largo: as três colunas (filtros, lista, detalhe) continuam cabendo lado a lado; nenhum título de vaga na coluna estreita passou a ser truncado
- [x] 3.2 Listagem pública em largura de celular: o título display responsivo da [linha 120](resources/js/Pages/Publico/Vagas/Index.jsx#L120) (`text-3xl`/`4xl`/`5xl` por breakpoint) não quebra feio nem transborda
- [x] 3.3 Detalhe de vaga ([Publico/Vagas/Show.jsx](resources/js/Pages/Publico/Vagas/Show.jsx)): descrição longa legível, entrelinhamento sem sobreposição, badges de status com texto inteiro
- [x] 3.4 Formulário de candidatura ([Publico/Candidatura.jsx](resources/js/Pages/Publico/Candidatura.jsx)): labels, campos e mensagens de validação sem corte
- [x] 3.5 [Publico/Alertas.jsx](resources/js/Pages/Publico/Alertas.jsx), [ConsultaCandidatura.jsx](resources/js/Pages/Publico/ConsultaCandidatura.jsx), [Confirmacao.jsx](resources/js/Pages/Publico/Confirmacao.jsx), [PoliticaPrivacidade.jsx](resources/js/Pages/Publico/PoliticaPrivacidade.jsx) e [FazendaRessacada.jsx](resources/js/Pages/Publico/FazendaRessacada.jsx) — passada rápida, desktop e mobile
- [x] 3.6 [Layouts/PublicLayout.jsx](resources/js/Layouts/PublicLayout.jsx): cabeçalho e rodapé sem quebra de alinhamento nem estouro de navegação

## 4. Verificação visual — telas internas

- [x] 4.1 [Layouts/InternalLayout.jsx](resources/js/Layouts/InternalLayout.jsx): rótulos do menu lateral cabem na largura fixa da sidebar, sem truncar
- [x] 4.2 Tabelas de candidaturas ([Coord/Candidaturas/Index.jsx](resources/js/Pages/Coord/Candidaturas/Index.jsx), [Todas.jsx](resources/js/Pages/Coord/Candidaturas/Todas.jsx)) com dados reais: nenhuma coluna passou a truncar conteúdo que antes aparecia inteiro
- [x] 4.3 [Coord/Candidaturas/Show.jsx](resources/js/Pages/Coord/Candidaturas/Show.jsx) — tela mais densa em `text-xs` (7 usos): conferir todos os rótulos auxiliares
- [x] 4.4 [Coord/Vagas/Form.jsx](resources/js/Pages/Coord/Vagas/Form.jsx): formulário longo, selects e campos com texto sem corte vertical
- [x] 4.5 [Coord/Dashboard.jsx](resources/js/Pages/Coord/Dashboard.jsx) e [Gestor/Dashboard.jsx](resources/js/Pages/Gestor/Dashboard.jsx): números e rótulos dos cards de métrica sem estouro
- [x] 4.6 [Gestor/Vagas/Index.jsx](resources/js/Pages/Gestor/Vagas/Index.jsx) e [Show.jsx](resources/js/Pages/Gestor/Vagas/Show.jsx); área do candidato ([Candidato/Candidaturas/](resources/js/Pages/Candidato/Candidaturas/), [Candidato/Perfil/Edit.jsx](resources/js/Pages/Candidato/Perfil/Edit.jsx))
- [x] 4.7 Telas de autenticação ([Auth/Login.jsx](resources/js/Pages/Auth/Login.jsx), [Candidato/Auth/](resources/js/Pages/Candidato/Auth/)) e [Layouts/AuthLayout.jsx](resources/js/Layouts/AuthLayout.jsx)

## 5. Verificação visual — componentes de altura fixa

- [x] 5.1 Varrer [resources/js/Components/ui/](resources/js/Components/ui/) por classes `h-*` fixas em componentes que contêm texto (button, input, select, tabs, dropdown) e listar os que precisam de ajuste
- [x] 5.2 Botões em todas as variantes e tamanhos: texto verticalmente centrado, sem corte no topo ou base
- [x] 5.3 Inputs, selects e textareas: texto digitado e placeholder inteiros; conferir o padding de select conforme a preferência já registrada no projeto
- [x] 5.4 Badges e chips de status: `text-xs` a 14px dentro do padding atual — caso mais provável de estouro, conferir explicitamente
- [x] 5.5 Toasts (sonner), tooltips, diálogos e o command palette (cmdk)
- [x] 5.6 Aplicar os ajustes de altura/padding apenas nos componentes comprovadamente quebrados, aumentando o espaço — nunca reduzindo o tamanho de fonte (design.md, Decisão 6)

## 6. Fechamento

- [x] 6.1 Repetir uma amostra das telas mais densas (3.1, 4.2, 4.3) no tema escuro, confirmando resultado tipográfico idêntico ao claro
- [x] 6.2 Confirmar que nenhum valor arbitrário de fonte (`text-[...]`, `style={{ fontSize }}`) foi introduzido durante os ajustes
- [x] 6.3 Disparar um e-mail transacional de teste e conferir a hierarquia (título > corpo > rodapé) preservada no cliente de e-mail
- [x] 6.4 Revisar o diff completo: a mudança deve ser o bloco `@theme`, os 5 templates de e-mail, e apenas os ajustes de layout justificados na tarefa 5.6
