<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;padding:0;background:#F8F9FA;font-family:'Segoe UI',Arial,sans-serif;}
.wrap{max-width:580px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);}
.header{background:linear-gradient(135deg,#074635,#0D9571);padding:28px 32px;text-align:center;}
.header h1{color:#fff;font-size:1.375rem;margin:8px 0 0;font-weight:700;}
.header p{color:rgba(255,255,255,0.75);font-size:0.945rem;margin:4px 0 0;}
.body{padding:32px;}
.footer{background:#F1F5F4;padding:16px 32px;text-align:center;font-size:0.875rem;color:#6C757D;border-top:1px solid #E9ECEF;}
.btn{display:inline-block;padding:12px 28px;background:#0D9571;color:#fff!important;font-weight:700;border-radius:8px;font-size:1.075rem;text-decoration:none!important;margin:16px 0;}
.info-box{background:#F8F9FA;border-left:4px solid #0D9571;border-radius:6px;padding:14px 16px;margin:16px 0;font-size:1rem;color:#3E3E3F;}
.info-row{display:flex;gap:8px;margin-bottom:6px;font-size:1rem;}
.info-label{font-weight:700;color:#495057;min-width:120px;}
.info-value{color:#3E3E3F;}
h2{font-size:1.275rem;font-weight:700;color:#2C4A44;margin:0 0 12px;}
p{font-size:1.025rem;line-height:1.65;color:#3E3E3F;margin:0 0 12px;}
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <img src="{{ url('imagens/fapeulogobranca.png') }}" alt="FAPEU" style="height:48px;width:auto;display:block;margin:0 auto 10px;">
        <h1>Portal de Vagas</h1>
        <p>FAPEU, Fundação de Apoio à Pesquisa e Extensão Universitária</p>
    </div>
    <div class="body">
        @yield('body')
    </div>
    <div class="footer">
        <p style="margin:0;">Este é um e-mail automático. Por favor, não responda a esta mensagem.</p>
        <p style="margin:4px 0 0;">© {{ date('Y') }} FAPEU. Todos os direitos reservados.</p>
    </div>
</div>
</body>
</html>
