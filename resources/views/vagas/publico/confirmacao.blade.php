@extends('layouts.publico')

@section('title', 'Candidatura Enviada!')

@section('content')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:3rem 1rem;">
    <div style="max-width:520px;width:100%;text-align:center;">
        <div style="width:80px;height:80px;background:rgba(13,149,113,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
            <i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#0D9571;"></i>
        </div>
        <h2 style="font-size:1.75rem;font-weight:800;color:#2C4A44;margin-bottom:0.5rem;">Candidatura enviada!</h2>
        @if(session('candidatura_nome'))
        <p style="font-size:1rem;color:#6C757D;margin-bottom:0.5rem;">
            Olá, <strong>{{ session('candidatura_nome') }}</strong>!
        </p>
        @endif
        <p style="font-size:0.95rem;color:#6C757D;line-height:1.6;margin-bottom:2rem;">
            Recebemos sua candidatura para <strong>{{ $vaga->titulo }}</strong>.<br>
            Você receberá um e-mail de confirmação em breve. Acompanhe sua caixa de entrada.
        </p>

        <div style="background:#F8F9FA;border-radius:12px;padding:1.25rem;margin-bottom:2rem;text-align:left;">
            <div style="font-size:0.78rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#6C757D;margin-bottom:0.75rem;">
                Próximos passos
            </div>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.6rem;">
                <li style="display:flex;align-items:flex-start;gap:0.6rem;font-size:0.875rem;color:#3E3E3F;">
                    <i class="bi bi-envelope-check-fill" style="color:#0D9571;margin-top:2px;flex-shrink:0;"></i>
                    Verifique seu e-mail — você receberá uma confirmação de recebimento.
                </li>
                <li style="display:flex;align-items:flex-start;gap:0.6rem;font-size:0.875rem;color:#3E3E3F;">
                    <i class="bi bi-search" style="color:#0D9571;margin-top:2px;flex-shrink:0;"></i>
                    O coordenador irá analisar sua candidatura.
                </li>
                <li style="display:flex;align-items:flex-start;gap:0.6rem;font-size:0.875rem;color:#3E3E3F;">
                    <i class="bi bi-calendar-check" style="color:#0D9571;margin-top:2px;flex-shrink:0;"></i>
                    Se selecionado para entrevista, você será notificado por e-mail com data e local.
                </li>
            </ul>
        </div>

        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="{{ route('vagas.publicas.index') }}" class="btn btn-principal px-4">
                <i class="bi bi-briefcase me-2"></i> Ver mais vagas
            </a>
            <a href="{{ route('vagas.publicas.show', $vaga) }}" class="btn btn-outline-principal px-4">
                <i class="bi bi-arrow-left me-2"></i> Voltar à vaga
            </a>
        </div>
    </div>
</div>
@endsection