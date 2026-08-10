## 1. Navbar de desktop

- [x] 1.1 Remover o `<nav>` de atalhos "Vagas"/"Alertas" do `<header>` em `resources/js/Layouts/PublicLayout.jsx` (bloco entre o lockup e o grupo de ações de conta)
- [x] 1.2 Corrigir a classe da segunda linha do lockup de `text-foreground-vagas` para `text-foreground`
- [x] 1.3 Reconferir espaçamento/alinhamento do header (`gap-4` entre lockup e o grupo de ações à direita) após a remoção do `<nav>`, já que ele deixa de ocupar espaço central

## 2. Verificação visual

- [x] 2.1 Rodar o app localmente e conferir o header em `/` (home) nos temas claro e escuro, como visitante deslogado (vê logo, lockup, ThemeToggle, Entrar/Criar conta)
- [x] 2.2 Conferir o header autenticado (dropdown de conta no lugar de Entrar/Criar conta) em vez de atalhos Vagas/Alertas
- [x] 2.3 Conferir que o menu mobile (hambúrguer) continua exibindo Vagas, Alertas e as opções de conta sem alteração
- [x] 2.4 Conferir visualmente que a cor de "Vagas" no lockup segue a cor de texto principal (foreground) nos dois temas

## 3. Ajustes de acabamento (feedback do usuário sobre o resultado)

- [x] 3.1 Trocar o container do `<header>` de `max-w-6xl` para `CONTAINER_LARGO` (10-80-10), alinhando a navbar com a listagem de vagas e a página de alertas
- [x] 3.2 Trocar o lockup "Portal de Vagas" de duas linhas empilhadas para uma linha só (`flex items-baseline gap-1.5`), mantendo categoria muted + nome bold
- [x] 3.3 Aumentar os controles do lado direito da navbar (`ThemeToggle` para `icon-lg`/ícone `size-5`; dropdown de conta e botões "Entrar"/"Criar conta" para altura `h-10`)
- [x] 3.4 Adicionar `size`/`iconClassName` ao componente `ThemeToggle` para permitir o tamanho maior só na navbar pública, sem afetar `InternalLayout`/`AuthLayout`
- [x] 3.5 Atualizar DESIGN_SYSTEM.md (seção 4.1, regras 11-12) e a spec `navegacao-publica` para refletir o lockup em uma linha e a largura 10-80-10
- [x] 3.6 Verificar visualmente (claro/escuro, deslogado/logado) que os dois ajustes ficaram consistentes
