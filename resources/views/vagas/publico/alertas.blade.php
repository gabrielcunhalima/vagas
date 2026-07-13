@extends('layouts.publico')

@section('title', 'Alertas de Vagas')

@section('content')
<div class="container py-5" style="max-width:600px">
    <h1 class="h3 fw-bold text-success mb-2"><i class="bi bi-bell me-2"></i>Alertas de Vagas</h1>
    <p class="text-muted mb-4">Cadastre seu e-mail e receba notificações quando houver novas vagas compatíveis com seus interesses.</p>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('alertas.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="seu@email.com" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Áreas de interesse <span class="text-muted fw-normal">(opcional — deixe vazio para todas)</span></label>
                    <div class="row g-2">
                        @foreach($areas as $val => $label)
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="areas[]" value="{{ $val }}" id="area_{{ $val }}"
                                       @checked(in_array($val, old('areas', [])))>
                                <label class="form-check-label small" for="area_{{ $val }}">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Modalidade <span class="text-muted fw-normal">(opcional)</span></label>
                    <div class="d-flex gap-3 flex-wrap">
                        @foreach($modalidades as $val => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="modalidades[]" value="{{ $val }}" id="mod_{{ $val }}"
                                   @checked(in_array($val, old('modalidades', [])))>
                            <label class="form-check-label small" for="mod_{{ $val }}">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Tipo de vaga <span class="text-muted fw-normal">(opcional)</span></label>
                    <div class="d-flex gap-3 flex-wrap">
                        @foreach($tipos as $val => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tipos[]" value="{{ $val }}" id="tipo_{{ $val }}"
                                   @checked(in_array($val, old('tipos', [])))>
                            <label class="form-check-label small" for="tipo_{{ $val }}">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4 p-3" style="background:#f8fffe;border:1.5px solid rgba(13,149,113,0.25);border-radius:10px;">
                    <div class="form-check">
                        <input class="form-check-input @error('lgpd_consentimento') is-invalid @enderror"
                            type="checkbox" name="lgpd_consentimento" id="lgpdAlerta" value="1" required>
                        <label class="form-check-label" for="lgpdAlerta" style="font-size:0.875rem;">
                            Concordo com o armazenamento do meu e-mail para o envio destes alertas, de acordo com a
                            <abbr title="Lei Geral de Proteção de Dados">LGPD</abbr> (Lei nº 13.709/2018) e a
                            <a href="{{ route('politica.privacidade') }}" target="_blank">Política de Privacidade</a>.
                            Posso cancelar a qualquer momento pelo link enviado nos e-mails.
                        </label>
                        @error('lgpd_consentimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-bell me-2"></i>Ativar Alertas
                </button>
            </form>
        </div>
    </div>

    <p class="text-muted text-center mt-3" style="font-size:.85rem">
        Você pode cancelar os alertas a qualquer momento pelo link enviado nos e-mails.
    </p>
</div>
@endsection
