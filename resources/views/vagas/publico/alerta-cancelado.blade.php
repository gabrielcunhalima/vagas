@extends('layouts.publico')

@section('title', 'Alerta Cancelado')

@section('content')
<div class="container py-5 text-center" style="max-width:500px">
    <div class="mb-3"><i class="bi bi-bell-slash text-muted" style="font-size:3rem"></i></div>
    <h2 class="h4 fw-bold">Alerta cancelado</h2>
    <p class="text-muted">Você não receberá mais notificações de vagas neste endereço de e-mail.</p>
    <a href="{{ url('/vagas') }}" class="btn btn-outline-success mt-2">Ver Vagas Disponíveis</a>
</div>
@endsection
