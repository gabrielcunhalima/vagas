## 1. Reestruturar a hero em duas colunas

- [x] 1.1 Em [Index.jsx](resources/js/Pages/Publico/Vagas/Index.jsx#L118-L167), trocar o filho único `<div className="max-w-2xl">` do `CONTAINER` por um grid: `grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-center lg:gap-12`
- [x] 1.2 Mover título, contagem de vagas e `<form>` de busca para a coluna esquerda, aplicando nela o `max-w-2xl` que hoje está no wrapper
- [x] 1.3 Mover o bloco do card de alerta para a coluna direita do grid, removendo o `mt-6` e o `max-w-xl` (a largura passa a ser a da coluna)
- [x] 1.4 Conferir que a busca continua com `max-w-xl` própria e que o `onSubmit`/`aplicar()` seguem intactos

## 2. Adaptar o card aos três estados

- [x] 2.1 Ajustar o container do card para `flex-col sm:flex-row sm:items-center lg:flex-col lg:items-start`, mantendo `rounded-xl bg-white/10 p-4 ring-1 ring-white/25 backdrop-blur-sm transition-colors hover:bg-white/15`
- [x] 2.2 Ajustar o botão "Criar alerta de vagas" para `w-full sm:w-auto lg:w-full`, preservando `asChild` + `<Link href={route('alertas.create')}>`
- [x] 2.3 Verificar que ícone `Bell`, título e descrição não mudaram de texto, tamanho nem cor

## 3. Reduzir a altura da hero

- [x] 3.1 Trocar `py-16 lg:py-24` do container da hero por `py-10 lg:py-14`
- [x] 3.2 Medir a altura da hero em `lg` estreito (~1024px) e confirmar que ficou menor que antes — 568px → 316px em 1024px, e 568px → 341px em 1440px; o `lg:gap-12` não precisou ser reduzido

## 4. Verificar nos breakpoints

- [x] 4.1 `≥1280px`: título/contagem/busca à esquerda e card à direita na mesma faixa vertical, sem sobreposição, dentro do container 10-80-10 — conferido em 1440px, card entre x=936 e x=1296 (borda direita do container)
- [x] 4.2 `1024px`: duas colunas cabem e a busca continua utilizável; o título quebrava em **três** linhas com `lg:text-5xl`, corrigido para `xl:text-5xl` (mitigação prevista na design.md) e agora quebra em duas
- [x] 4.3 `<1024px`: card volta a empilhar abaixo da busca, largura cheia, sem rolagem horizontal — conferido em 1023px e 750px
- [x] 4.4 `<640px`: card em coluna (ícone, texto, botão de largura total), como antes da mudança — conferido em 500px, que exercita o mesmo estado (abaixo de `sm` as classes são idênticas); larguras CSS abaixo de ~500px não são renderizáveis no Chrome headless em Windows por causa da largura mínima de janela
- [x] 4.5 Redimensionar a janela cruzando `lg` e confirmar que o card não some, não duplica e não deixa espaço vazio — renderizado nos dois lados do breakpoint (1023px e 1024px) e o DOM traz uma única instância do card, então a transição é só troca de media query sobre o mesmo nó
- [x] 4.6 Conferir contraste do card sobre `home-hero.jpg` na área direita — legível em tema claro e escuro em 1440px e 1024px; `bg-white/10` mantido
- [x] 4.7 Clicar em "Criar alerta de vagas" e confirmar que leva a `alertas.create` — `href="/vagas/public/alertas"` no DOM renderizado
- [x] 4.8 Repetir 4.1 e 4.3 em tema escuro — conferido nos dois temas (escuro em 1440/1024/1023, claro em 1440/1023)

## 5. Documentação

- [x] 5.1 Atualizar a linha "Hero público (home)" da seção 8 do [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md#L152) para registrar a hero em duas colunas com o CTA de alerta na direita
