## Why

A página pública de detalhe da vaga (`/vagas/{id}`) hoje despeja os dados do back-end em blocos de texto corrido praticamente sem hierarquia: descrição, requisitos, diferenciais e benefícios aparecem todos com o mesmo peso visual, o endereço é uma string única, os cursos desejados ficam soltos no fim do conteúdo e o painel lateral repete informação que já está no cabeçalho (local aparece duas vezes). O candidato precisa ler tudo para descobrir se a vaga serve para ele.

Essa é a tela que decide a candidatura — é onde o tráfego da listagem e dos e-mails de alerta desemboca. Ela precisa entregar em segundos as respostas de triagem ("quanto paga?", "onde é?", "quantas horas?", "até quando posso me inscrever?", "meu curso está na lista?") e só depois o texto longo.

## What Changes

- **Cabeçalho reestruturado**: badges + título + linha de identificação (área, local, projeto) passam a conviver com uma faixa de "fatos rápidos" (remuneração, carga horária, modalidade/local, prazo) logo abaixo do título, visível sem rolagem e sem depender do aside — que hoje é a única fonte desses dados e some no mobile.
- **Seções de conteúdo com hierarquia real**: cada bloco (Sobre a vaga, Requisitos, Diferenciais, Benefícios) ganha ícone, título legível e espaçamento consistente; o texto `whitespace-pre-line` vindo do banco passa a ser renderizado como lista quando o conteúdo já vem em linhas/marcadores, em vez de um parágrafo único.
- **Cursos desejados promovidos**: saem do fim do conteúdo e viram um bloco próprio de destaque (chips), já que são o critério de elegibilidade mais objetivo da vaga.
- **Local de trabalho estruturado**: em vez de imprimir `endereco_completo` como uma linha só, a página quebra o endereço em partes legíveis (logradouro/número/complemento, bairro, cidade/estado, CEP) e omite o bloco quando a vaga é remota.
- **Painel de candidatura reorganizado**: deixa de duplicar o que já está nos fatos rápidos e passa a concentrar prazo (com urgência visual), CTA principal e compartilhamento; no mobile vira uma barra de ação fixa para o CTA não ficar inacessível.
- **Estados de dado ausente tratados**: campos opcionais vazios (`remuneracao`, `carga_horaria`, `beneficios`, `requisitos_desejaveis`, `projeto_nome`) não deixam mais rótulos órfãos nem blocos vazios.
- **Nenhuma mudança no back-end**: o controller continua enviando exatamente as mesmas props (`vaga` com os campos de `vagaCompleta()` e `relacionadas`). Toda a mudança é de apresentação no `Pages/Publico/Vagas/Show.jsx` e componentes de apoio.

Não é escopo: alterar o fluxo de inscrição, o `VagaCard` da listagem, o layout público (navbar/footer) ou qualquer campo novo no banco.

## Capabilities

### New Capabilities

- `vaga-detalhe-publica`: apresentação pública da página de detalhe de uma vaga — quais informações aparecem, em que ordem/agrupamento, como o candidato acessa a candidatura e o compartilhamento, e como a página se comporta com campos opcionais ausentes e em telas pequenas.

### Modified Capabilities

Nenhuma — não há specs em `openspec/specs/` ainda; esta é a primeira capability do repositório.

## Impact

- **Código alterado**: [Show.jsx](resources/js/Pages/Publico/Vagas/Show.jsx) (reescrita da composição da página).
- **Código possivelmente criado**: componentes de apoio em [resources/js/Components/](resources/js/Components/) para fatos rápidos, seção de conteúdo com ícone e endereço estruturado; helpers de formatação em [format.js](resources/js/lib/format.js) (ex.: quebra de texto em lista, montagem de endereço por partes).
- **Back-end**: inalterado — [VagaPublicaController::show()](app/Http/Controllers/Vagas/VagaPublicaController.php) e `vagaCompleta()` permanecem como estão.
- **Documentação**: seção 8 do [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) ("Detalhe de vaga") precisa ser atualizada para refletir o novo padrão de página.
- **Restrições de design herdadas**: valem as regras 8, 9 e 10 do DESIGN_SYSTEM (sem hover-lift, tipo de vaga só por cor, sem filete lateral colorido), tokens de cor apenas, ícones só `lucide-react`.
