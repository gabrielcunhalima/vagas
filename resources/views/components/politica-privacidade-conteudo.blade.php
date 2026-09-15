<div class="flex items-center gap-3">
    <div class="flex size-11 items-center justify-center rounded-xl bg-accent">
        <svg class="size-5 text-accent-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
    </div>
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Política de Privacidade</h1>
        <p class="text-sm text-muted-foreground">Como tratamos seus dados pessoais neste portal.</p>
    </div>
</div>

<div class="mt-8 rounded-xl bg-card p-6 ring-1 ring-foreground/10 sm:p-8">
    <p class="text-sm leading-relaxed text-muted-foreground">
        A Fundação de Amparo à Pesquisa e Extensão Universitária (FAPEU) é a controladora dos dados
        pessoais coletados neste portal, em conformidade com a Lei Geral de Proteção de Dados Pessoais
        (Lei nº 13.709/2018, LGPD).
    </p>

    <section class="mt-8">
        <h2 class="text-base font-bold tracking-tight">1. Quais dados coletamos</h2>
        <div class="mt-2 text-sm leading-relaxed text-muted-foreground">
            <p>
                Ao se cadastrar ou se candidatar a uma vaga, coletamos dados de identificação (nome, CPF,
                e-mail, telefone), dados acadêmicos (curso, instituição, escolaridade), endereço, currículo
                e, quando informado voluntariamente por você, dados sobre acessibilidade/PcD e sobre
                eventual conflito de interesse com a FAPEU.
            </p>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-base font-bold tracking-tight">2. Finalidade do tratamento</h2>
        <div class="mt-2 text-sm leading-relaxed text-muted-foreground">
            <p>
                Seus dados são utilizados exclusivamente para fins de processo seletivo: avaliação de
                candidaturas, contato durante o processo e envio de alertas de novas vagas compatíveis com
                seu interesse (quando você solicitar esse serviço).
            </p>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-base font-bold tracking-tight">3. Quem acessa seus dados</h2>
        <div class="mt-2 text-sm leading-relaxed text-muted-foreground">
            <p>
                Seus dados são acessados exclusivamente pelos coordenadores dos projetos aos quais você se
                candidatar e pela equipe administrativa da FAPEU responsável pela gestão de vagas. Não
                compartilhamos seus dados com terceiros para fins comerciais.
            </p>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-base font-bold tracking-tight">4. Por quanto tempo guardamos seus dados</h2>
        <div class="mt-2 text-sm leading-relaxed text-muted-foreground">
            <p>
                Candidaturas vinculadas a vagas encerradas há mais de 180 dias têm seus dados pessoais
                anonimizados automaticamente, mantendo-se apenas o histórico do processo seletivo (sem
                identificação pessoal). Você pode solicitar a exclusão da sua conta a qualquer momento, o
                que anonimiza imediatamente seus dados e os de suas candidaturas.
            </p>
            <p class="mt-2">
                O currículo enviado fica armazenado por {{ \App\Models\CandidatoCurriculo::RETENCAO_MESES }} meses a partir do envio
                ou da sua última candidatura, o que for mais recente. Passado esse prazo sem nova candidatura, ele é
                removido automaticamente, e um novo currículo será pedido na próxima vez que você se candidatar.
            </p>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-base font-bold tracking-tight">5. Seus direitos como titular dos dados</h2>
        <div class="mt-2 text-sm leading-relaxed text-muted-foreground">
            <p>Você tem o direito de, a qualquer momento:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                <li>Acessar e revisar os dados que mantemos sobre você, em <em>Meus Dados</em>;</li>
                <li>Exportar uma cópia de todos os seus dados, em <em>Meus Dados → Exportar meus dados</em>;</li>
                <li>Corrigir dados incompletos, inexatos ou desatualizados;</li>
                <li>Revogar seu consentimento e excluir sua conta, em <em>Meus Dados → Excluir minha conta</em>;</li>
                <li>Cancelar alertas de vaga pelo link enviado em qualquer e-mail de alerta.</li>
            </ul>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-base font-bold tracking-tight">6. Segurança</h2>
        <div class="mt-2 text-sm leading-relaxed text-muted-foreground">
            <p>
                Currículos e demais documentos enviados são armazenados em ambiente de acesso restrito,
                acessível apenas mediante autenticação e somente pelos coordenadores da vaga correspondente.
            </p>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-base font-bold tracking-tight">7. Contato</h2>
        <div class="mt-2 text-sm leading-relaxed text-muted-foreground">
            <p>
                Em caso de dúvidas sobre o tratamento dos seus dados pessoais, entre em contato com a FAPEU
                pelos canais oficiais disponíveis em
                <a href="https://fapeu.org.br" target="_blank" rel="noreferrer" class="font-semibold text-primary hover:underline">fapeu.org.br</a>.
            </p>
        </div>
    </section>
</div>
