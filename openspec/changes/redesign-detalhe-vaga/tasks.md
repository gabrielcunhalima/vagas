## 1. Helpers de formatação (`resources/js/lib/format.js`)

- [ ] 1.1 Implementar `blocosDeTexto(texto)` retornando `{ tipo: 'lista' | 'paragrafos', itens: string[] }` — `lista` quando ≥2 linhas começam com marcador (`-`, `*`, `•`, `–`, `—`, `1.`, `1)`), com marcadores removidos e linhas vazias descartadas; caso contrário `paragrafos`, quebrando em linhas em branco (design.md — Decisão 2)
- [ ] 1.2 Cobrir os casos de borda de `blocosDeTexto`: texto vazio/`null` (retorna lista de itens vazia), uma única linha, linhas com marcador misto, texto com blocos separados por linha em branco sem marcador
- [ ] 1.3 Implementar `partesEndereco(vaga)` retornando as linhas montadas (`logradouro, numero — complemento` · `bairro` · `cidade/estado` · `CEP`), omitindo partes vazias sem deixar separadores soltos (design.md — Decisão 3)
- [ ] 1.4 Fazer `partesEndereco` retornar `[vaga.endereco_completo]` quando nenhuma parte individual existir e o accessor vier preenchido, e lista vazia quando não houver nada

## 2. Cabeçalho e fatos rápidos

- [ ] 2.1 Manter o link de retorno "Todas as vagas" e o cabeçalho (selos `NovaBadge`/`TipoBadge`/`ModalidadeBadge`, título, linha área + local), renderizando o projeto apenas quando `projeto_nome` existir
- [ ] 2.2 Criar o sub-componente local `FatoRapido` ({ icon, label, children }) com ícone `lucide`, rótulo `text-muted-foreground` e valor `font-semibold`
- [ ] 2.3 Montar o bloco de fatos rápidos em largura total, **antes** do grid de duas colunas, com `grid-cols-2 sm:grid-cols-4` e superfície no padrão do `VagaCard` (`bg-card/55` + `backdrop-blur` + `shadow-lg shadow-black/[0.07]`, sem filete lateral)
- [ ] 2.4 Preencher os fatos: remuneração via `faixaSalarial` (cai para "A combinar"), carga horária (omitida se ausente), modalidade/local via `localVaga`, prazo via `data_encerramento`
- [ ] 2.5 Verificar que o grid se acomoda quando um fato é omitido, sem célula vazia nem rótulo órfão

## 3. Coluna de conteúdo

- [ ] 3.1 Reescrever o sub-componente `Secao` para aceitar ícone e renderizar via `blocosDeTexto`: `<ul>` com marcadores próprios para `lista`, `<p>` com `whitespace-pre-line` para `paragrafos`
- [ ] 3.2 Mover o bloco de cursos desejados (`curso_desejado` em chips `Badge variant="secondary"`) para o topo da coluna de conteúdo, omitindo-o quando o array estiver vazio ou ausente
- [ ] 3.3 Renderizar as seções na ordem "Sobre a vaga" → "Requisitos" → "Diferenciais" → "Benefícios", omitindo integralmente (título incluído) as de campo vazio
- [ ] 3.4 Criar o bloco de local de trabalho consumindo `partesEndereco`, com uma linha por parte; não renderizar nada quando `modalidade === 'remoto'` ou quando a lista vier vazia
- [ ] 3.5 Revisar o espaçamento/separação entre seções para manter hierarquia legível sem o `divide-y` genérico atual

## 4. Painel de candidatura, compartilhamento e CTA mobile

- [ ] 4.1 Remover `MetaLinha` e as linhas de remuneração/carga/local do aside — os fatos passam a ter dono único no bloco da seção 2
- [ ] 4.2 Manter no aside o indicador de prazo com urgência (`diasRestantes(...) <= 5` → tratamento `destructive` com contagem de dias; caso contrário data de encerramento em tratamento neutro) e o botão "Candidatar-se agora" apontando para `route('inscricao.create', vaga.id)`
- [ ] 4.3 Manter o bloco de compartilhamento (copiar link com toast de sucesso/erro, WhatsApp com título + URL) e o link de alerta de vagas
- [ ] 4.4 Confirmar que o aside permanece `lg:sticky lg:top-20` durante a rolagem do conteúdo em tela larga
- [ ] 4.5 Adicionar a barra de ação fixa em `lg:hidden` (`bg-background/90 backdrop-blur`, borda superior, botão "Candidatar-se") e o `padding-bottom` compensatório na página para nada ficar encoberto

## 5. Vagas relacionadas e conformidade visual

- [ ] 5.1 Manter a seção de vagas relacionadas com `VagaCard`, omitindo seção e título quando a lista vier vazia
- [ ] 5.2 Revisar o arquivo contra as regras do DESIGN_SYSTEM: sem `hover:-translate-y-*` (8), tipo de vaga sem ícone próprio (9), sem filete/borda lateral colorida (10), só tokens de cor (4), ícones só de `lucide-react` (3)
- [ ] 5.3 Confirmar que nenhuma prop nova é consumida e que nenhuma requisição extra ao servidor foi introduzida — apenas os campos de `vagaCompleta()` e `relacionadas`

## 6. Verificação e documentação

- [ ] 6.1 Rodar `npm run build` e garantir build limpo, sem warnings novos
- [ ] 6.2 Verificar manualmente a página em tema claro e escuro, em largura mobile e desktop
- [ ] 6.3 Verificar com uma vaga de dados completos e com uma vaga mínima (sem benefícios, sem desejáveis, sem cursos, sem endereço) e com uma vaga remota — nenhum rótulo órfão ou bloco vazio em qualquer caso
- [ ] 6.4 Verificar uma vaga com requisitos escritos em lista com hífen e outra com descrição em parágrafo corrido, confirmando a renderização esperada de cada uma
- [ ] 6.5 Atualizar a seção 8 do `DESIGN_SYSTEM.md` ("Detalhe de vaga") descrevendo o novo padrão: faixa de fatos rápidos em largura total, cursos no topo do conteúdo, aside só com prazo/CTA/share e barra de ação fixa no mobile
