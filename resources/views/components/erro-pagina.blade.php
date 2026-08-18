@props(['status', 'titulo', 'texto'])
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.theme-script')
    <title>{{ $status }}: {{ $titulo }} - Portal de Vagas FAPEU</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('imagens/fapeu_ico.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="flex min-h-dvh flex-col items-center justify-center bg-background px-4 text-center">
        <x-logo class="h-10" />
        <p class="mt-8 text-6xl font-bold tracking-tight text-primary/25">{{ $status }}</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight">{{ $titulo }}</h1>
        <p class="mt-2 max-w-sm text-sm leading-relaxed text-muted-foreground">{{ $texto }}</p>
        <x-ui.button tag="a" href="{{ route('home') }}" class="mt-7">Voltar ao início</x-ui.button>
    </div>
</body>
</html>
