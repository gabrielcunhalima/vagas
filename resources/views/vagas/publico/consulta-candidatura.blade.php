@extends('layouts.publico')

@section('title', 'Minhas Candidaturas')

@section('content')

<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:3rem 0 2.5rem;">
    <div class="container-xl">
        <h1 style="font-size:2rem;font-weight:800;color:#fff;line-height:1.15;margin-bottom:0.5rem;">
            Minhas candidaturas
        </h1>
        <p style="color:rgba(255,255,255,0.78);font-size:0.95rem;margin:0;">
            Informe seu CPF e e-mail para consultar o andamento das suas candidaturas.
        </p>
    </div>
</div>

<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 15px rgba(0,0,0,0.07);padding:2rem;margin-bottom:2rem;">
                <form action="{{ route('candidatura.consulta.busca') }}" method="POST" novalidate>
                    @csrf

                    @if($errors->any())
                    <div class="alert alert-danger mb-3" style="font-size:0.875rem;">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text" name="cpf" id="cpf"
                            class="form-control @error('cpf') is-invalid @enderror"
                            value="{{ old('cpf') }}"
                            placeholder="000.000.000-00"
                            maxlength="14"
                            autocomplete="off">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="seu@email.com"
                            autocomplete="off">
                    </div>

                    <button type="submit" class="btn btn-principal w-100">
                        <i class="bi bi-search me-2"></i> Consultar
                    </button>
                </form>
            </div>

            @isset($candidaturas)
                @if($candidaturas->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size:3rem;color:#CACACA;"></i>
                        <h5 class="mt-3" style="color:#6C757D;">Nenhuma candidatura encontrada</h5>
                        <p style="color:#CACACA;font-size:0.9rem;">Verifique se o CPF e o e-mail estão corretos.</p>
                    </div>
                @else
                    <h6 style="font-weight:700;color:#495057;margin-bottom:1rem;">
                        {{ $candidaturas->count() }} {{ $candidaturas->count() === 1 ? 'candidatura encontrada' : 'candidaturas encontradas' }}
                    </h6>

                    @foreach($candidaturas as $candidatura)
                    @php
                        $corMap = [
                            'recebida'   => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'icon' => 'bi-inbox-fill'],
                            'em_analise' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'icon' => 'bi-hourglass-split'],
                            'entrevista' => ['bg' => '#EEF2FF', 'text' => '#4338CA', 'icon' => 'bi-calendar-check-fill'],
                            'aprovado'   => ['bg' => '#F0FDF4', 'text' => '#166534', 'icon' => 'bi-check-circle-fill'],
                            'reprovado'  => ['bg' => '#FFF1F2', 'text' => '#9F1239', 'icon' => 'bi-x-circle-fill'],
                        ];
                        $cor = $corMap[$candidatura->status] ?? ['bg' => '#F3F4F6', 'text' => '#374151', 'icon' => 'bi-circle'];
                    @endphp
                    <div style="background:#fff;border-radius:16px;box-shadow:0 4px 15px rgba(0,0,0,0.07);overflow:hidden;margin-bottom:1rem;">
                        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div>
                                    <div style="font-size:1rem;font-weight:700;color:#1F2937;margin-bottom:2px;">
                                        {{ $candidatura->vaga->titulo ?? 'Vaga removida' }}
                                    </div>
                                    @if($candidatura->vaga && $candidatura->vaga->projeto_nome)
                                    <div style="font-size:0.82rem;color:#6C757D;">
                                        <i class="bi bi-building me-1"></i>{{ $candidatura->vaga->projeto_nome }}
                                    </div>
                                    @endif
                                </div>
                                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:700;background:{{ $cor['bg'] }};color:{{ $cor['text'] }};white-space:nowrap;flex-shrink:0;">
                                    <i class="bi {{ $cor['icon'] }}"></i>
                                    {{ $candidatura->status_label }}
                                </span>
                            </div>
                        </div>

                        <div style="padding:1rem 1.5rem;">
                            <div class="d-flex flex-wrap gap-3" style="font-size:0.82rem;color:#6C757D;">
                                <span><i class="bi bi-calendar3 me-1"></i> Candidatura em {{ $candidatura->created_at->format('d/m/Y') }}</span>
                                @if($candidatura->vaga)
                                    <span><i class="bi bi-tag me-1"></i> {{ $candidatura->vaga->tipo_label }}</span>
                                @endif
                            </div>

                            {{-- Progress step --}}
                            @php
                                $passos = ['recebida' => 1, 'em_analise' => 2, 'entrevista' => 3, 'aprovado' => 4, 'reprovado' => 4];
                                $passoAtual = $passos[$candidatura->status] ?? 1;
                                $labelsP = ['Recebida', 'Análise', 'Entrevista', 'Resultado'];
                            @endphp
                            <div style="margin-top:0.75rem;display:flex;align-items:center;gap:4px;">
                                @foreach($labelsP as $i => $lp)
                                <div style="flex:1;text-align:center;">
                                    <div style="width:24px;height:24px;border-radius:50%;margin:0 auto 3px;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;
                                        background:{{ $passoAtual >= ($i+1) ? '#0D9571' : '#e9ecef' }};
                                        color:{{ $passoAtual >= ($i+1) ? '#fff' : '#6c757d' }}">{{ $i+1 }}</div>
                                    <div style="font-size:0.65rem;color:{{ $passoAtual >= ($i+1) ? '#0D9571' : '#adb5bd' }};font-weight:{{ $passoAtual === ($i+1) ? '700' : '400' }}">{{ $lp }}</div>
                                </div>
                                @if($i < count($labelsP) - 1)
                                <div style="flex:0.5;height:2px;background:{{ $passoAtual > ($i+1) ? '#0D9571' : '#e9ecef' }};margin-bottom:1rem;"></div>
                                @endif
                                @endforeach
                            </div>

                            @if($candidatura->status === 'entrevista' && $candidatura->entrevista_data)
                            <div style="margin-top:0.75rem;background:#EEF2FF;border-radius:10px;padding:0.75rem 1rem;font-size:0.85rem;">
                                <div style="font-weight:700;color:#4338CA;margin-bottom:0.25rem;">
                                    <i class="bi bi-calendar-event-fill me-1"></i> Entrevista agendada
                                </div>
                                <div style="color:#4338CA;">
                                    {{ $candidatura->entrevista_data->format('d/m/Y') }} às {{ $candidatura->entrevista_data->format('H:i') }}
                                    @if($candidatura->entrevista_local)
                                        — {{ $candidatura->entrevista_local }}
                                    @endif
                                </div>
                                @if($candidatura->entrevista_observacoes)
                                <div style="margin-top:0.35rem;color:#6366F1;font-size:0.8rem;">{{ $candidatura->entrevista_observacoes }}</div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            @endisset

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('cpf').addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        this.value = v;
    });
</script>
@endpush

@endsection
