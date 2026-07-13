@extends('emails.vagas.layout')
@section('body')
<h2>Retorno do Processo Seletivo</h2>
<p>Olá, <strong>{{ $candidatura->nome }}</strong>!</p>
<p>Agradecemos seu interesse e o tempo dedicado ao processo seletivo da vaga <strong>{{ $candidatura->vaga->titulo }}</strong>.</p>
<p>Após análise cuidadosa, informamos que, desta vez, não foi possível prosseguir com sua candidatura. A decisão se baseia no perfil dos candidatos em relação às necessidades específicas do projeto no momento.</p>

<div class="info-box" style="border-left-color:#DC3545;background:#FFF5F5;">
    <div class="info-row"><span class="info-label">Vaga:</span><span class="info-value">{{ $candidatura->vaga->titulo }}</span></div>
    <div class="info-row"><span class="info-label">Resultado:</span><span class="info-value" style="color:#DC3545;font-weight:700;">Não aprovado(a)</span></div>
</div>

<p>Não desanime! Continue acompanhando o Portal de Vagas FAPEU para novas oportunidades que possam surgir. Desejamos sucesso na sua trajetória profissional.</p>
<p>Atenciosamente,<br><strong>Equipe FAPEU</strong></p>
@endsection
