## Why

O frontend hoje é uma SPA Inertia.js + React (25 páginas em `resources/js/Pages`, ~40 componentes, incluindo uma biblioteca shadcn/radix-ui completa em `Components/ui`). Isso exige um pipeline Node/Vite com dependências React, JSX e um bundle client-side para renderizar páginas que, em sua maioria, são formulários e listagens simples. O usuário quer eliminar essa complexidade: remover React e Inertia por completo e voltar a um modelo Laravel clássico — Blade para renderização server-side e JavaScript puro (sem framework/bundler de componentes) para as poucas interações que hoje dependem de estado no cliente, mantendo Tailwind CSS para estilos.

## What Changes

- **BREAKING**: Remove Inertia.js do backend e do frontend (`@inertiajs/react`, `laravel-vite-plugin` config de Inertia, middleware `HandleInertiaRequests` se existir, `Route::inertia`, chamadas `Inertia::render`).
- **BREAKING**: Remove React, ReactDOM, `@vitejs/plugin-react`, shadcn/radix-ui, `cmdk`, `lucide-react`, `class-variance-authority`, `tailwind-merge`, `sonner`, `ziggy-js` e todo `resources/js/**/*.jsx`.
- Converte as 25 páginas Inertia (`resources/js/Pages/**`) em views Blade equivalentes em `resources/views/**`, com os 3 layouts (`AuthLayout`, `InternalLayout`, `PublicLayout`) recriados como `@extends`/`@component`/layouts Blade.
- Reimplementa os componentes de UI reutilizáveis (cards, badges, paginação, tabelas, dialogs, dropdowns, tabs, toggles, etc.) como Blade components (`resources/views/components/**`) estilizados com Tailwind puro, sem dependência de biblioteca de componentes React.
- Reimplementa em JavaScript puro (sem framework) as interações que hoje dependem de estado React/Inertia, preservando o comportamento já validado com o usuário: filtros que auto-aplicam ao mudar, upload de currículo por dropzone com progresso, medidor de força de senha, alternância de tema (dark/light), toasts/flash messages, e paginação/navegação sem F5 completo onde já existia.
- Controllers que hoje retornam `Inertia::render(...)` passam a retornar `view(...)` com os mesmos dados; requisições de formulário passam a ser submissões HTML clássicas (ou `fetch` pontual em JS puro) com validação e redirect/flash do Laravel, no lugar do ciclo de visita Inertia.
- Atualiza `resources/views/app.blade.php`, `vite.config.js` e `package.json` para o novo pipeline (Tailwind + JS puro, sem plugin React/Inertia).
- Remove os testes de frontend específicos de React/Inertia (se houver) e ajusta os testes Feature (PHPUnit) que hoje fazem asserts em payload Inertia (`assertInertia`) para asserts em conteúdo/estrutura Blade.
- Mantém 1:1 o comportamento, layout visual e regras de negócio já especificados (login/registro de candidato, listagem/detalhe pública de vagas, inscrição, alertas de vaga, perfil único do candidato, retenção de contas inativas, visibilidade de candidatura para coordenador) — esta é uma migração de tecnologia de apresentação, não uma mudança de requisitos.

## Capabilities

### New Capabilities

_Nenhuma. Esta mudança não introduz comportamento novo para o usuário final._

### Modified Capabilities

_Nenhuma requisição de comportamento muda._ Esta é uma migração pura de stack de apresentação (React/Inertia → Blade/Tailwind/JS puro): todas as capacidades existentes (`alertas-vagas`, `cadastro-candidato-conta`, `candidatura-vinculada-a-conta`, `navegacao-publica`, `perfil-candidato-unico`, `tipografia-base`, `vagas-listagem-publica`, `visibilidade-candidatura-coordenador`) devem continuar se comportando exatamente como especificado. `skip_specs: true` está setado neste change; `design.md` documenta o mapeamento técnico página a página e componente a componente.

## Impact

- **Removido**: `resources/js/**/*.jsx` (Pages, Components/components duplicados por causa do case, Layouts, hooks, lib), dependências `@inertiajs/react`, `react`, `react-dom`, `@vitejs/plugin-react`, `radix-ui`, `shadcn`, `cmdk`, `lucide-react`, `class-variance-authority`, `tailwind-merge`, `tw-animate-css`, `sonner`, `ziggy-js`.
- **Adicionado**: `resources/views/**` (novas views/partials/components Blade), JS puro em `resources/js/**` (sem JSX, sem framework), possivelmente pequenas libs vanilla (ex.: máscara de CPF, dropzone) reescritas sem dependência React.
- **Modificado**: todos os controllers em `app/Http/Controllers/{Auth,Candidato,Coord,Gestor,Vagas}` que hoje chamam `Inertia::render`; `routes/web.php` (remove `Route::inertia`, ajusta nomes/params se necessário); `resources/views/app.blade.php`; `vite.config.js`; `package.json`; `app/Providers/AppServiceProvider.php` (uso de `Vite::prefetch`, específico de bundling client-side, deve ser revisado).
- **Testes**: `tests/Feature/**` que usam `assertInertia`/Inertia testing helpers precisam ser reescritos para asserts sobre views/HTML Blade.
- **Sem impacto em**: schema de banco de dados, models, regras de negócio/policies, integração com DRHFlow (SQL Server), envio de e-mails (`resources/views/emails/**` já é Blade puro e não muda).
