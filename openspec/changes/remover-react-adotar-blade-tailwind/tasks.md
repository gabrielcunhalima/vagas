## 1. Fundação: pipeline e layouts base

> Nota de sequenciamento (ajuste sobre a ordem original do design.md — "Migration Plan"): remover React/Inertia de `package.json`/`composer.json` e do `vite.config.js` só é seguro depois que NENHUM controller chama mais `Inertia::render` (fim da fase 4). Enquanto isso, o novo entry point JS puro convive com o antigo `app.jsx` (dois entries no mesmo `vite.config.js`, plugin React ainda presente). 1.1/1.2 abaixo cobrem só a adição do novo entry; a remoção efetiva das dependências virou 5.1/5.3.

- [x] 1.1 Adicionar o novo entry point JS puro (`resources/js/app.js`) ao `vite.config.js`, mantendo `resources/js/app.jsx` e o plugin React intactos até a fase 5 (remoção de dependências fica em 5.1/5.3).
- [x] 1.2 Ajustar `vite.config.js`: adicionar `resources/js/app.js` aos `input` do plugin `laravel-vite-plugin`, ao lado de `resources/css/app.css` e `resources/js/app.jsx` (que segue existindo até a fase 5).
- [x] 1.3 Criar `resources/views/components/layouts/app.blade.php` (base HTML para as novas views Blade, com `@vite(['resources/css/app.css', 'resources/js/app.js'])`, sem `@inertia`/`@inertiaHead`/`@viteReactRefresh`) mantendo `@include('partials.theme-script')`, favicon, fontes; `resources/views/app.blade.php` (raiz Inertia) não é tocado até a fase 5. Nota: ficou em `components/layouts/` (não `layouts/`) para que `<x-layouts.app>` resolva via a convenção de auto-discovery de componentes do Blade — os outros layouts (public/internal/auth) o consomem assim.
- [x] 1.4 Criar `resources/views/components/layouts/public.blade.php` a partir de `Layouts/PublicLayout.jsx` (header, footer, container, flash messages, alternância de tema, aviso de e-mail não verificado/perfil incompleto lendo `auth('candidato')->user()` direto — sem prop `auth.candidato` vinda de Inertia).
- [x] 1.5 Criar `resources/views/components/layouts/internal.blade.php` + partial `partials/internal-sidebar.blade.php` a partir de `Layouts/InternalLayout.jsx` (sidebar coordenador/gestor, topbar, slots `pageTitle`/`breadcrumb`/`topbarActions`, menu mobile via off-canvas).
- [x] 1.6 Criar `resources/views/components/layouts/auth.blade.php` a partir de `Layouts/AuthLayout.jsx`.
- [x] 1.7 Criar componentes Blade de UI genéricos sem estado em `resources/views/components/ui/` (button, input, textarea, label, badge, alert(+title/description), card(+header/title/description/content/footer), table(+header/body/row/head/cell), separator, avatar(+fallback), progress, input-group(+addon)) traduzindo classes Tailwind 1:1 dos equivalentes em `resources/js/components/ui/*.jsx`. `select`/`dialog`/`sheet`/`dropdown-menu`/`popover`/`command`/`tabs`/`switch`/`checkbox`/`radio-group`/`alert-dialog` ficam para quando as páginas que os usam forem migradas (fases 2-4), priorizando HTML nativo por decisão de design.
- [x] 1.8 Criar componentes Blade compostos em `resources/views/components/`: `logo`, `flash-messages` (substitui `FlashMessages.jsx` + `sonner`, toasts via `resources/js/flash.js`), `empty-state`, `stat-card`, `field` (wired ao `$errors` bag do Laravel em vez de prop de erro manual), `pagination` (usa `linkCollection()` do paginator Laravel — mesmo formato de link que o JSON antigo), `badges/*` (status-vaga, status-candidatura, tipo, tipo-admissao, modalidade, nova — reaproveitando `Vaga::$statusLabel`/`$tiposLabel`/`$modalidadesLabel` e `Candidatura::$statusLabel` já existentes no PHP, em vez de duplicar em `lib/enums.js`), `politica-privacidade-conteudo`, `icons/whats-app`.
- [x] 1.9 Implementar `resources/js/tema.js` (liga botões `[data-tema-toggle]` ao `window.toggleTheme()` já definido em `partials/theme-script.blade.php`) e `resources/js/app.js` como entry point que inicializa todos os widgets em `DOMContentLoaded` e expõe `window.initWidgets()` para re-inicializar fragmentos trocados por fetch.
- [x] 1.10 Implementar `resources/js/senha-forca.js` (medidor de força, substitui `PasswordStrengthMeter.jsx`) como widget reutilizável por `data-senha-forca-input`.
- [x] 1.11 Implementar `resources/js/curriculo-dropzone.js` (arrastar/soltar, preview, limpar; substitui `CurriculoDropzone.jsx`) como widget por `data-curriculo-dropzone`, com fallback sem JS (input nativo visível). Também implementado `resources/js/ui/sheet.js` (off-canvas genérico usado pelo menu mobile dos layouts) e `resources/js/flash.js`, não previstos originalmente mas necessários para os layouts 1.4/1.5.
- [x] 1.12 Ajuste de escopo: `lib/format.js` e `lib/enums.js` NÃO foram portados para JS — como o Blade renderiza no servidor, os rótulos/formatações desses módulos viram lógica PHP (accessors/estáticos já existentes em `Vaga`/`Candidatura`, mais o novo helper `app/Support/Iniciais.php` para as iniciais do avatar). `lib/cpf.js` (máscara de CPF/CEP/telefone digitada pelo usuário) continua necessária em JS e fica adiada para a fase 3 (formulários de candidato), onde é de fato usada.

## 2. Público: home, listagem/detalhe de vagas, inscrição, alertas

- [ ] 2.1 Migrar `Publico/Vagas/Index.jsx` para `resources/views/publico/vagas/index.blade.php`: hero + busca, filtros (tipo, escolaridade, projeto, cidade/UF, faixa salarial, ordenação), lista + painel de detalhe master-detail, paginação, estados vazio/indisponível.
- [ ] 2.2 Implementar o comportamento "filtro auto-aplica" em JS puro (fetch progressivo para a mesma rota GET, troca do HTML da lista+detalhe preservando scroll; fallback para submit de formulário GET sem JS) — ver Decisão 3 do design.md.
- [ ] 2.3 Implementar o painel de detalhe da vaga (`VagaDetalhePainel.jsx` → partial Blade) e o item de lista (`VagaListaItem.jsx` → partial Blade), reaproveitados tanto na renderização cheia quanto no fragmento por fetch.
- [ ] 2.4 Implementar a versão mobile do painel de detalhe (hoje `Sheet` do Radix) como off-canvas em JS puro (abrir/fechar por classe, travar scroll do body) — sem framework de portal.
- [ ] 2.5 Ajustar `VagaPublicaController` para responder com a view completa ou só o fragmento de lista+detalhe, conforme o header/parametro que o fetch do passo 2.2 envia.
- [ ] 2.6 Migrar `Publico/Vagas/Show.jsx` para `resources/views/publico/vagas/show.blade.php`.
- [ ] 2.7 Migrar `Publico/Candidatura.jsx` (formulário de inscrição, com `CurriculoDropzone`) e `Publico/Confirmacao.jsx`.
- [ ] 2.8 Migrar `Publico/Alertas.jsx`, `Publico/AlertaCancelado.jsx`, `Publico/ConsultaCandidatura.jsx`.
- [ ] 2.9 Migrar `Publico/PoliticaPrivacidade.jsx` e `Publico/FazendaRessacada.jsx`; trocar `Route::inertia(...)` por `Route::view(...)` em `routes/web.php`.
- [ ] 2.10 Migrar `Pages/Error.jsx` para a página de erro Blade padrão do Laravel (`resources/views/errors/*.blade.php`) ou manter uma view custom equivalente.
- [ ] 2.11 Atualizar `VagaPublicaController`, `InscricaoController`, `AlertaVagaController` trocando `Inertia::render` por `view()`.
- [ ] 2.12 Reescrever `tests/TestCase.php`: remover `propsInertia`/`assertComponenteInertia`/`assertVeInertia`/`assertNaoVeInertia`/`propsInertiaJson`; usar `assertViewIs`/`assertSee`/`assertDontSee`/`assertViewHas` nativos do Laravel.
- [ ] 2.13 Atualizar `tests/Feature/VagaPublicaTest.php`, `tests/Feature/InscricaoTest.php`, `tests/Feature/AlertaVagaTest.php`, `tests/Feature/Drhflow/AcompanhamentoInscricaoTest.php` para as novas asserções Blade; rodar e confirmar verde antes de seguir.

## 3. Candidato: conta, perfil, minhas candidaturas

- [ ] 3.1 Migrar `Candidato/Auth/{Login,Registro,EsqueciSenha,RedefinirSenha,VerificarEmail}.jsx` para views em `resources/views/candidato/auth/`.
- [ ] 3.2 Migrar `Candidato/Perfil/Edit.jsx` (dados, senha, currículo, exportar dados, excluir conta) reaproveitando os widgets de senha (1.10) e dropzone (1.11).
- [ ] 3.3 Migrar `Candidato/Candidaturas/Index.jsx` e `Show.jsx` (com `AndamentoInscricao.jsx`/`CandidaturaTimeline.jsx` → partials Blade).
- [ ] 3.4 Atualizar `CandidatoLoginController`, `CandidatoRegistroController`, `CandidatoRecuperarSenhaController`, `CandidatoVerificacaoController`, `PerfilController`, `MinhaCandidaturaController` trocando `Inertia::render` por `view()`.
- [ ] 3.5 Atualizar `tests/Feature/CandidaturaTriagemTest.php`, `tests/Feature/ExclusaoContaTest.php`, `tests/Feature/AuthTest.php` para as novas asserções Blade; rodar e confirmar verde.

## 4. Coordenador e Gestor: painéis internos

- [ ] 4.1 Migrar `Coord/Dashboard.jsx`, `Coord/Vagas/{Index,Form}.jsx`, `Coord/Candidaturas/{Index,Show,Todas}.jsx` para `resources/views/coord/**`, usando o layout interno (1.5).
- [ ] 4.2 Migrar `Gestor/Dashboard.jsx`, `Gestor/Vagas/{Index,Show}.jsx` para `resources/views/gestor/**`.
- [ ] 4.3 Migrar componentes usados só no painel interno: `candidaturas-list.jsx`, `VagaCard.jsx`, `StatCard.jsx` (se ainda não coberto em 1.8).
- [ ] 4.4 Atualizar `VagaController`, `CandidaturaController`, `DashboardController` trocando `Inertia::render` por `view()`.
- [ ] 4.5 Atualizar `tests/Feature/VagaCoordenadorTest.php`, `tests/Feature/VagaGestorTest.php` para as novas asserções Blade; rodar e confirmar verde.

## 5. Limpeza final

- [ ] 5.1 Apagar `resources/js/Pages`, `resources/js/Components`, `resources/js/components` (a cópia duplicada por case), `resources/js/Layouts`, `resources/js/hooks`, `resources/js/app.jsx`, `resources/js/bootstrap.js` (se específico de Inertia/React) e qualquer `.jsx` remanescente.
- [ ] 5.2 Revisar `app/Providers/AppServiceProvider.php`: decidir se `Vite::prefetch(concurrency: 3)` ainda se aplica ao novo pipeline JS puro (era otimização de bundle SPA) e ajustar/remover.
- [ ] 5.3 Rodar `composer install`/`npm install` limpos e `npm run build` para confirmar que o pipeline novo compila sem dependências React/Inertia residuais.
- [ ] 5.4 Rodar a suíte completa (`php artisan test` / phpunit) e confirmar 100% verde.
- [ ] 5.5 Validar manualmente no navegador (via skill `run`) os fluxos principais: home/listagem de vagas com filtros, inscrição com upload de currículo, cadastro/login/recuperação de senha de candidato, perfil do candidato, painel do coordenador (vagas + candidaturas), painel do gestor (autorizar vaga), alternância de tema.
- [ ] 5.6 Atualizar `README.md` se ele documentar o stack React/Inertia.
