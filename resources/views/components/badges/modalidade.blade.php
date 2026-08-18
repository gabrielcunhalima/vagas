@props(['modalidade'])
<x-ui.badge variant="secondary">{{ \App\Models\Vagas\Vaga::$modalidadesLabel[$modalidade] ?? $modalidade }}</x-ui.badge>
