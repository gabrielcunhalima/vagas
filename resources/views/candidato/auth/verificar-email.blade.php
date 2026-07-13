@extends('layouts.publico')

@section('title', 'Confirme seu e-mail — Portal de Vagas FAPEU')

@section('content')

<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:2.5rem 0 2rem;">
    <div class="container-xl text-center">
        <i class="bi bi-envelope-check-fill" style="font-size:2.5rem;color:rgba(255,255,255,0.85);display:block;margin-bottom:0.5rem;"></i>
        <h1 style="font-size:1.6rem;font-weight:800;color:#fff;margin:0 0 0.25rem;">Confirme seu e-mail</h1>
        <p style="color:rgba(255,255,255,0.78);font-size:0.9rem;margin:0;">Falta só um passo para acessar sua conta</p>
    </div>
</div>

<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            @if(session('success'))
            <div class="alert alert-success mb-4" style="border-radius:10px;font-size:0.875rem;">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
            @endif

            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4 text-center">
                    <p style="font-size:0.95rem;color:#3E3E3F;">
                        Enviamos um e-mail de confirmação para <strong>{{ auth('candidato')->user()->email }}</strong>.
                        Clique no link recebido para liberar o acesso à sua conta.
                    </p>
                    <p style="font-size:0.875rem;color:#6c757d;">Não recebeu? Verifique sua caixa de spam ou solicite o reenvio.</p>

                    <form action="{{ route('candidato.verification.send') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-principal">
                            <i class="bi bi-arrow-repeat me-2"></i> Reenviar e-mail de confirmação
                        </button>
                    </form>

                    <form action="{{ route('candidato.logout') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted" style="font-size:0.875rem;">
                            Sair da conta
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
