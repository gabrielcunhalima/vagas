<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vagas\VagaPublicaController;
use App\Http\Controllers\Vagas\InscricaoController;
use App\Http\Controllers\Vagas\VagaController;
use App\Http\Controllers\Vagas\CandidaturaController;
use App\Http\Controllers\Vagas\DashboardController;
use App\Http\Controllers\Vagas\CepController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\Auth\CandidatoLoginController;
use App\Http\Controllers\Auth\CandidatoRecuperarSenhaController;
use App\Http\Controllers\Auth\CandidatoRegistroController;
use App\Http\Controllers\Auth\CandidatoVerificacaoController;
use App\Http\Controllers\Candidato\PerfilController as CandidatoPerfilController;
use App\Http\Controllers\Candidato\MinhaCandidaturaController;
use App\Http\Controllers\Vagas\AlertaVagaController;

Route::get('/auth/sso', [SsoController::class, 'entrar'])->name('auth.sso');

Route::get('/', [VagaPublicaController::class, 'index'])->name('home');
Route::inertia('/politica-privacidade', 'Publico/PoliticaPrivacidade')->name('politica.privacidade');

Route::get('/vagas', [VagaPublicaController::class, 'index'])->name('vagas.publicas.index');
Route::get('/vagas/{vaga}', [VagaPublicaController::class, 'show'])->name('vagas.publicas.show');

Route::inertia('/fazenda-ressacada', 'Publico/FazendaRessacada')->name('fazenda.ressacada');

// Candidatar-se e gerenciar alertas exigem conta com e-mail verificado.
Route::middleware(['candidato.auth', 'candidato.verified'])->group(function () {
    Route::get('/candidatura/{vaga}', [InscricaoController::class, 'create'])->name('inscricao.create');
    Route::post('/candidatura/{vaga}', [InscricaoController::class, 'store'])->name('inscricao.store');
    Route::get('/candidatura/{vaga}/confirmacao', [InscricaoController::class, 'confirmacao'])->name('inscricao.confirmacao');

    Route::get('/alertas', [AlertaVagaController::class, 'create'])->name('alertas.create');
    Route::post('/alertas', [AlertaVagaController::class, 'store'])->name('alertas.store');
});

// Sair de uma lista de e-mails nunca pode exigir login.
Route::get('/alertas/cancelar/{token}', [AlertaVagaController::class, 'cancelar'])->name('alertas.cancelar');

/*
 * Legado: a consulta por CPF + e-mail existia para quem se candidatava sem conta.
 * Como toda candidatura passa a pertencer a uma conta, o acompanhamento agora é
 * pela área autenticada. Mantido como redirecionamento para não quebrar links.
 */
Route::redirect('/minhas-candidaturas', '/minha-conta/candidaturas')->name('candidatura.consulta');

Route::get('/api/cep/{cep}', [CepController::class, 'buscar'])
    ->where('cep', '[0-9\-]{8,9}')
    ->name('api.cep');

// ─── Auth Candidato ──────────────────────────────────────────────────────────
Route::prefix('minha-conta')->name('candidato.')->group(function () {

    Route::get('/entrar', [CandidatoLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/entrar', [CandidatoLoginController::class, 'login'])->name('login.post');
    Route::post('/sair', [CandidatoLoginController::class, 'logout'])->name('logout');

    Route::get('/cadastro', [CandidatoRegistroController::class, 'showForm'])->name('registro');
    Route::post('/cadastro', [CandidatoRegistroController::class, 'store'])->name('registro.post');
    Route::get('/cadastro/verificar-cpf', [CandidatoRegistroController::class, 'verificarCpf'])
        ->middleware('throttle:30,1')
        ->name('registro.verificar-cpf');

    // ─── Recuperação de senha ─────────────────────────────────────────────────
    Route::get('/esqueci-senha', [CandidatoRecuperarSenhaController::class, 'showLinkRequestForm'])->name('senha.request');
    Route::post('/esqueci-senha', [CandidatoRecuperarSenhaController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:6,1')
        ->name('senha.email');
    Route::get('/redefinir-senha/{token}', [CandidatoRecuperarSenhaController::class, 'showResetForm'])->name('senha.reset');
    Route::post('/redefinir-senha', [CandidatoRecuperarSenhaController::class, 'reset'])
        ->middleware('throttle:6,1')
        ->name('senha.update');

    /*
     * A verificação de e-mail passa a valer por ATO, não por área.
     *
     * Bloquear a área inteira devolveria na saída o atrito que o cadastro mínimo
     * removeu na entrada: a pessoa cria a conta e não consegue nem preencher o
     * perfil antes de sair para o e-mail. Navegar e mexer nos próprios dados não
     * tem efeito externo; candidatar-se e ativar alertas têm.
     */
    Route::middleware('candidato.auth')->group(function () {

        Route::get('/verificar-email', [CandidatoVerificacaoController::class, 'notice'])->name('verification.notice');
        Route::get('/verificar-email/{id}/{hash}', [CandidatoVerificacaoController::class, 'verify'])
            ->middleware('signed')->name('verification.verify');
        Route::post('/verificar-email/reenviar', [CandidatoVerificacaoController::class, 'resend'])->name('verification.send');

        Route::get('/vagas', [VagaPublicaController::class, 'index'])->name('vagas');

        // Perfil / Meus Dados — dado próprio, sem efeito externo: liberado sem verificação.
        Route::get('/meus-dados', [CandidatoPerfilController::class, 'edit'])->name('perfil.edit');
        Route::put('/meus-dados', [CandidatoPerfilController::class, 'update'])->name('perfil.update');
        Route::put('/meus-dados/senha', [CandidatoPerfilController::class, 'updateSenha'])->name('perfil.senha');
        Route::delete('/meus-dados/curriculo', [CandidatoPerfilController::class, 'removerCurriculo'])->name('perfil.curriculo.remover');
        Route::get('/meus-dados/curriculo', [CandidatoPerfilController::class, 'downloadCurriculo'])->name('perfil.curriculo.download');
        Route::get('/meus-dados/exportar', [CandidatoPerfilController::class, 'exportarDados'])->name('perfil.exportar');
        Route::delete('/minha-conta', [CandidatoPerfilController::class, 'excluirConta'])->name('excluir');

        /*
         * Candidaturas exigem verificação: como candidatar-se já a exige, tudo que
         * uma conta não verificada veria aqui só pode ter vindo da incorporação de
         * histórico anterior, cuja titularidade ainda não foi comprovada.
         */
        Route::middleware('candidato.verified')->group(function () {
            Route::get('/candidaturas', [MinhaCandidaturaController::class, 'index'])->name('candidaturas.index');
            Route::get('/candidaturas/{candidatura}', [MinhaCandidaturaController::class, 'show'])->name('candidaturas.show');
            Route::get('/candidaturas/{candidatura}/curriculo', [MinhaCandidaturaController::class, 'downloadCurriculo'])->name('candidaturas.curriculo');
        });
    });
});

// ─── Auth Coordenador / Sistema Interno ──────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('coord')
    ->middleware(['auth', 'perfil:coordenador,admin'])
    ->name('coord.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/vagas', [VagaController::class, 'index'])->name('vagas.index');
        Route::get('/vagas/create', [VagaController::class, 'create'])->name('vagas.create');
        Route::post('/vagas', [VagaController::class, 'store'])->name('vagas.store');
        Route::get('/vagas/{vaga}/edit', [VagaController::class, 'edit'])->name('vagas.edit');
        Route::put('/vagas/{vaga}', [VagaController::class, 'update'])->name('vagas.update');
        Route::delete('/vagas/{vaga}', [VagaController::class, 'destroy'])->name('vagas.destroy');

        Route::patch('/vagas/{vaga}/submeter', [VagaController::class, 'submeter'])->name('vagas.submeter');
        Route::patch('/vagas/{vaga}/desativar', [VagaController::class, 'desativar'])->name('vagas.desativar');
        Route::patch('/vagas/{vaga}/reativar', [VagaController::class, 'reativar'])->name('vagas.reativar');
        Route::patch('/vagas/{vaga}/notificacao', [VagaController::class, 'toggleNotificacao'])->name('vagas.notificacao');

        Route::get('/candidaturas', [CandidaturaController::class, 'todas'])->name('candidaturas.todas');
        Route::get('/vagas/{vaga}/candidaturas', [CandidaturaController::class, 'index'])->name('candidaturas.index');
        Route::get('/vagas/{vaga}/candidaturas/{candidatura}', [CandidaturaController::class, 'show'])->name('candidaturas.show');
        Route::patch('/vagas/{vaga}/candidaturas/{candidatura}/status', [CandidaturaController::class, 'updateStatus'])->name('candidaturas.updateStatus');
        Route::get('/vagas/{vaga}/candidaturas/{candidatura}/curriculo', [CandidaturaController::class, 'downloadCurriculo'])->name('candidaturas.curriculo');
    });

Route::prefix('gestor')
    ->middleware(['auth', 'perfil:gestor,admin'])
    ->name('gestor.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'indexGestor'])->name('dashboard');

        Route::get('/vagas', [VagaController::class, 'indexGestor'])->name('vagas.index');
        Route::get('/vagas/{vaga}', [VagaController::class, 'showGestor'])->name('vagas.show');
        Route::patch('/vagas/{vaga}/autorizar', [VagaController::class, 'autorizar'])->name('vagas.autorizar');
        Route::patch('/vagas/{vaga}/recusar', [VagaController::class, 'recusar'])->name('vagas.recusar');
    });
