@extends('layouts.publico')

@section('title', 'Política de Privacidade — Portal de Vagas FAPEU')

@section('content')

<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:2.5rem 0 2rem;">
    <div class="container-xl text-center">
        <i class="bi bi-shield-lock-fill" style="font-size:2.5rem;color:rgba(255,255,255,0.85);display:block;margin-bottom:0.5rem;"></i>
        <h1 style="font-size:1.6rem;font-weight:800;color:#fff;margin:0 0 0.25rem;">Política de Privacidade</h1>
        <p style="color:rgba(255,255,255,0.78);font-size:0.9rem;margin:0;">Como tratamos seus dados pessoais neste portal</p>
    </div>
</div>

<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4 p-md-5" style="font-size:0.95rem;color:#3E3E3F;line-height:1.7;">

                    <p>
                        A Fundação de Amparo à Pesquisa e Extensão Universitária (FAPEU) é a controladora dos dados pessoais
                        coletados neste portal, em conformidade com a Lei Geral de Proteção de Dados Pessoais
                        (Lei nº 13.709/2018 — LGPD).
                    </p>

                    <h5 class="fw-bold mt-4" style="color:#074635;">1. Quais dados coletamos</h5>
                    <p>
                        Ao se cadastrar ou se candidatar a uma vaga, coletamos dados de identificação (nome, CPF, e-mail,
                        telefone), dados acadêmicos (curso, instituição, escolaridade), endereço, currículo, e,
                        quando informado voluntariamente por você, dados sobre acessibilidade/PCD e sobre eventual
                        conflito de interesse com a FAPEU.
                    </p>

                    <h5 class="fw-bold mt-4" style="color:#074635;">2. Finalidade do tratamento</h5>
                    <p>
                        Seus dados são utilizados exclusivamente para fins de processo seletivo: avaliação de candidaturas,
                        contato durante o processo, e envio de alertas de novas vagas compatíveis com seu interesse
                        (quando você solicitar esse serviço).
                    </p>

                    <h5 class="fw-bold mt-4" style="color:#074635;">3. Quem acessa seus dados</h5>
                    <p>
                        Seus dados são acessados exclusivamente pelos coordenadores dos projetos aos quais você se
                        candidatar e pela equipe administrativa da FAPEU responsável pela gestão de vagas. Não
                        compartilhamos seus dados com terceiros para fins comerciais.
                    </p>

                    <h5 class="fw-bold mt-4" style="color:#074635;">4. Por quanto tempo guardamos seus dados</h5>
                    <p>
                        Candidaturas vinculadas a vagas encerradas há mais de 180 dias têm seus dados pessoais
                        anonimizados automaticamente, mantendo-se apenas o histórico do processo seletivo (sem
                        identificação pessoal). Você pode solicitar a exclusão da sua conta a qualquer momento, o que
                        anonimiza imediatamente seus dados e os de suas candidaturas.
                    </p>

                    <h5 class="fw-bold mt-4" style="color:#074635;">5. Seus direitos como titular dos dados</h5>
                    <p>Você tem o direito de, a qualquer momento:</p>
                    <ul>
                        <li>Acessar e revisar os dados que mantemos sobre você, em <em>Meus Dados</em>;</li>
                        <li>Exportar uma cópia de todos os seus dados, em <em>Meus Dados → Exportar meus dados</em>;</li>
                        <li>Corrigir dados incompletos, inexatos ou desatualizados;</li>
                        <li>Revogar seu consentimento e excluir sua conta, em <em>Meus Dados → Excluir minha conta</em>;</li>
                        <li>Cancelar alertas de vaga pelo link enviado em qualquer e-mail de alerta.</li>
                    </ul>

                    <h5 class="fw-bold mt-4" style="color:#074635;">6. Segurança</h5>
                    <p>
                        Currículos e demais documentos enviados são armazenados em ambiente de acesso restrito,
                        acessível apenas mediante autenticação e somente pelos coordenadores da vaga correspondente.
                    </p>

                    <h5 class="fw-bold mt-4" style="color:#074635;">7. Contato</h5>
                    <p class="mb-0">
                        Em caso de dúvidas sobre o tratamento dos seus dados pessoais, entre em contato com a FAPEU
                        pelos canais oficiais disponíveis em <a href="https://fapeu.org.br" target="_blank" style="color:#0D9571;font-weight:600;">fapeu.org.br</a>.
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
