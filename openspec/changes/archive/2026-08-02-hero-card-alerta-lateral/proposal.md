## Why

Na listagem pública, o CTA de alerta de vagas hoje fica **empilhado abaixo** do campo de busca, dentro da mesma coluna de texto (`max-w-xl`). Isso empurra a hero para baixo: em telas largas ela chega a ~24rem de padding vertical mais quatro blocos empilhados (título, contagem, busca, card), e a lista de vagas — o conteúdo que o candidato veio ver — só aparece depois de rolar.

Ao mesmo tempo, a metade direita da hero está vazia: o gradiente `brand-deep/80 → transparent` deixa a foto exposta e nenhum conteúdo ocupa aquele espaço em `lg+`. Mover o card para lá resolve os dois problemas de uma vez: o CTA ganha destaque próprio em vez de virar um rodapé do formulário, e a hero encolhe em altura porque deixa de empilhar.

## What Changes

- **Card de alerta vai para a direita da hero**: a partir de `lg`, a hero passa a ter duas colunas — à esquerda título, contagem de vagas e busca; à direita o card "Não perca nenhuma vaga" com o botão "Criar alerta de vagas". Abaixo de `lg` o card continua empilhado depois da busca, como hoje.
- **Hero mais baixa**: com o card fora da pilha vertical, o padding vertical da hero diminui (`py-16 lg:py-24` → valores menores), aproximando a lista de vagas da dobra.
- **Card ganha forma vertical em telas largas**: hoje o card é horizontal (ícone + texto à esquerda, botão à direita, `sm:flex-row`). Como coluna lateral ele fica vertical — ícone, título, descrição e botão em largura total — e volta ao formato horizontal atual nas larguras em que fica empilhado.
- **Largura da hero inalterada**: continua no container 10-80-10 (`lg:w-4/5`), igual à listagem abaixo. Nenhuma mudança de largura.
- **Sem mudança de conteúdo ou destino**: mesmos textos, mesmo ícone `Bell`, mesmo link para `route('alertas.create')`.

Não é escopo: alterar a foto/overlay da hero, a busca e seus filtros, o fluxo de criação de alerta, ou a hero de qualquer outra página pública.

## Capabilities

### New Capabilities

Nenhuma — a hero da listagem pertence à capability já existente.

### Modified Capabilities

- `vagas-listagem-publica`: passa a especificar a composição da hero — o CTA de alerta ocupa a área direita em telas largas e empilha abaixo da busca em telas estreitas, e a hero mantém a largura 10-80-10 já exigida enquanto reduz sua altura.

## Impact

- **Código alterado**: [Index.jsx](resources/js/Pages/Publico/Vagas/Index.jsx#L106-L168) — bloco da hero (grid de duas colunas, padding vertical, reflow do card).
- **Back-end**: inalterado — [VagaPublicaController](app/Http/Controllers/Vagas/VagaPublicaController.php) segue enviando as mesmas props (`vagas`, `filtros`, `total`).
- **Rotas**: inalteradas — `alertas.create` continua sendo o destino do CTA.
- **Documentação**: a linha "Hero público (home)" do [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md#L152) precisa registrar a hero em duas colunas.
- **Restrições de design herdadas**: sem hover-lift (regra 8 do DESIGN_SYSTEM), ícones apenas `lucide-react`, tokens de cor do tema.
