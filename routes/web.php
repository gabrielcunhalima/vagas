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
use App\Http\Controllers\Auth\CandidatoRegistroController;
use App\Http\Controllers\Auth\CandidatoVerificacaoController;
use App\Http\Controllers\Candidato\PerfilController as CandidatoPerfilController;
use App\Http\Controllers\Candidato\MinhaCandidaturaController;
use App\Http\Controllers\Vagas\AlertaVagaController;

Route::get('/auth/sso', [SsoController::class, 'entrar'])->name('auth.sso');

Route::get('/', [VagaPublicaController::class, 'index'])->name('home');
Route::view('/politica-privacidade', 'vagas.publico.politica-privacidade')->name('politica.privacidade');

Route::get('/vagas', [VagaPublicaController::class, 'index'])->name('vagas.publicas.index');
Route::get('/vagas/{vaga}', [VagaPublicaController::class, 'show'])->name('vagas.publicas.show');

Route::get('/fazenda-ressacada', fn() => view('vagas.publico.fazenda-ressacada'))->name('fazenda.ressacada');

Route::get('/candidatura/{vaga}', [InscricaoController::class, 'create'])->name('inscricao.create');
Route::post('/candidatura/{vaga}', [InscricaoController::class, 'store'])->name('inscricao.store');
Route::get('/candidatura/{vaga}/confirmacao', [InscricaoController::class, 'confirmacao'])->name('inscricao.confirmacao');

Route::get('/minhas-candidaturas', [InscricaoController::class, 'consultaForm'])->name('candidatura.consulta');
Route::post('/minhas-candidaturas', [InscricaoController::class, 'consulta'])->name('candidatura.consulta.busca');

Route::get('/alertas', [AlertaVagaController::class, 'create'])->name('alertas.create');
Route::post('/alertas', [AlertaVagaController::class, 'store'])->name('alertas.store');
Route::get('/alertas/cancelar/{token}', [AlertaVagaController::class, 'cancelar'])->name('alertas.cancelar');

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
    Route::get('/cadastro/verificar-cpf', [CandidatoRegistroController::class, 'verificarCpf'])->name('registro.verificar-cpf');

    // ─── Área logada (e-mail ainda não verificado) ───────────────────────────
    Route::middleware('candidato.auth')->group(function () {
        Route::get('/verificar-email', [CandidatoVerificacaoController::class, 'notice'])->name('verification.notice');
        Route::get('/verificar-email/{id}/{hash}', [CandidatoVerificacaoController::class, 'verify'])
            ->middleware('signed')->name('verification.verify');
        Route::post('/verificar-email/reenviar', [CandidatoVerificacaoController::class, 'resend'])->name('verification.send');
    });

    // ─── Área autenticada do candidato (e-mail verificado) ───────────────────
    Route::middleware(['candidato.auth', 'candidato.verified'])->group(function () {

        Route::get('/vagas', [VagaPublicaController::class, 'index'])->name('vagas');

        // Perfil / Meus Dados
        Route::get('/meus-dados', [CandidatoPerfilController::class, 'edit'])->name('perfil.edit');
        Route::put('/meus-dados', [CandidatoPerfilController::class, 'update'])->name('perfil.update');
        Route::put('/meus-dados/senha', [CandidatoPerfilController::class, 'updateSenha'])->name('perfil.senha');
        Route::delete('/meus-dados/curriculo', [CandidatoPerfilController::class, 'removerCurriculo'])->name('perfil.curriculo.remover');
        Route::get('/meus-dados/curriculo', [CandidatoPerfilController::class, 'downloadCurriculo'])->name('perfil.curriculo.download');
        Route::get('/meus-dados/exportar', [CandidatoPerfilController::class, 'exportarDados'])->name('perfil.exportar');
        Route::delete('/minha-conta', [CandidatoPerfilController::class, 'excluirConta'])->name('excluir');

        // Minhas Candidaturas
        Route::get('/candidaturas', [MinhaCandidaturaController::class, 'index'])->name('candidaturas.index');
        Route::get('/candidaturas/{candidatura}', [MinhaCandidaturaController::class, 'show'])->name('candidaturas.show');
        Route::get('/candidaturas/{candidatura}/curriculo', [MinhaCandidaturaController::class, 'downloadCurriculo'])->name('candidaturas.curriculo');
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
