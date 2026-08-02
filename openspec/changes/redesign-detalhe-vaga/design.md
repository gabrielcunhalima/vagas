## Context

Ver [proposal.md](proposal.md) — Why, para a motivação.

Estado atual relevante ([Show.jsx](resources/js/Pages/Publico/Vagas/Show.jsx), 185 linhas):

- Grid `lg:grid-cols-[1fr_330px]`: coluna de conteúdo com `divide-y` + aside sticky.
- Dois sub-componentes locais: `Secao` (título uppercase muted + `whitespace-pre-line`) e `MetaLinha` (ícone + rótulo + valor, usado só no aside).
- O aside é a **única** fonte de remuneração e carga horária. Como ele fica depois do conteúdo no fluxo do DOM em telas estreitas, no mobile esses dados só aparecem depois de rolar todo o texto da vaga — e o CTA junto.
- `localVaga(vaga)` aparece duas vezes (cabeçalho e aside).
- `vaga.endereco_completo` é um accessor do model impresso como string única.
- `curso_desejado` é um array e hoje é o último bloco do conteúdo.

Restrições herdadas do [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) que moldam o desenho: sem `hover:-translate-y-*` (regra 8), tipo de vaga só por cor e nunca por ícone (regra 9), filete/borda lateral colorida proibido (regra 10), só tokens de cor (regra 4), ícones só de `lucide-react` (regra 3). O `VagaCard` já estabelece a linguagem visual da tela pública — superfície `bg-card/55` com `backdrop-blur`, `shadow-lg shadow-black/[0.07]`, wash de gradiente na cor do tipo — e o detalhe deve conversar com ela.

Props disponíveis (fixas, ver `vagaCompleta()` em [VagaPublicaController.php](app/Http/Controllers/Vagas/VagaPublicaController.php)): `id, titulo, descricao, tipo, area, modalidade, remuneracao, remuneracao_max, carga_horaria, cidade, estado, local_trabalho, data_encerramento, autorizada_em, created_at, requisitos, requisitos_desejaveis, beneficios, curso_desejado, cep, logradouro, numero, complemento, bairro, pais, projeto_nome, projeto_codigo, endereco_completo`.

## Goals / Non-Goals

**Goals:**

- Levar remuneração, jornada, local e prazo para acima da dobra em **todas** as larguras, sem depender do aside.
- Eliminar a duplicação de informação entre cabeçalho, faixa de fatos e aside — cada dado tem um dono na página.
- Dar hierarquia ao conteúdo textual e melhorar a legibilidade do texto livre vindo do banco, sem introduzir renderizador de markdown nem HTML do usuário.
- Manter o arquivo da página legível: composição no `Show.jsx`, lógica pura em `lib/format.js`.

**Non-Goals:**

- Criar um design system paralelo. Tudo sai de tokens e componentes `ui/` existentes.
- Reutilizar os novos blocos em outras telas (Gestor/Coord) nesta change — a extração para `Components/` fica para quando houver segundo consumidor.
- Sanitizar/parsear HTML: os campos de texto continuam sendo tratados como texto puro.

## Decisions

### 1. Faixa de fatos rápidos como bloco próprio, e o aside deixa de repetir

Um bloco de fatos rápidos entra logo abaixo do cabeçalho, na largura total do container, **antes** do grid de duas colunas. Grid responsivo (`grid-cols-2 sm:grid-cols-4`), cada item com ícone `lucide`, rótulo em `text-muted-foreground` e valor em `font-semibold`. Superfície igual à do `VagaCard` (`bg-card/55` + `backdrop-blur` + `shadow-lg shadow-black/[0.07]`), sem borda lateral colorida.

Em contrapartida, o aside **perde** as `MetaLinha` de remuneração/carga/local e fica com prazo + CTA + compartilhamento. `MetaLinha` sai do arquivo.

*Por quê:* resolve os dois problemas de uma vez — o dado crítico sobe no mobile (o bloco vem antes do conteúdo no DOM) e a duplicação some, porque só existe um lugar onde cada fato mora. 

*Alternativas consideradas:* (a) mover o aside para antes do conteúdo no mobile via `order-*` — mantém a duplicação e empurra todo o painel (incluindo CTA e share) para o meio da leitura; (b) repetir os fatos nos dois lugares com CSS escondendo um deles — duplicação de markup e risco de divergirem.

### 2. Renderização de texto livre: heurística de duas regras, sem markdown

Helper puro `blocosDeTexto(texto)` em `lib/format.js` devolve `{ tipo: 'lista' | 'paragrafos', itens: string[] }`:

1. Se **2 ou mais linhas** começam com marcador (`-`, `*`, `•`, `–`, `—`, ou `1.` / `1)`) → `lista`, com os marcadores removidos e linhas vazias descartadas.
2. Caso contrário → `paragrafos`, quebrando em linhas em branco; quebras simples dentro de um parágrafo continuam preservadas por `whitespace-pre-line`.

A seção renderiza `<ul>` com marcadores próprios no caso 1 e `<p>` no caso 2.

*Por quê:* o campo é preenchido por coordenadores em `<textarea>`, então lista com hífen é o padrão real de escrita, mas nada garante isso. A regra exige evidência (≥2 marcadores) para converter em lista e, na dúvida, preserva o texto como está — nunca inventa estrutura.

*Alternativas consideradas:* (a) `react-markdown`/`marked` — nova dependência, superfície de XSS e comportamento imprevisível sobre texto que não é markdown (um `_` ou `#` solto viraria formatação); (b) manter `whitespace-pre-line` puro — é exatamente o problema que a change existe para resolver; (c) tratar toda linha isolada como item de lista — converteria dois parágrafos curtos em bullets falsos.

### 3. Endereço montado por partes, com `endereco_completo` como fallback

Helper puro `partesEndereco(vaga)` em `lib/format.js` devolve uma lista de linhas já montadas, pulando o que estiver vazio:

- linha 1: `logradouro, numero — complemento`
- linha 2: `bairro`
- linha 3: `cidade/estado`
- linha 4: `CEP 00000-000`

Se nenhuma parte existir mas `endereco_completo` vier preenchido, renderiza essa string como linha única. Se a modalidade for `remoto`, o bloco inteiro não é renderizado.

*Por quê:* as partes já vêm nas props e produzem um endereço escaneável; o fallback evita regressão para vagas antigas cujo endereço só exista no accessor.

*Alternativa considerada:* pedir ao back-end um array estruturado — mudaria o contrato, que a proposta explicitamente congela.

### 4. Cursos desejados viram bloco de elegibilidade no topo do conteúdo

`curso_desejado` sai do fim e vira o primeiro bloco da coluna de conteúdo, com título próprio e chips (`Badge variant="secondary"`).

*Por quê:* é o critério de corte mais objetivo da vaga — quem não tem o curso pode parar de ler ali. É informação de triagem, não de detalhe.

*Alternativa considerada:* colocar na faixa de fatos rápidos — a lista é de tamanho variável e quebraria o grid de quatro colunas fixas.

### 5. Barra de ação fixa no mobile

Em `lg:hidden`, uma barra `sticky bottom-0` (ou `fixed inset-x-0 bottom-0`) com fundo `bg-background/90 backdrop-blur`, borda superior e o botão "Candidatar-se". A página ganha `pb-*` equivalente à altura da barra para o conteúdo não ficar encoberto. No desktop nada muda: o aside sticky já cumpre o papel.

*Por quê:* o requisito é o CTA acessível durante toda a leitura; no mobile o aside cai para o fim da página e deixa o candidato sem ação por toda a rolagem.

*Alternativa considerada:* CTA repetido ao fim do conteúdo — resolve só o fim da leitura, não o meio.

### 6. Sub-componentes permanecem locais ao `Show.jsx`

`Secao` (agora com ícone e renderização por bloco), `FatoRapido`, `Elegibilidade`, `LocalTrabalho` e `PainelCandidatura` ficam no próprio arquivo da página. Só os helpers puros (`blocosDeTexto`, `partesEndereco`) vão para `lib/format.js`.

*Por quê:* a página é o único consumidor. `Components/` no projeto guarda o que é compartilhado (`VagaCard`, `badges`, `Pagination`); promover cedo cria acoplamento sem ganho. Os helpers vão para `lib/` porque são lógica pura e testável, e `format.js` já é o lugar canônico disso.

## Risks / Trade-offs

- **Heurística de lista converte texto que não era lista** → a regra exige ≥2 linhas com marcador explícito; sem isso o texto cai no caminho de parágrafos, que é o comportamento atual. O pior caso é "não melhorou", nunca "corrompeu".
- **Barra fixa no mobile rouba área útil em telas baixas** → altura enxuta (um botão, ~64px) e `padding-bottom` compensatório na página; nenhum conteúdo fica inacessível.
- **Remover os fatos do aside pode parecer "perda" no desktop** → a informação continua acima da dobra na faixa de fatos, que no desktop fica na largura total, mais visível do que estava na coluna de 330px.
- **Faixa de quatro colunas com fatos ausentes fica desbalanceada** → os itens são renderizados condicionalmente em um grid que se acomoda a 2, 3 ou 4 itens; remuneração e prazo existem sempre (remuneração cai para "A combinar"), então o mínimo real é 3.
- **Regressão visual não coberta por teste automatizado** → o projeto não tem testes de front; a verificação é manual, nos dois temas e em duas larguras, com vagas de dados completos e mínimos.

## Migration Plan

Mudança puramente de apresentação no front-end. Sem migração de dados, sem alteração de rota, contrato ou banco.

1. Implementar helpers em `lib/format.js` (aditivo — nada existente muda de assinatura).
2. Reescrever a composição de `Show.jsx`.
3. `npm run build` e verificação manual: tema claro/escuro × mobile/desktop × vaga completa/vaga mínima (sem benefícios, sem desejáveis, sem cursos, sem endereço, remota).
4. Atualizar a seção 8 do `DESIGN_SYSTEM.md`.

**Rollback:** reverter o commit — como nenhuma outra tela consome os novos helpers, o revert é isolado a `Show.jsx` e `format.js`.

## Open Questions

- `endereco_completo` continua sendo enviado pelo controller e passa a ser usado apenas como fallback. Se depois da implantação nenhuma vaga ativa depender dele, o campo pode sair do payload em uma limpeza posterior — decisão que não afeta specs, abordagem nem tarefas desta change.
