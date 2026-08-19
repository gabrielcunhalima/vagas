## 1. Componente Button

- [x] 1.1 Em `resources/js/Components/ui/button.jsx`, detectar via `React.Children` se `children` contém exatamente um elemento com prop `data-icon` (`inline-start` ou `inline-end`) junto de outro conteúdo (texto)
- [x] 1.2 Quando detectado, renderizar um clone invisível desse ícone (`aria-hidden="true"`, `pointer-events-none`, mesma classe/tamanho) do lado oposto do texto, mantendo o ícone real e o texto na ordem atual
- [x] 1.3 Não alterar a renderização quando não houver ícone, quando houver ícone nos dois lados (`inline-start` e `inline-end` simultâneos), ou em botões só-ícone (`size="icon*"`, sem texto)
- [x] 1.4 Remover de `buttonVariants` as classes `has-data-[icon=inline-end]:pr-2` e `has-data-[icon=inline-start]:pl-2` (todos os tamanhos: `default`, `xs`, `sm`, `lg`), deixando o padding simétrico
- [x] 1.5 Tratar o caso `asChild`: quando `Button` recebe `asChild` com um único filho elemento (`<a>`/`<Link>`), aplicar a mesma detecção/espelhagem nos filhos *desse* elemento (onde o ícone e o texto realmente estão), clonando-o com o novo conteúdo

## 2. Verificação visual

- [x] 2.1 Conferir o botão "Salvar alterações" (`Candidato/Perfil/Edit.jsx`) com texto centralizado, inclusive durante o estado de envio (ícone trocado para `Loader2`)
- [x] 2.2 Conferir botões com ícone em outras telas representativas (ex.: `Coord/Vagas/Form.jsx`, diálogos com "Cancelar"/"Remover") nos tamanhos usados (`default`, `sm`, `lg`)
- [x] 2.3 Conferir que botões só-texto (sem ícone) continuam com a mesma aparência de antes
- [x] 2.4 Conferir tema claro e escuro
- [x] 2.5 Rodar `openspec validate centralizar-texto-botoes --strict` e corrigir eventuais pendências
