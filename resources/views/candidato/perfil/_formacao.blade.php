{{-- $index: int|string ('__INDEX__' no template usado para clonar novas linhas). $formacao: array --}}
@php
    $niveis = \App\Models\CandidatoFormacao::$niveisLabel;
@endphp
<div class="rounded-lg bg-muted/30 p-4 ring-1 ring-foreground/10" data-formacao-linha>
    <div class="flex items-center justify-between gap-2">
        <h3 class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
            Formação <span data-formacao-numero>{{ is_numeric($index) ? $index + 1 : '' }}</span>
        </h3>
        <button type="button" data-remover-formacao class="hidden cursor-pointer text-sm text-muted-foreground hover:text-foreground">
            <svg class="inline size-4 -mt-0.5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14Z"/></svg>
            Remover
        </button>
    </div>
    <div class="mt-3 grid gap-4 sm:grid-cols-2">
        <x-field label="Nível de escolaridade" name="formacoes.{{ $index }}.nivel_escolaridade">
            <select id="nivel_escolaridade_{{ $index }}" name="formacoes[{{ $index }}][nivel_escolaridade]" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                <option value="">Selecione</option>
                @foreach ($niveis as $valor => $label)
                    <option value="{{ $valor }}" @selected(($formacao['nivel_escolaridade'] ?? '') === $valor)>{{ $label }}</option>
                @endforeach
            </select>
        </x-field>
        <x-field label="Situação do curso" name="formacoes.{{ $index }}.situacao_curso">
            <select id="situacao_curso_{{ $index }}" name="formacoes[{{ $index }}][situacao_curso]" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                <option value="">Selecione</option>
                <option value="cursando" @selected(($formacao['situacao_curso'] ?? '') === 'cursando')>Cursando</option>
                <option value="concluido" @selected(($formacao['situacao_curso'] ?? '') === 'concluido')>Concluído</option>
            </select>
        </x-field>
        <x-field label="Curso" name="formacoes.{{ $index }}.curso">
            <x-ui.input id="curso_{{ $index }}" name="formacoes[{{ $index }}][curso]" value="{{ $formacao['curso'] ?? '' }}" />
        </x-field>
        <x-field label="Instituição" name="formacoes.{{ $index }}.instituicao">
            <x-ui.input id="instituicao_{{ $index }}" name="formacoes[{{ $index }}][instituicao]" value="{{ $formacao['instituicao'] ?? '' }}" />
        </x-field>
        <div data-mostrar-se="situacao_curso_{{ $index }}=cursando" class="{{ ($formacao['situacao_curso'] ?? '') === 'cursando' ? '' : 'hidden' }}">
            <x-field label="Semestre" name="formacoes.{{ $index }}.semestre">
                <x-ui.input id="semestre_{{ $index }}" name="formacoes[{{ $index }}][semestre]" value="{{ $formacao['semestre'] ?? '' }}" />
            </x-field>
        </div>
        <x-field label="Previsão de conclusão" name="formacoes.{{ $index }}.previsao_conclusao">
            <x-ui.input id="previsao_conclusao_{{ $index }}" name="formacoes[{{ $index }}][previsao_conclusao]" type="date" value="{{ $formacao['previsao_conclusao'] ?? '' }}" />
        </x-field>
    </div>
</div>
