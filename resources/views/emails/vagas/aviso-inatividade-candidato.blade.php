@extends('emails.vagas.layout')

@section('body')
<h2>Sua conta será encerrada em {{ $diasParaAnonimizar }} dias</h2>
<p>Olá, {{ $candidato->nome_exibicao }}!</p>
<p>
    Sua conta no Portal de Vagas da FAPEU está sem uso há mais de {{ $anosInativa }}
    {{ $anosInativa === 1 ? 'ano' : 'anos' }}. Como não guardamos dados pessoais além do
    necessário, contas inativas são encerradas e seus dados, anonimizados.
</p>

<p><strong>Para manter sua conta, basta entrar.</strong> Qualquer acesso cancela o encerramento.</p>

<div style="text-align:center;">
    <a href="{{ $url }}" class="btn">Entrar na minha conta</a>
</div>

<p>
    Se você não acessar em {{ $diasParaAnonimizar }} dias, seus dados pessoais e currículos
    serão removidos definitivamente. Essa ação não pode ser desfeita.
</p>
<p style="font-size:0.925rem;color:#6C757D;">
    Se preferir encerrar agora, você pode excluir a conta a qualquer momento em “Meus dados”.
</p>
@endsection
