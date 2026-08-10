## 1. Largura 10-80-10 compartilhada

- [x] 1.1 Criar a constante da largura 10-80-10 em `resources/js/lib/` (valor atual: `mx-auto w-full px-4 lg:w-4/5 lg:px-0`), com comentário curto explicando que é a largura das páginas públicas largas
- [x] 1.2 Substituir a constante local `CONTAINER` de [Index.jsx:22](resources/js/Pages/Publico/Vagas/Index.jsx#L22) pela importação da constante nova, sem alterar nenhuma das classes aplicadas
- [x] 1.3 Conferir na listagem que hero, filtros, lista e detalhe continuam na mesma largura e nas mesmas quebras de antes

## 2. Estrutura de duas áreas em Alertas.jsx

- [x] 2.1 Trocar o wrapper `max-w-2xl` de [Alertas.jsx:60](resources/js/Pages/Publico/Alertas.jsx#L60) pela constante de largura importada
- [x] 2.2 Transformar o `<form>` no grid de duas colunas: `grid gap-x-10 gap-y-8 xl:grid-cols-[320px_minmax(0,1fr)] xl:grid-rows-[auto_1fr]`, mantendo o card (`rounded-xl bg-card p-6 ring-1 ring-foreground/10`) como superfície única
- [x] 2.3 Montar o bloco **A** (`xl:col-start-1 xl:row-start-1`) com o h1, a descrição da página e o campo de e-mail — movendo o cabeçalho que hoje fica acima do card para dentro dele
- [x] 2.4 Montar o bloco **B** (`xl:col-start-2 xl:row-start-1 xl:row-span-2`) com os três grupos de preferência
- [x] 2.5 Montar o bloco **C** (`xl:col-start-1 xl:row-start-2`) com o consentimento LGPD e o botão "Ativar alertas", envolvendo seu conteúdo num `div` com `xl:sticky xl:top-20`
- [x] 2.6 Garantir que a ordem dos blocos no JSX seja A → B → C e comentar que essa ordem é o que dá o empilhamento correto abaixo de `xl`

## 3. Distribuição das preferências

- [x] 3.1 Passar `colunas="sm:grid-cols-2 xl:grid-cols-3"` ao grupo de áreas de interesse
- [x] 3.2 Agrupar "Tipos de vaga" e "Modalidades" num `grid gap-8 sm:grid-cols-2`, cada um mantendo `sm:grid-cols-3` internamente
- [x] 3.3 Aplicar `xl:border-l xl:pl-10` ao bloco B como divisor entre as duas áreas, e ajustar o `gap-x` se o filete ficar encostado no conteúdo
- [x] 3.4 Remover a borda superior (`border-t pt-5`) do bloco de consentimento, que separava seções numa pilha que deixa de existir em `xl` — ou mantê-la apenas abaixo de `xl`
- [x] 3.5 Separar "Tipos de vaga" e "Modalidades" com um filete vertical quando estiverem lado a lado (`sm:border-l sm:pl-8` no segundo grupo, via nova prop `className` do `GrupoCheckbox`)

## 4. Verificação de comportamento

- [ ] 4.1 Em janela larga (≥1280px): as duas áreas aparecem lado a lado, e-mail/LGPD/botão à esquerda e os três grupos à direita, sem sobreposição
- [ ] 4.2 Em janela larga: rolar as preferências mantém consentimento e botão visíveis e clicáveis
- [x] 4.3 Em janela estreita: a ordem lida de cima para baixo é título e e-mail → preferências → consentimento → botão, em largura total e sem rolagem horizontal
- [x] 4.4 Tabular com o teclado do campo de e-mail em diante percorre as preferências antes de chegar ao consentimento e ao botão, nas duas larguras
- [ ] 4.5 Redimensionar a janela cruzando o ponto de quebra `xl` não perde nem sobrepõe conteúdo, e nenhum rótulo de opção fica truncado
- [x] 4.6 Enviar sem consentimento e sem e-mail: as mensagens de erro aparecem junto dos campos correspondentes nas duas larguras
- [x] 4.7 Enviar sem marcar nenhuma área de interesse cria o alerta cobrindo todas as áreas, como antes
- [x] 4.8 Abrir a página com o e-mail já conhecido: o campo vem preenchido e não editável
- [x] 4.9 Navegar da listagem para `/alertas` em janela larga: as duas páginas apresentam o conteúdo na mesma largura, sem salto lateral
- [x] 4.10 Conferir cabeçalho e rodapé na largura máxima de sempre, e que nenhuma outra página pública mudou de largura
- [x] 4.11 Rodar `npm run build` e confirmar que não há erro

## 5. Documentação

- [x] 5.1 Ajustar a linha "Listagem pública (split view)" de [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md#L152): a listagem deixa de ser a "única página em 10-80-10"
- [x] 5.2 Acrescentar em "Padrões de página" a entrada da página de alertas: largura 10-80-10, grid aside (e-mail, LGPD, envio sticky) + preferências, e empilhamento abaixo de `xl`

## Notas de verificação

**4.1, 4.2 e 4.5 continuam abertas**: são checagens que exigem olhar a página renderizada em janela larga e redimensionada. Não há Playwright, Puppeteer nem Cypress instalados no projeto, e o Inertia renderiza no cliente — buscar o HTML da rota devolve só o JSON de props, não o layout. Precisam de conferência manual no navegador.

Como as demais foram verificadas:

- **1.3, 4.9, 4.10** — a constante extraída tem string idêntica à anterior; `git diff` de `Index.jsx` mostra só a troca da declaração local pelo import. `grep` confirma que apenas `Index.jsx` e `Alertas.jsx` consomem `CONTAINER_LARGO`, e `PublicLayout.jsx` (navbar/footer em `max-w-6xl`) não foi tocado.
- **4.3, 4.4** — a ordem no JSX é A (h1 + e-mail) → B (preferências) → C (LGPD + envio), e abaixo de `xl` o grid é de coluna única, então ordem visual = ordem do DOM = ordem de tabulação. Nenhum `order-*` nem `tabindex` no arquivo.
- **4.6** — `errors.email` é renderizado pelo `Field` junto do input (bloco A) e `errors.lgpd_consentimento` logo abaixo do rótulo de consentimento (bloco C); nenhum dos dois mudou de bloco. Teste `criar alerta sem email falha` passa.
- **4.7** — teste `cria alerta sem filtros` passa.
- **4.8** — `disabled={emailFixo}` inalterado.
- **4.11** — `npm run build` conclui sem erro (745ms).
- **Suíte** — `php artisan test --filter=AlertaVaga`: 25 passaram, 44 asserções.
