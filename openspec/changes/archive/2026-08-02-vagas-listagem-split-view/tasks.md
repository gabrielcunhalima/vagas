## 1. Back-end: campos de detalhe na listagem

- [x] 1.1 Em [VagaPublicaController::index()](app/Http/Controllers/Vagas/VagaPublicaController.php), trocar o `->through(fn(Vaga $v) => $this->vagaResumo($v))` do paginator por `vagaCompleta($v)` (design.md — Decisão 3)
- [x] 1.2 Confirmar que `show()` e o mapeamento de `relacionadas` continuam usando `vagaCompleta()`/`vagaResumo()` como hoje, sem alteração
- [x] 1.3 Conferir no payload da listagem que `projeto_nome`, `requisitos`, `requisitos_desejaveis`, `beneficios`, `curso_desejado` e `endereco_completo` chegam preenchidos e que `observacoes_internas` não aparece

## 2. Item de lista compacto (`components/VagaListaItem.jsx`)

- [x] 2.1 Criar o componente recebendo `{ vaga, selecionada, onSelect }` e renderizando um `<button type="button">` `w-full text-left`, sem `<Link>` e sem navegação
- [x] 2.2 Exibir exclusivamente título do cargo, `localVaga(vaga)`, projeto e prazo — nada de descrição, remuneração, carga horária, modalidade ou área (spec — Item de lista compacto e contínuo)
- [x] 2.3 Omitir o projeto por completo quando `projeto_nome` for nulo/vazio, sem rótulo órfão nem espaço reservado
- [x] 2.4 Aplicar o prazo com o mesmo critério de urgência do `VagaCard`: `diasRestantes(...) <= 5` → contagem em `text-destructive font-semibold`; caso contrário `Inscrições até {formatDate(...)}` em `text-muted-foreground`
- [x] 2.5 Estado selecionado: `bg-accent` + título em `text-primary font-semibold`; hover só `bg-muted/60` — **sem filete/barra lateral colorida** (regra 10) e **sem translate no hover** (regra 8)
- [x] 2.6 Garantir foco visível pelo teclado (`focus-visible:` com token de ring) e `aria-current` no item selecionado

## 3. Painel de detalhe (`components/VagaDetalhePainel.jsx`)

- [x] 3.1 Criar o componente recebendo `{ vaga }` e retornando `null` quando não houver vaga
- [x] 3.2 Montar o cabeçalho interno `sticky top-0` com fundo opaco: `TipoBadge` + `ModalidadeBadge` (+ `NovaBadge` quando `isNova`), título, linha de área/local/projeto e o botão **Candidatar-se** apontando para `route('inscricao.create', vaga.id)` (design.md — Decisão 6)
- [x] 3.3 Montar o resumo rápido em grade: remuneração via `faixaSalarial` (cai para "A combinar"), carga horária, modalidade e prazo — cada fato omitido quando o campo não existir, sem célula vazia
- [x] 3.4 Renderizar as seções de texto na ordem Sobre a vaga (`descricao`) → Requisitos → Diferenciais (`requisitos_desejaveis`) → Benefícios, omitindo integralmente (título incluído) as de campo vazio
- [x] 3.5 Renderizar `curso_desejado` como chips `Badge variant="secondary"`, omitindo a seção quando o array vier vazio ou ausente
- [x] 3.6 Renderizar o local de trabalho a partir de `endereco_completo`, omitindo a seção quando `modalidade === 'remoto'` ou quando o valor vier vazio
- [x] 3.7 Limitar a largura do conteúdo textual do painel (`max-w-4xl`) para não gerar linhas longas demais em monitor ultralargo (design.md — Risks)
- [x] 3.8 Revisar contra o DESIGN_SYSTEM: só tokens de cor (4), ícones só `lucide-react` (3), tipo de vaga sem ícone próprio (9)

## 4. Reescrita da listagem (`Pages/Publico/Vagas/Index.jsx`)

- [x] 4.1 Trocar os containers `mx-auto w-full max-w-6xl px-4` do hero e da área de conteúdo por `mx-auto w-full px-4 lg:w-4/5 lg:px-0`, sem tocar em `PublicLayout` (design.md — Decisão 1)
- [x] 4.2 Introduzir o estado de seleção derivado: `useState(null)` para o id e `vagas.data.find(v => v.id === selecionadaId) ?? vagas.data[0] ?? null` para a vaga — sem `useEffect` de sincronização (design.md — Decisão 4)
- [x] 4.3 Substituir o grid por `lg:grid-cols-[240px_1fr] xl:grid-cols-[240px_minmax(340px,420px)_1fr]`, mantendo a coluna de filtros sticky como está
- [x] 4.4 Trocar a renderização de `VagaCard` pela lista contínua: container `rounded-xl bg-card ring-1 ring-foreground/10 divide-y overflow-hidden` com um `VagaListaItem` por vaga, sem `gap` entre itens
- [x] 4.5 Manter acima da lista o contador de vagas encontradas e o select de ordenação, e abaixo dela o `Pagination`
- [x] 4.6 Renderizar `VagaDetalhePainel` na terceira coluna com `hidden xl:block`, `xl:sticky xl:top-20`, `max-h-[calc(100dvh-6rem)] overflow-y-auto`
- [x] 4.7 Renderizar o `Sheet side="bottom"` (~92dvh) com o mesmo `VagaDetalhePainel`, montado apenas abaixo de `xl`, abrindo ao selecionar e fechando por gesto/`Esc`/botão/clique fora (design.md — Decisão 7)
- [x] 4.8 Manter o estado vazio atual (mensagem + limpar filtros + criar alerta) no lugar da lista, sem renderizar painel nem `Sheet` quando não houver resultados
- [x] 4.9 Remover do arquivo o import de `VagaCard` e qualquer referência a `route('vagas.publicas.show', ...)`

## 5. Preservação de `Show.jsx` e da rota

- [x] 5.1 Confirmar que [Show.jsx](resources/js/Pages/Publico/Vagas/Show.jsx), [VagaCard.jsx](resources/js/components/VagaCard.jsx) e a rota `vagas.publicas.show` em [web.php](routes/web.php) permanecem sem nenhuma alteração
- [x] 5.2 Verificar por busca no `resources/js/` que nenhuma referência a `vagas.publicas.show` sobrou **na listagem pública**. Referências fora dela são mantidas de propósito — a spec só proíbe a listagem de navegar para o detalhe: `VagaCard.jsx` ("Vagas relacionadas", dentro da própria `Show.jsx`), `Pages/Candidato/Candidaturas/Show.jsx` ("Ver vaga") e `Pages/Publico/Candidatura.jsx` ("Ver descrição completa da vaga")
- [x] 5.3 Acessar `/vagas/{id}` diretamente e confirmar que a página abre normalmente; acessar o id de uma vaga encerrada e confirmar que o comportamento é o mesmo de antes

## 6. Verificação

- [x] 6.1 Rodar `npm run build` e garantir build limpo, sem warnings novos
- [x] 6.2 Verificar em ≥1440px: 10% de margem de cada lado, três colunas, primeira vaga já selecionada e detalhe visível sem clique
- [x] 6.3 Verificar que cabeçalho, rodapé e as demais páginas públicas continuam na largura `max-w-6xl` de antes
- [x] 6.4 Verificar a seleção: clicar em um item troca só o painel (lista, filtros e rolagem intactos); o item selecionado é distinguível; a navegação e o acionamento por teclado funcionam com foco visível
- [x] 6.5 Verificar a reconciliação da seleção: aplicar um filtro que mantém a vaga selecionada no resultado (segue selecionada) e outro que a remove (passa para a primeira do novo resultado); trocar de página da paginação (passa para a primeira)
- [x] 6.6 Verificar a rolagem: com a lista longa, rolar a lista e confirmar que o painel continua visível; com um detalhe longo, rolar o painel e confirmar que a lista não é arrastada junto e que o botão Candidatar-se continua alcançável
- [x] 6.7 Verificar em ~390px e ~1100px: lista ocupa a tela, seleção abre o `Sheet` com o botão Candidatar-se acessível, fechar devolve a lista na mesma posição, filtros continuam alcançáveis
- [x] 6.8 Verificar com uma vaga de dados completos e uma vaga mínima (sem benefícios, sem desejáveis, sem cursos, sem carga horária, sem projeto, sem remuneração) e uma vaga remota — nenhum rótulo órfão nem bloco vazio na lista ou no painel
- [x] 6.9 Verificar o filtro sem resultados: estado vazio no lugar da lista, sem painel, com os filtros ainda visíveis e preenchidos
- [x] 6.10 Verificar tema claro e escuro, com o item selecionado no topo, no meio e no fim da lista (divisórias do `divide-y` adjacentes ao `bg-accent`)
- [x] 6.11 Clicar em Candidatar-se e confirmar que leva ao cadastro de currículo da vaga selecionada

## 7. Documentação

- [x] 7.1 Atualizar a seção 8 do [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) — "Listagem pública" passa a descrever o split view de três colunas em 10-80-10, itens colados com quatro campos e painel de detalhe com CTA
- [x] 7.2 Atualizar a tabela da seção 6 do `DESIGN_SYSTEM.md` com `VagaListaItem` e `VagaDetalhePainel`, e registrar que `VagaCard` passa a ser usado apenas em "Vagas relacionadas"
- [x] 7.3 Registrar na seção 8 que a página de detalhe em endereço próprio continua viva para acesso direto, mas sem ponto de entrada na interface
