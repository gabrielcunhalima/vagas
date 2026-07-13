# Design System — Portal de Vagas FAPEU

Documentação completa do sistema de design utilizado no Portal de Vagas da FAPEU. Este documento descreve todos os tokens, componentes, padrões e diretrizes visuais do sistema.

---

## Sumário

1. [Fundação](#1-fundação)
2. [Tipografia](#2-tipografia)
3. [Paleta de Cores](#3-paleta-de-cores)
4. [Espaçamento](#4-espaçamento)
5. [Bordas e Sombras](#5-bordas-e-sombras)
6. [Botões](#6-botões)
7. [Formulários](#7-formulários)
8. [Cards](#8-cards)
9. [Badges e Status](#9-badges-e-status)
10. [Alertas](#10-alertas)
11. [Tabelas](#11-tabelas)
12. [Navegação — Navbar Pública](#12-navegação--navbar-pública)
13. [Navegação — Sidebar Interna](#13-navegação--sidebar-interna)
14. [Topbar Interna](#14-topbar-interna)
15. [Layouts](#15-layouts)
16. [Animações](#16-animações)
17. [Responsividade](#17-responsividade)
18. [Iconografia](#18-iconografia)
19. [Padrões de Página](#19-padrões-de-página)

---

## 1. Fundação

### Stack de tecnologia

| Camada      | Tecnologia                          |
|-------------|-------------------------------------|
| Framework   | Laravel 13 (Blade templates)        |
| CSS base    | Bootstrap 5.3.3                     |
| Ícones      | Bootstrap Icons 1.11.3              |
| Tipografia  | Google Fonts (DM Sans) |
| Build tool  | Vite 8                              |

### Filosofia visual

O sistema adota uma identidade verde institucional derivada da marca FAPEU. O design é limpo, profissional e acessível, com dois contextos distintos:

- **Público** — páginas abertas para candidatos e visitantes. Tom mais expressivo, com gradientes e animações sutis.
- **Interno** — painel administrativo para coordenadores e gestores. Tom mais sóbrio, focado em dados e eficiência.

---

## 2. Tipografia

### Fontes

| Papel        | Família             | Uso                                         |
|--------------|---------------------|---------------------------------------------|
| Principal    | Reddit Sans         | Todo o corpo de texto, labels, botões, títulos |
| Secundária   | DM Sans             | Disponível mas não usada ativamente          |

```html
<link href="https://fonts.googleapis.com/css2?family=Reddit+Sans:ital,wght@0,200..900;1,200..900&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
```

### Tamanhos

| Token           | Valor       | Uso                                         |
|-----------------|-------------|---------------------------------------------|
| `--text-xs`     | 0.68rem     | Labels de seção na sidebar, metadados       |
| `--text-sm`     | 0.75–0.8rem | Badges, metadados, labels de formulário     |
| `--text-base`   | 0.875rem    | Corpo do painel interno, células de tabela  |
| `--text-md`     | 0.9rem      | Corpo público, links de navegação           |
| `--text-root`   | 18px        | Tamanho base (`font-size` no `html`)        |
| `--text-lg`     | 1rem        | Títulos de card, topbar-title               |
| `--text-xl`     | 1.1rem      | Brand na navbar                             |
| `--text-2xl`    | 1.75rem     | stat-number (dashboard)                     |
| `--text-hero`   | 2rem        | Título do hero (listagem pública)           |

### Pesos

| Peso  | Uso                                                    |
|-------|--------------------------------------------------------|
| 400   | Corpo de texto genérico                                |
| 500   | Links de navegação, metadados com destaque             |
| 600   | Labels de formulário, botões, headers de card          |
| 700   | Títulos de seção, navbar brand, nomes de usuário       |
| 800   | stat-number, título do hero                            |

### Line-height

- Corpo de texto: `1.5`–`1.6`
- Títulos de card / topbar: `1.2`
- stat-number: `1` (sem entrelinha)

---

## 3. Paleta de Cores

### Cores primárias (marca)

| Nome            | Hex       | Uso                                                       |
|-----------------|-----------|-----------------------------------------------------------|
| Principal       | `#0D9571` | Botão primário, badge ativa, header de card, link ativo   |
| Principal hover | `#0C8061` | Estado hover do botão principal                           |
| Verde escuro    | `#074635` | Navbar pública, sidebar interna, footer                   |
| Verde médio     | `#2C4A44` | Títulos internos (topbar-title, section headers)          |
| Verde accent    | `#197060` | Variante de gradiente e detalhes de acento                |

### Neutros

| Nome          | Hex       | Uso                                              |
|---------------|-----------|--------------------------------------------------|
| Branco        | `#FFFFFF` | Fundo de cards, área de conteúdo, topbar         |
| Fundo         | `#F8F9FA` | Background global do `body`                      |
| Cinza claro   | `#E9ECEF` | Bordas de tabela, divisores, border da topbar    |
| Cinza medio   | `#6C757D` | Textos secundários, ícones, breadcrumbs          |
| Carvão        | `#3E3E3F` | Cor base do texto (`color` no body)              |
| Cinza label   | `#495057` | Labels de formulário                             |

### Cores semânticas (status)

| Status                   | Fundo      | Texto     | Uso                                     |
|--------------------------|------------|-----------|------------------------------------------|
| Rascunho / Inativa       | `#E9ECEF`  | `#6C757D` | Vaga ainda não publicada                 |
| Aguardando / Em análise  | `#FFF3CD`  | `#856404` | Aguardando aprovação do gestor           |
| Ativa / Aprovado         | `#D1E7DD`  | `#0A5234` | Vaga publicada, candidatura aprovada     |
| Encerrada                | `#E2E3E5`  | `#383D41` | Prazo encerrado                          |
| Recusada / Reprovada     | `#F8D7DA`  | `#842029` | Rejeitada pelo gestor                    |
| Recebida                 | `#CFE2FF`  | `#084298` | Candidatura recém-recebida               |
| Entrevista               | `#CFF4FC`  | `#055160` | Candidato em processo de entrevista      |
| Danger (sistema)         | `#DC3545`  | —         | Erros, dias restantes críticos (≤5 dias) |
| Warning (sistema)        | `#FFC107`  | —         | Avisos gerais                            |
| Info (sistema)           | `#17A2B8`  | —         | Informações neutras                      |

### Cores por tipo de vaga

| Tipo     | Cor   | Hex       |
|----------|-------|-----------|
| Estágio  | Roxo  | `#6F42C1` |
| Emprego  | Azul  | `#0D6EFD` |
| Bolsa    | Verde | `#0D9571` |

Essas cores são usadas no título do card, nos ícones de metadados e no botão "Ver vaga" de cada listagem.

### Transparências recorrentes

| Uso                                    | Valor                                  |
|----------------------------------------|----------------------------------------|
| Overlay da sidebar (item hover)        | `rgba(255,255,255,0.06)`               |
| Overlay da sidebar (item ativo)        | `rgba(13,149,113,0.20)`                |
| Borda de divisão da sidebar            | `rgba(255,255,255,0.08)`               |
| Badge/pill da navbar                   | `rgba(255,255,255,0.12)`, borda `0.25` |
| Foco de formulário                     | `rgba(13,149,113,0.25)`                |
| Alert principal (fundo)                | `rgba(13,149,113,0.05–0.06)`           |

---

## 4. Espaçamento

O sistema usa a escala de espaçamento do Bootstrap 5 (`0.25rem` × n), complementada por valores customizados:

| Escala | Valor    | Uso principal                                   |
|--------|----------|-------------------------------------------------|
| 4px    | 0.25rem  | Gap mínimo entre ícone e texto                  |
| 8px    | 0.5rem   | Gap interno em badges, separador fino           |
| 10px   | 0.625rem | Padding de badges, padding de status-badge      |
| 12px   | 0.75rem  | Gap sidebar brand, padding da sidebar-footer    |
| 14px   | 0.875rem | Padding lateral de células de tabela            |
| 18px   | 1.125rem | Padding horizontal de botões outline-principal  |
| 20px   | 1.25rem  | Padding padrão de card interno, sidebar brand   |
| 22px   | 1.375rem | Padding horizontal de botões principais         |
| 24px   | 1.5rem   | Padding lateral do page-body e topbar           |
| 28px   | 1.75rem  | Padding vertical do page-body, padding hero     |

---

## 5. Bordas e Sombras

### Border-radius

| Token       | Valor | Uso                                               |
|-------------|-------|---------------------------------------------------|
| Pill        | 50px  | Botão de busca, nav-pill, "Ver vaga" pill button  |
| Round large | 25px  | Badges de autenticação da navbar                  |
| Card grande | 16px  | card-vaga (público), filtro sidebar público       |
| Card médio  | 12px  | card-interno, stat-card, sidebar brand icon       |
| Botão       | 8px   | btn-principal, btn-outline-principal              |
| Status      | 6px   | status-badge                                      |
| Badge tipo  | 20px  | badge-tipo (estágio, emprego, bolsa)              |
| Avatar      | 50%   | user-avatar                                       |

### Sombras

| Nível      | Valor                                    | Uso                              |
|------------|------------------------------------------|----------------------------------|
| Sutil      | `0 2px 8px rgba(0,0,0,0.04)`            | Topbar, navbar pública           |
| Padrão     | `0 4px 15px rgba(0,0,0,0.07)`           | card-vaga, filtro público        |
| Interno    | `0 4px 15px rgba(0,0,0,0.10)`           | stat-card, card-interno          |
| Tabela     | `0 2px 8px rgba(0,0,0,0.06)`            | table-vagas                      |
| Hover btn  | `0 8px 20px rgba(6,85,26,0.30)`         | btn-principal:hover              |
| Stat hover | `0 8px 25px rgba(12,128,97,0.30)`       | stat-card:hover                  |
| Navbar     | `0 2px 8px rgba(0,0,0,0.15)`            | navbar-fapeu (layout interno)    |

---

## 6. Botões

### `.btn-principal`

Botão primário verde, usado para ações principais (salvar, enviar candidatura, aplicar filtros).

```css
background: #0D9571;
color: #fff;
font-weight: 600;
border: 2px solid #0D9571;
border-radius: 8px;
padding: 10px 28px;        /* público */
padding: 8px 22px;         /* interno */
transition: all 0.3s ease;

:hover {
  background: #0C8061;
  border-color: #0C8061;
  box-shadow: 0 8px 20px rgba(6,85,26,0.3);
}
```

### `.btn-outline-principal`

Botão secundário, contorno verde, usado para ações alternativas (limpar filtros, visualizar).

```css
color: #0D9571;
border: 2px solid #0D9571;  /* público */
border: 1px solid #0D9571;  /* interno */
border-radius: 8px;
padding: 10px 28px;         /* público */
padding: 7px 18px;          /* interno */
font-weight: 600;
background: transparent;
transition: all 0.3s;

:hover {
  background: #0D9571;
  color: #fff;
}
```

### Pill button (inline)

Usado no botão "Ver vaga" das listagens e no botão "Buscar" do hero.

```css
display: inline-flex;
align-items: center;
gap: 0.35rem;
padding: 7px 18px;
background: <cor do tipo>;
color: #fff;
font-size: 0.82rem;
font-weight: 600;
border-radius: 20px;
transition: all 0.3s;
```

### Regra base para todos os `.btn`

```css
display: inline-flex;
align-items: center;
justify-content: center;
font-size: 0.875rem;
font-weight: 600;
border-radius: 8px;
```

---

## 7. Formulários

### Inputs e selects

```css
/* Foco */
.form-control:focus,
.form-select:focus {
  border-color: #0D9571;
  box-shadow: 0 0 0 0.2rem rgba(13,149,113,0.25);
}
```

### Labels

```css
.form-label {
  color: #495057;
  font-weight: 600;
  font-size: 0.875rem;   /* interno */
  margin-bottom: 0.4rem;
}
```

### Labels de filtro (variante uppercase)

Usados no painel de filtros da listagem pública:

```css
font-size: 0.8rem;
font-weight: 700;
color: #495057;
text-transform: uppercase;
letter-spacing: 0.4px;
```

### Campos obrigatórios

Indicados com `<span class="text-danger">*</span>` ao lado do label.

### Validação inline

```html
<input class="form-control @error('campo') is-invalid @enderror">
@error('campo') <div class="invalid-feedback">{{ $message }}</div> @enderror
```

### Seções de formulário

Cada grupo lógico de campos é envolto em um `.card-interno` com padding de `1.5rem` e um `<h6>` de título:

```css
font-weight: 700;
color: #2C4A44;
margin-bottom: 1.25rem;
padding-bottom: 0.75rem;
border-bottom: 1px solid #F1F5F4;
display: flex;
align-items: center;
gap: 0.5rem;
```

---

## 8. Cards

### `.card-vaga` (público)

Card de listagem de vagas. Layout horizontal com conteúdo principal.

```css
border: none;
border-radius: 16px;
box-shadow: 0 4px 15px rgba(0,0,0,0.07);
background: #fff;
transition: all 0.3s ease;
overflow: hidden;
```

Estrutura interna (padding `1.25rem 1.5rem`):
```
Linha superior: título linkado + badges (tipo, modalidade)
Parágrafo: descrição truncada (2 linhas, -webkit-line-clamp: 2)
Linha inferior: metadados (área, local, CH, remuneração) + prazo + botão pill
```

### `.card-interno` (painel admin)

Card usado em formulários, tabelas e seções do painel interno.

```css
border: none;
border-radius: 12px;
box-shadow: 0 4px 15px rgba(0,0,0,0.07);
background: #fff;
```

Com header (via `.card-header`):
```css
.card-interno .card-header {
  background: linear-gradient(45deg, #0d9571, #0c8061);
  color: #fff;
  font-weight: 600;
  font-size: 0.9rem;
  border-radius: 12px 12px 0 0 !important;
  padding: 0.9rem 1.25rem;
  border: none;
}
```

### `.stat-card` (dashboard)

Card de métrica com ícone + número + label.

```css
background: #fff;
border-radius: 12px;
box-shadow: 0 4px 15px rgba(0,0,0,0.10);
padding: 1.25rem 1.5rem;
display: flex;
align-items: center;
gap: 1rem;
transition: all 0.3s;

:hover {
  box-shadow: 0 8px 25px rgba(12,128,97,0.30);
}
```

Sub-elementos:

```css
.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}

.stat-number {
  font-size: 1.75rem;
  font-weight: 800;
  color: #2C4A44;
  line-height: 1;
}

.stat-label {
  font-size: 0.8rem;
  color: #6C757D;
  font-weight: 500;
  margin-top: 3px;
}
```

---

## 9. Badges e Status

### `.badge-tipo` (tipo de vaga)

```css
padding: 4px 12px;
border-radius: 20px;
font-size: 0.75rem;
font-weight: 600;
```

| Modificador                                           | Fundo                    | Texto     |
|-------------------------------------------------------|--------------------------|-----------|
| `.badge-estagio`                                      | `rgba(111,66,193,0.10)`  | `#6F42C1` |
| `.badge-emprego`                                      | `rgba(13,110,253,0.10)`  | `#0D6EFD` |
| `.badge-bolsa`                                        | `rgba(13,149,113,0.10)`  | `#0D9571` |
| `.badge-presencial` / `.badge-remoto` / `.badge-hibrido` | `#EBEBEB`             | `#555555` |

### `.status-badge` (status de vaga e candidatura)

```css
display: inline-flex;
align-items: center;
gap: 0.35rem;
font-size: 0.78rem;
font-weight: 600;
padding: 4px 10px;
border-radius: 6px;
```

| Modificador                      | Fundo      | Texto     |
|----------------------------------|------------|-----------|
| `.status-rascunho`               | `#E9ECEF`  | `#6C757D` |
| `.status-aguardando_autorizacao` | `#FFF3CD`  | `#856404` |
| `.status-ativa`                  | `#D1E7DD`  | `#0A5234` |
| `.status-encerrada`              | `#E2E3E5`  | `#383D41` |
| `.status-recusada`               | `#F8D7DA`  | `#842029` |
| `.status-inativa`                | `#E9ECEF`  | `#6C757D` |
| `.status-recebida`               | `#CFE2FF`  | `#084298` |
| `.status-em_analise`             | `#FFF3CD`  | `#856404` |
| `.status-entrevista`             | `#CFF4FC`  | `#055160` |
| `.status-aprovado`               | `#D1E7DD`  | `#0A5234` |
| `.status-reprovado`              | `#F8D7DA`  | `#842029` |

---

## 10. Alertas

### `.alert-principal`

Alerta de sucesso/informação com acento verde.

```css
border-left: 4px solid #0D9571;
background: rgba(13,149,113,0.06);
border-color: rgba(13,149,113,0.20);
color: #3E3E3F;
```

### `.alert-aviso`

Alerta de aviso com acento amarelo.

```css
border-left: 4px solid #FFC107;
background: rgba(255,193,7,0.08);
border-color: rgba(255,193,7,0.30);
```

### Alertas padrão Bootstrap

Para erros de validação usa-se `.alert-danger` nativo do Bootstrap sem customização adicional.

### Flash messages (layout interno)

Posicionados logo após o topbar, dentro de `.px-4.pt-3`:

| Chave de sessão | Classe            | Ícone                           |
|-----------------|-------------------|---------------------------------|
| `sucesso`       | `alert-principal` | `bi-check-circle-fill` verde    |
| `aviso`         | `alert-aviso`     | `bi-exclamation-triangle-fill`  |
| `erro`          | `alert-danger`    | `bi-x-circle-fill`              |

---

## 11. Tabelas

### `.table-vagas`

```css
border-collapse: separate;
border-spacing: 0;
border-radius: 8px;
overflow: hidden;
box-shadow: 0 2px 8px rgba(0,0,0,0.06);
```

**Cabeçalho:**
```css
thead th {
  background: #0D9571;
  color: #fff;
  font-weight: 600;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 12px 14px;
  border: none;
}
```

**Corpo:**
```css
tbody td {
  padding: 11px 14px;
  vertical-align: middle;
  border-bottom: 1px solid #E9ECEF;
  font-size: 0.875rem;
  background: #fff;
}

tbody tr:hover td { background: #f8faf9; }
tbody tr:last-child td { border-bottom: none; }
```

---

## 12. Navegação — Navbar Pública

### Contexto: `layouts/publico.blade.php`

```css
.navbar-fapeu {
  background: #074635;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
```

Na welcome page (fundo animado), variante com blur:
```css
background: rgba(7,70,53,0.55);
backdrop-filter: blur(12px);
border-bottom: 1px solid rgba(255,255,255,0.10);
```

**Brand:**
```css
.navbar-brand-text { font-size: 1rem; font-weight: 700; color: #fff; }
.navbar-brand-sub  { font-size: 0.72rem; color: rgba(255,255,255,0.65); }
```

**Links:**
```css
.nav-link-pub { color: rgba(255,255,255,0.8); font-weight: 500; font-size: 0.9rem; }
.nav-link-pub:hover { color: #fff; }
```

**Botão pill da navbar (login):**
```css
padding: 7px 18px;
border-radius: 20px;
background: rgba(255,255,255,0.12);
color: #fff;
font-size: 0.875rem;
font-weight: 600;
border: 1.5px solid rgba(255,255,255,0.25);
transition: all 0.3s;
```

**Mobile:** hamburguer `.navbar-toggler` sem borda, ícone `bi-list` branco tamanho `fs-4`.

---

## 13. Navegação — Sidebar Interna

Largura fixa de **260px**, fixada à esquerda, visível em `≥ 992px`.

```css
.sidebar {
  position: fixed;
  top: 0; bottom: 0; left: 0;
  width: 260px;
  background: #074635;
  overflow-y: auto;
  transition: transform 0.3s ease;
  z-index: 100;
}
```

### Estrutura da sidebar

```
Logo + Brand
─────────────────────────────
[SEÇÃO] Coordenador
  Dashboard
  Minhas Vagas
  Nova Vaga
  Candidaturas
─────────────────────────────
[SEÇÃO] Gestor
  Dashboard
  Autorizar Vagas
─────────────────────────────
[SEÇÃO] Sistema
  Ver página pública (nova aba)
─────────────────────────────
[Footer] Usuário + logout
```

A renderização de cada seção é condicional ao `perfil` do usuário autenticado (`coordenador`, `gestor`, `admin`).

### Link de navegação

```css
.sidebar .nav-link {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  color: rgba(255,255,255,0.70);
  font-weight: 500;
  font-size: 0.88rem;
  padding: 0.6rem 1.25rem;
  border-left: 3px solid transparent;
  transition: all 0.2s;
}

.sidebar .nav-link:hover {
  color: #fff;
  background: rgba(255,255,255,0.06);
  border-left-color: rgba(255,255,255,0.30);
}

.sidebar .nav-link.active {
  color: #fff;
  background: rgba(13,149,113,0.20);
  border-left-color: #0D9571;
  font-weight: 600;
}

.sidebar .nav-link i {
  font-size: 1rem;
  width: 18px;
  text-align: center;
}
```

### Labels de seção

```css
.sidebar-section {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: rgba(255,255,255,0.35);
  padding: 1rem 1.25rem 0.35rem;
}
```

### Footer da sidebar (usuário logado)

```css
.sidebar-footer {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  padding: 1rem 1.25rem;
  border-top: 1px solid rgba(255,255,255,0.08);
}

.user-avatar {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: #0D9571;
  color: #fff;
  font-size: 0.8rem;
  font-weight: 700;
  /* Conteúdo: 2 iniciais maiúsculas do nome do usuário */
}

.user-name   { font-size: 0.82rem; font-weight: 600; color: #fff; }
.user-perfil { font-size: 0.7rem; color: rgba(255,255,255,0.50); text-transform: capitalize; }
```

---

## 14. Topbar Interna

Barra superior fixa do painel admin.

```css
.topbar {
  background: #fff;
  border-bottom: 1px solid #E9ECEF;
  padding: 0.75rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 50;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.topbar-title      { font-size: 1rem; font-weight: 700; color: #2C4A44; }
.topbar-breadcrumb { font-size: 0.78rem; color: #6C757D; }
```

O lado direito aceita ações via `@yield('topbar-actions')`. Em mobile, exibe o botão toggle da sidebar.

---

## 15. Layouts

### Layout Público (`layouts/publico.blade.php`)

```
<navbar>
<main>
  @yield('content')
</main>
<footer>
```

- Container: `.container-xl`
- Background body: `#F8F9FA`
- Footer: background `#074635`, padding `2.5rem 0 1.5rem`

### Layout Interno (`layouts/interno.blade.php`)

```
<sidebar> (260px, fixed)
<main-content> (margin-left: 260px)
  <topbar> (sticky)
  <flash-messages> (.px-4.pt-3)
  <page-body> (padding: 1.75rem 1.5rem)
    @yield('content')
  </page-body>
</main-content>
```

### Grid de formulário (dois terços + sidebar)

```html
<div class="row g-4">
  <div class="col-lg-8">  <!-- campos do formulário -->
  <div class="col-lg-4">  <!-- classificação, ações, resumo -->
```

### Hero de seção (listagem pública)

```css
background: linear-gradient(135deg, #074635 0%, #0D9571 100%);
padding: 3rem 0 2.5rem;
```

Conteúdo: título + contador de vagas (esquerda) + barra de busca pill (direita), em `.row.align-items-center.g-4`.

---

## 16. Animações

### Gradiente animado do hero (welcome page)

```css
.dynamic-gradient-bg {
  background: linear-gradient(90deg,
    rgba(47,110,82,1) 0%, rgba(24,140,109,1) 12%,
    rgba(33,145,112,1) 26%, rgba(47,168,112,1) 57%,
    rgba(65,150,113,1) 75%, rgba(43,140,113,1) 91%,
    rgba(3,115,72,1) 100%
  );
  background-size: 400% 400%;
  animation: gradientShift 22s ease infinite;
}

@keyframes gradientShift {
  0%   { background-position: 0% 50%; }
  50%  { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
```

### Transições padrão

| Contexto                   | Valor                 |
|----------------------------|-----------------------|
| Botões, cards, links       | `all 0.3s ease`       |
| Links da sidebar           | `all 0.2s`            |
| Sidebar mobile (slide)     | `transform 0.3s ease` |

---

## 17. Responsividade

### Breakpoints (Bootstrap 5)

| Breakpoint | Tamanho  | Comportamento                                         |
|------------|----------|-------------------------------------------------------|
| `xs`       | < 576px  | Layout vertical, botões full-width                    |
| `sm`       | ≥ 576px  | Pequenas melhorias de espaçamento                     |
| `md`       | ≥ 768px  | Grid 2 colunas em formulários                         |
| `lg`       | ≥ 992px  | Sidebar visível, layout 3+9 (filtros + listagem)      |

### Sidebar mobile

```css
@media (max-width: 991px) {
  .sidebar          { transform: translateX(-260px); }
  .sidebar.show     { transform: translateX(0); }
  .main-content     { margin-left: 0; }
}
```

Toggle: botão `#sidebarToggle` + `classList.toggle('show')` via JS.

### Navbar mobile

`.navbar-toggler` sem borda, ícone `bi-list fs-4` branco, colapsa em `#navPublico`.

### Cards de vaga mobile

Badges e metadados usam `flex-wrap` para quebrar linha naturalmente.

---

## 18. Iconografia

Biblioteca exclusiva: **Bootstrap Icons 1.11.3**.

### Mapeamento de ícones por contexto

| Contexto            | Ícone                          |
|---------------------|-------------------------------|
| Hamburguer (mobile) | `bi-list`                     |
| Login / acesso      | `bi-box-arrow-in-right`       |
| Logout              | `bi-box-arrow-right`          |
| Dashboard           | `bi-speedometer2`             |
| Vagas               | `bi-briefcase`                |
| Nova vaga           | `bi-plus-circle`              |
| Candidaturas        | `bi-people`                   |
| Autorizar vagas     | `bi-shield-check`             |
| Página pública      | `bi-box-arrow-up-right`       |
| Filtros             | `bi-funnel` / `bi-funnel-fill` |
| Busca               | `bi-search`                   |
| Área/tag            | `bi-tag-fill`                 |
| Localização         | `bi-geo-alt-fill`             |
| Carga horária       | `bi-clock-fill`               |
| Remuneração         | `bi-cash-stack`               |
| Calendário/prazo    | `bi-calendar3`                |
| Seta direita        | `bi-arrow-right`              |
| Sucesso             | `bi-check-circle-fill`        |
| Aviso               | `bi-exclamation-triangle-fill` |
| Erro                | `bi-x-circle-fill`            |
| Limpar              | `bi-x-circle`                 |

### Tamanho dos ícones na sidebar

```css
.sidebar .nav-link i {
  font-size: 1rem;
  width: 18px;
  text-align: center;
}
```

---

## 19. Padrões de Página

### Listagem pública de vagas

```
Hero (gradiente verde)
  ├── col-lg-6: Título h1 + contador de vagas
  └── col-lg-6: Barra de busca pill (backdrop-filter)

container-xl > row g-4
  ├── col-lg-3: Painel de filtros
  │   └── card com header verde + selects (Área, Tipo, Modalidade, Curso)
  │       + Botão "Aplicar filtros" + link "Limpar filtros" (condicional)
  └── col-lg-9: Lista de vagas
      ├── Paginação (topo, direita)
      ├── @forelse .card-vaga (horizontal)
      └── Paginação (base, direita)
```

**Estado vazio:**
```html
<div class="text-center py-5">
  <i class="bi bi-search" style="font-size:3rem;color:#CACACA;"></i>
  <h5 style="color:#6C757D;">Nenhuma vaga encontrada</h5>
  <p style="color:#CACACA;font-size:0.9rem;">Tente outros filtros ou volte em breve.</p>
  <a href="..." class="btn btn-outline-principal mt-2">Limpar filtros</a>
</div>
```

### Dashboard interno (coordenador / gestor)

```
page-body
  ├── row: stat-cards (4 colunas)
  └── card-interno: tabela de vagas recentes (.table-vagas)
```

### Formulário de vaga

```
page-body
  └── row g-4
      ├── col-lg-8: Seções do formulário
      │   ├── card-interno: Identificação (título, descrição, requisitos, benefícios)
      │   ├── card-interno: Dados do projeto (nome, código)
      │   └── card-interno: Condições (tipo, modalidade, carga horária, remuneração, datas)
      └── col-lg-4: Sidebar
          └── card-interno: Classificação + Botões de ação (Salvar rascunho / Publicar)
```

### Detalhe de vaga (público)

```
Hero (gradiente + breadcrumb + título da vaga + badges)

container-xl
  └── row
      ├── col-lg-8: Descrição, requisitos, benefícios (white-space: pre-line)
      └── col-lg-4: Card de candidatura (prazo, metadados, CTA principal)
```

---

## Utilitários CSS disponíveis

| Classe              | Efeito                        |
|---------------------|-------------------------------|
| `.text-principal`   | `color: #0D9571`              |
| `.text-cinza`       | `color: #6C757D`              |
| `.bg-principal`     | `background: #0D9571`         |
| `.bg-principal2`    | `background: #074635`         |

---

*Última atualização: maio de 2026*
