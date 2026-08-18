{{-- Região trocada via fetch pelos filtros (resources/js/vagas-filtro.js).
     Reaproveitada tanto na página cheia quanto no fragmento AJAX. Os dois
     divs de topo são os dois itens da grade 3 colunas de index.blade.php:
     lista (+contagem/ordenação/paginação) e painel de detalhe. --}}
<div class="{{ $vagas->isEmpty() ? 'min-w-0 xl:col-span-2' : 'min-w-0' }}">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-muted-foreground">
            <span class="font-semibold text-foreground">{{ $vagas->total() }}</span>
            {{ $vagas->total() === 1 ? 'vaga encontrada' : 'vagas encontradas' }}
        </p>
        <select name="ordenar" form="filtro-vagas" data-auto-apply class="h-8 w-44 rounded-[min(var(--radius-md),12px)] border border-input bg-transparent px-2.5 text-[0.925rem] outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 dark:bg-input/30">
            <option value="recentes" @selected(($filtros['ordenar'] ?? 'recentes') === 'recentes')>Mais recentes</option>
            <option value="encerramento" @selected(($filtros['ordenar'] ?? '') === 'encerramento')>Encerram primeiro</option>
        </select>
    </div>

    @if ($indisponivel)
        <div class="rounded-xl bg-card ring-1 ring-foreground/10">
            <x-empty-state title="Vagas temporariamente indisponíveis" description="Não conseguimos consultar as vagas agora. Isso não significa que não há vagas abertas — tente novamente em alguns minutos.">
                <x-slot:icon>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/></svg>
                </x-slot:icon>
                <x-ui.button tag="a" href="{{ route('vagas.publicas.index') }}" variant="outline">Tentar novamente</x-ui.button>
                <x-ui.button tag="a" href="{{ route('alertas.create') }}" variant="ghost">Criar alerta</x-ui.button>
            </x-empty-state>
        </div>
    @elseif ($vagas->isEmpty())
        <div class="rounded-xl bg-card ring-1 ring-foreground/10">
            <x-empty-state title="Nenhuma vaga encontrada" description="Tente ajustar os filtros ou volte em breve, novas vagas são publicadas com frequência.">
                @if (count(array_filter($filtros)) > 0)
                    <x-ui.button tag="a" href="{{ route('vagas.publicas.index') }}" variant="outline">Limpar filtros</x-ui.button>
                @endif
                <x-ui.button tag="a" href="{{ route('alertas.create') }}" variant="ghost">Criar alerta</x-ui.button>
            </x-empty-state>
        </div>
    @else
        <ul data-vaga-lista class="divide-y overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
            @foreach ($vagas as $vaga)
                <li><x-vaga-item :vaga="$vaga" :selecionada="$loop->first" /></li>
            @endforeach
        </ul>

        <x-pagination :paginator="$vagas" class="mt-6" />
    @endif
</div>

@unless ($indisponivel || $vagas->isEmpty())
    {{-- Painéis de detalhe de todas as vagas da página, um por vaga: seleção
         não faz fetch, só troca qual painel fica visível (resources/js/vagas-selecao.js).
         Coluna própria só a partir de xl; abaixo disso ficam ocultos aqui e
         servem de origem para o clone que abre no off-canvas mobile. --}}
    <div data-vaga-detalhes class="hidden min-w-0 rounded-xl ring-1 ring-foreground/10 xl:sticky xl:top-20 xl:block">
        @foreach ($vagas as $vaga)
            <x-vaga-detalhe :vaga="$vaga" data-vaga-detalhe-id="{{ $vaga->codigo }}" class="{{ $loop->first ? '' : 'hidden' }}" />
        @endforeach
    </div>
@endunless
