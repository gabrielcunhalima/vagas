@extends('layouts.publico')

@section('title', 'Fazenda Ressacada — Projeto UFSC')

@push('styles')
<style>
    .hero-ressacada {
        background: linear-gradient(135deg, #074635 0%, #0D9571 60%, #0a7a5a 100%);
        padding: 90px 0 70px;
        position: relative;
        overflow: hidden;
    }

    .hero-ressacada::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 20px;
        margin-bottom: 1.25rem;
    }

    .hero-title {
        font-family: 'DM Sans', sans-serif;
        font-size: clamp(2rem, 5vw, 3.25rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
        margin-bottom: 1rem;
    }

    .hero-subtitle {
        font-size: 1.1rem;
        color: rgba(255,255,255,0.82);
        max-width: 580px;
        line-height: 1.7;
    }

    .hero-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 2.5rem;
    }

    .hero-stat {
        text-align: center;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.18);
        border-radius: 14px;
        padding: 14px 22px;
        min-width: 110px;
    }

    .hero-stat-num {
        font-family: 'DM Sans', sans-serif;
        font-size: 1.7rem;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .hero-stat-label {
        font-size: 0.73rem;
        color: rgba(255,255,255,0.65);
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .hero-img-wrap {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        aspect-ratio: 4/3;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 12px;
    }

    .section-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #0D9571;
        margin-bottom: 0.5rem;
    }

    .section-title {
        font-family: 'DM Sans', sans-serif;
        font-size: clamp(1.5rem, 3vw, 2.1rem);
        font-weight: 800;
        color: #1a2e1a;
        line-height: 1.25;
        margin-bottom: 1rem;
    }

    .section-text {
        color: #555;
        line-height: 1.8;
        font-size: 1rem;
    }

    .card-area {
        border: none;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        padding: 28px 24px;
        height: 100%;
        transition: transform 0.25s, box-shadow 0.25s;
        background: #fff;
    }

    .card-area:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 35px rgba(0,0,0,0.1);
    }

    .card-area-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .card-area-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: #1a2e1a;
        margin-bottom: 0.5rem;
    }

    .card-area-text {
        font-size: 0.875rem;
        color: #666;
        line-height: 1.65;
        margin: 0;
    }

    .timeline-item {
        position: relative;
        padding-left: 44px;
        padding-bottom: 2rem;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 17px;
        top: 34px;
        bottom: 0;
        width: 2px;
        background: #e0f2ed;
    }

    .timeline-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 36px;
        height: 36px;
        background: #0D9571;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.9rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .timeline-year {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #0D9571;
        margin-bottom: 2px;
    }

    .timeline-title {
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        color: #1a2e1a;
        margin-bottom: 4px;
    }

    .timeline-text {
        font-size: 0.85rem;
        color: #666;
        line-height: 1.6;
        margin: 0;
    }

    .highlight-box {
        background: linear-gradient(135deg, #074635, #0D9571);
        border-radius: 20px;
        padding: 50px 48px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .highlight-box::before {
        content: '"';
        position: absolute;
        top: -20px;
        left: 30px;
        font-size: 180px;
        color: rgba(255,255,255,0.06);
        font-family: Georgia, serif;
        line-height: 1;
    }

    .especie-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(13,149,113,0.08);
        border: 1px solid rgba(13,149,113,0.2);
        color: #0D9571;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 20px;
    }

    .info-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        padding: 28px;
        border-left: 4px solid #0D9571;
    }

    .parceiro-logo {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 18px 24px;
        text-align: center;
        font-size: 0.82rem;
        font-weight: 600;
        color: #555;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .parceiro-logo:hover {
        border-color: #0D9571;
        box-shadow: 0 4px 15px rgba(13,149,113,0.12);
    }

    .bg-section-alt {
        background: #f0faf6;
    }

    .map-placeholder {
        background: linear-gradient(135deg, #e8f5f0, #d0ede4);
        border-radius: 16px;
        height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        border: 2px dashed rgba(13,149,113,0.3);
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="hero-ressacada">
    <div class="container-xl position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="bi bi-mortarboard-fill"></i>
                    Projeto Universitário · UFSC
                </div>
                <h1 class="hero-title">Fazenda Ressacada</h1>
                <p class="hero-subtitle">
                    Estação de Aquicultura da Universidade Federal de Santa Catarina, referência nacional
                    em pesquisa, ensino e extensão no cultivo sustentável de organismos aquáticos.
                </p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-num">+40</div>
                        <div class="hero-stat-label">Anos de pesquisa</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">12 ha</div>
                        <div class="hero-stat-label">Área total</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">6</div>
                        <div class="hero-stat-label">Espécies cultivadas</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">200+</div>
                        <div class="hero-stat-label">Pesquisadores</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="hero-img-wrap">
                    <i class="bi bi-water" style="font-size:4rem;color:rgba(255,255,255,0.35);"></i>
                    <span style="color:rgba(255,255,255,0.45);font-size:0.82rem;">Fazenda Ressacada · Florianópolis, SC</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Breadcrumb --}}
<div style="background:#fff;border-bottom:1px solid #e9ecef;padding:10px 0;">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-principal">Início</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vagas.publicas.index') }}" class="text-principal">Vagas</a></li>
                <li class="breadcrumb-item active">Fazenda Ressacada</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Sobre o projeto --}}
<section class="py-5 mt-2">
    <div class="container-xl">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="section-label">Sobre o Projeto</div>
                <h2 class="section-title">Uma estação de referência em agricultura sustentável</h2>
                <p class="section-text mb-3">
                    Localizada no município de Florianópolis, no bairro Ressacada, a Fazenda Ressacada é uma
                    Unidade de Pesquisa, Ensino e Extensão vinculada ao Departamento de Agronomia (CCA) da
                    Universidade Federal de Santa Catarina (UFSC).
                </p>
                <p class="section-text mb-3">
                    Fundada na década de 1980, a estação atua como laboratório vivo onde estudantes de graduação,
                    pós-graduação e pesquisadores desenvolvem estudos sobre o cultivo de camarões, ostras,
                    mexilhões, peixes e algas marinhas, contribuindo para o desenvolvimento científico e
                    tecnológico da agricultura brasileira.
                </p>
                <p class="section-text">
                    Além da pesquisa, a Fazenda Ressacada promove ações de extensão junto às comunidades de
                    agricultores e agrícolas de Santa Catarina, transferindo tecnologia e boas práticas
                    que fortalecem a produção regional.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card-area">
                            <div class="card-area-icon" style="background:rgba(13,149,113,0.1);">
                                <i class="bi bi-droplet text-principal"></i>
                            </div>
                            <div class="card-area-title">Maricultura</div>
                            <p class="card-area-text">Cultivo de ostras, mexilhões e vieiras em ambiente marinho controlado.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card-area">
                            <div class="card-area-icon" style="background:rgba(13,110,253,0.1);">
                                <i class="bi bi-tsunami" style="color:#0D6EFD;"></i>
                            </div>
                            <div class="card-area-title">Carcinicultura</div>
                            <p class="card-area-text">Pesquisa com camarão-branco do Pacífico em sistemas intensivos e superintensivos.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card-area">
                            <div class="card-area-icon" style="background:rgba(111,66,193,0.1);">
                                <i class="bi bi-feather" style="color:#6F42C1;"></i>
                            </div>
                            <div class="card-area-title">Piscicultura</div>
                            <p class="card-area-text">Estudos com espécies nativas e exóticas em sistemas de recirculação de água (RAS).</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card-area">
                            <div class="card-area-icon" style="background:rgba(230,81,0,0.1);">
                                <i class="bi bi-flower1" style="color:#E65100;"></i>
                            </div>
                            <div class="card-area-title">Algas Marinhas</div>
                            <p class="card-area-text">Cultivo de microalgas e macroalgas para alimentação, biocombustíveis e cosméticos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Espécies cultivadas --}}
<section class="py-5 bg-section-alt">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="section-label">Biodiversidade</div>
            <h2 class="section-title">Espécies cultivadas na Fazenda</h2>
            <p class="section-text mx-auto" style="max-width:560px;">
                A estação mantém cultivos ativos de diversas espécies, integrando produção, pesquisa e material didático para as turmas do AQI.
            </p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-area">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="card-area-icon mb-0" style="background:rgba(13,149,113,0.1);">
                            <i class="bi bi-circle-fill text-principal" style="font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="card-area-title mb-0">Ostra-do-Pacífico</div>
                            <small class="text-muted fst-italic">Crassostrea gigas</small>
                        </div>
                    </div>
                    <p class="card-area-text">Principal espécie de maricultura do estado. Santa Catarina responde por mais de 95% da produção nacional, e a UFSC contribui com genética e manejo de ponta.</p>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="especie-pill"><i class="bi bi-geo-alt"></i> Maricultura</span>
                        <span class="especie-pill"><i class="bi bi-star"></i> Espécie-chave</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-area">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="card-area-icon mb-0" style="background:rgba(13,110,253,0.1);">
                            <i class="bi bi-circle-fill" style="color:#0D6EFD;font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="card-area-title mb-0">Camarão Vannamei</div>
                            <small class="text-muted fst-italic">Litopenaeus vannamei</small>
                        </div>
                    </div>
                    <p class="card-area-text">Espécie dominante na carcinicultura mundial. Pesquisas focam em nutrição, genética de resistência a doenças e sistemas biofloc para produção sustentável.</p>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="especie-pill"><i class="bi bi-geo-alt"></i> Carcinicultura</span>
                        <span class="especie-pill"><i class="bi bi-flask"></i> Biofloc</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-area">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="card-area-icon mb-0" style="background:rgba(111,66,193,0.1);">
                            <i class="bi bi-circle-fill" style="color:#6F42C1;font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="card-area-title mb-0">Mexilhão-perna-perna</div>
                            <small class="text-muted fst-italic">Perna perna</small>
                        </div>
                    </div>
                    <p class="card-area-text">Espécie nativa cultivada em long-lines na Baía Sul de Florianópolis. Alvo de estudos sobre qualidade microbiológica, sanidade e rastreabilidade.</p>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="especie-pill"><i class="bi bi-geo-alt"></i> Maricultura</span>
                        <span class="especie-pill"><i class="bi bi-shield-check"></i> Nativa</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-area">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="card-area-icon mb-0" style="background:rgba(230,81,0,0.1);">
                            <i class="bi bi-circle-fill" style="color:#E65100;font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="card-area-title mb-0">Vieira</div>
                            <small class="text-muted fst-italic">Nodipecten nodosus</small>
                        </div>
                    </div>
                    <p class="card-area-text">Molusco de alto valor de mercado. Pesquisas em reprodução controlada e larvicultura viabilizam o fornecimento de sementes para produtores catarinenses.</p>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="especie-pill"><i class="bi bi-geo-alt"></i> Maricultura</span>
                        <span class="especie-pill"><i class="bi bi-currency-dollar"></i> Alto valor</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-area">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="card-area-icon mb-0" style="background:rgba(25,135,84,0.1);">
                            <i class="bi bi-circle-fill" style="color:#198754;font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="card-area-title mb-0">Tilápia-do-Nilo</div>
                            <small class="text-muted fst-italic">Oreochromis niloticus</small>
                        </div>
                    </div>
                    <p class="card-area-text">Estudada em sistemas de recirculação de água (RAS) e aquaponia. Pesquisas avaliam bem-estar animal, nutrição funcional e integração com horticultura.</p>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="especie-pill"><i class="bi bi-geo-alt"></i> Piscicultura</span>
                        <span class="especie-pill"><i class="bi bi-recycle"></i> RAS</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-area">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="card-area-icon mb-0" style="background:rgba(32,201,151,0.1);">
                            <i class="bi bi-circle-fill" style="color:#20C997;font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="card-area-title mb-0">Microalgas</div>
                            <small class="text-muted fst-italic">Nannochloropsis, Chaetoceros spp.</small>
                        </div>
                    </div>
                    <p class="card-area-text">Produzidas como alimento vivo para larvas de moluscos e camarões. Também investigadas como fonte de ômega-3, pigmentos e biocombustíveis de terceira geração.</p>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="especie-pill"><i class="bi bi-geo-alt"></i> Ficicultura</span>
                        <span class="especie-pill"><i class="bi bi-lightning"></i> Biocombustível</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Linha do tempo --}}
<section class="py-5">
    <div class="container-xl">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="section-label">Histórico</div>
                <h2 class="section-title">Mais de quatro décadas de ciência aquícola</h2>
                <p class="section-text mb-4">
                    Da implantação pioneira nos anos 1980 à liderança atual em pesquisa aquícola nacional,
                    a Fazenda Ressacada consolidou-se como polo de excelência científica e transferência
                    de tecnologia para o setor produtivo.
                </p>
                <div class="info-card">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-geo-alt-fill text-principal fs-5"></i>
                        <strong style="font-size:0.9rem;">Localização</strong>
                    </div>
                    <p class="mb-1" style="font-size:0.875rem;color:#555;">Rodovia Admar Gonzaga, 1.346 — Itacorubi</p>
                    <p class="mb-0" style="font-size:0.875rem;color:#555;">Florianópolis, SC — CEP 88034-001</p>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="timeline-item">
                    <div class="timeline-dot">1</div>
                    <div class="timeline-year">1983</div>
                    <div class="timeline-title">Fundação da Estação</div>
                    <p class="timeline-text">Implantação da Estação de Aquicultura Marinha da UFSC com foco inicial no cultivo experimental de ostras e mexilhões na Baía Norte de Florianópolis.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot">2</div>
                    <div class="timeline-year">1991</div>
                    <div class="timeline-title">Laboratório de Camarões Marinhos</div>
                    <p class="timeline-text">Criação do LCM — Laboratório de Camarões Marinhos, que se tornaria referência internacional em pesquisa com camarões peneídeos e desenvolvimento do sistema Biofloc.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot">3</div>
                    <div class="timeline-year">2000</div>
                    <div class="timeline-title">Reconhecimento nacional</div>
                    <p class="timeline-text">O Departamento de Aquicultura passa a integrar o Sistema Nacional de Pesquisa Agropecuária, ampliando parcerias com Embrapa, EPAGRI e órgãos federais.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot">4</div>
                    <div class="timeline-year">2010</div>
                    <div class="timeline-title">Programa de Pós-Graduação</div>
                    <p class="timeline-text">Consolidação do PPGAQUI — Programa de Pós-Graduação em Aquicultura, com conceito CAPES 5, formando mestres e doutores para o setor produtivo e academia.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot">5</div>
                    <div class="timeline-year">Hoje</div>
                    <div class="timeline-title">Polo de Inovação Aquícola</div>
                    <p class="timeline-text">Mais de 200 pesquisadores ativos, projetos financiados por CNPq, FINEP e FAPESC, e parcerias com empresas do setor produtivo nacional e internacional.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Destaques de pesquisa --}}
<section class="py-5 bg-section-alt">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="section-label">Linhas de Pesquisa</div>
            <h2 class="section-title">O que a Fazenda Ressacada investiga</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card-area text-center">
                    <div class="card-area-icon mx-auto" style="background:rgba(13,149,113,0.1);">
                        <i class="bi bi-dna text-principal"></i>
                    </div>
                    <div class="card-area-title">Genética e Melhoramento</div>
                    <p class="card-area-text">Seleção de linhagens resistentes a patógenos e com maior eficiência de conversão alimentar.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card-area text-center">
                    <div class="card-area-icon mx-auto" style="background:rgba(13,110,253,0.1);">
                        <i class="bi bi-flask" style="color:#0D6EFD;"></i>
                    </div>
                    <div class="card-area-title">Nutrição e Alimentação</div>
                    <p class="card-area-text">Desenvolvimento de rações funcionais com ingredientes regionais sustentáveis e avaliação de aditivos naturais.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card-area text-center">
                    <div class="card-area-icon mx-auto" style="background:rgba(111,66,193,0.1);">
                        <i class="bi bi-heart-pulse" style="color:#6F42C1;"></i>
                    </div>
                    <div class="card-area-title">Sanidade Aquícola</div>
                    <p class="card-area-text">Diagnóstico e controle de enfermidades, uso racional de antibióticos e desenvolvimento de vacinas.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card-area text-center">
                    <div class="card-area-icon mx-auto" style="background:rgba(230,81,0,0.1);">
                        <i class="bi bi-tree" style="color:#E65100;"></i>
                    </div>
                    <div class="card-area-title">Sustentabilidade</div>
                    <p class="card-area-text">Avaliação de impacto ambiental, sistemas integrados multitrófico (IMTA) e certificações ambientais.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Citação --}}
<section class="py-5">
    <div class="container-xl">
        <div class="highlight-box">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <p style="font-size:1.25rem;font-weight:600;line-height:1.6;margin-bottom:1rem;position:relative;z-index:1;">
                        A Fazenda Ressacada representa a ponte entre o conhecimento gerado na universidade e a realidade do aquicultor brasileiro, formando profissionais capazes de transformar ciência em produção sustentável.
                    </p>
                    <p style="font-size:0.85rem;color:rgba(255,255,255,0.65);margin:0;">
                        — Departamento de Aquicultura · Centro de Ciências Agrárias · UFSC
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <div style="display:inline-flex;flex-direction:column;align-items:center;gap:8px;background:rgba(255,255,255,0.12);border-radius:16px;padding:24px 32px;">
                        <i class="bi bi-award-fill" style="font-size:2.5rem;color:#fff;"></i>
                        <span style="font-weight:700;font-size:1.1rem;color:#fff;">Conceito CAPES 5</span>
                        <span style="font-size:0.78rem;color:rgba(255,255,255,0.65);">Programa de Pós-Graduação</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Parceiros --}}
<section class="py-5 bg-section-alt">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="section-label">Parceiros e Fomento</div>
            <h2 class="section-title">Quem apoia a Fazenda Ressacada</h2>
        </div>
        <div class="row g-3 justify-content-center">
            @foreach([
                ['icon'=>'bi-building', 'nome'=>'UFSC', 'desc'=>'Universidade Federal de Santa Catarina'],
                ['icon'=>'bi-diagram-3', 'nome'=>'CNPq', 'desc'=>'Conselho Nacional de Pesquisa'],
                ['icon'=>'bi-bank', 'nome'=>'FAPESC', 'desc'=>'Fundação de Amparo à Pesquisa de SC'],
                ['icon'=>'bi-lightning-charge', 'nome'=>'FINEP', 'desc'=>'Financiadora de Estudos e Projetos'],
                ['icon'=>'bi-tree', 'nome'=>'EPAGRI', 'desc'=>'Empresa de Pesquisa Agropecuária de SC'],
                ['icon'=>'bi-shield', 'nome'=>'MAPA', 'desc'=>'Ministério da Agricultura, Pecuária e Abastecimento'],
            ] as $p)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="parceiro-logo">
                    <i class="bi {{ $p['icon'] }} text-principal fs-4 d-block mb-1"></i>
                    <strong>{{ $p['nome'] }}</strong>
                    <div style="font-size:0.72rem;color:#888;margin-top:2px;">{{ $p['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Localização e CTA --}}
<section class="py-5">
    <div class="container-xl">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <div class="section-label">Venha conhecer</div>
                <h2 class="section-title">Localização e Contato</h2>
                <p class="section-text mb-4">
                    A Fazenda Ressacada está aberta para visitas técnicas agendadas, recebe estagiários e
                    alunos de iniciação científica vinculados à UFSC e a outras instituições parceiras.
                </p>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                    <li class="d-flex align-items-start gap-3">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(13,149,113,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-geo-alt-fill text-principal"></i>
                        </div>
                        <div>
                            <strong style="font-size:0.875rem;">Endereço</strong><br>
                            <span style="font-size:0.85rem;color:#666;">Rodovia Admar Gonzaga, 1.346 — Bairro Ressacada<br>Florianópolis, SC — CEP 88034-001</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(13,149,113,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-globe text-principal"></i>
                        </div>
                        <div>
                            <strong style="font-size:0.875rem;">Departamento</strong><br>
                            <span style="font-size:0.85rem;color:#666;">Departamento de Aquicultura (AQI)<br>Centro de Ciências Agrárias — UFSC</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(13,149,113,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-briefcase text-principal"></i>
                        </div>
                        <div>
                            <strong style="font-size:0.875rem;">Vagas e estágios</strong><br>
                            <span style="font-size:0.85rem;color:#666;">Oportunidades vinculadas ao projeto são divulgadas neste portal.</span>
                        </div>
                    </li>
                </ul>
                <a href="{{ route('vagas.publicas.index') }}" class="btn btn-principal">
                    <i class="bi bi-briefcase me-2"></i>Ver vagas do projeto
                </a>
            </div>
            <div class="col-lg-7">
                <div class="map-placeholder">
                    <i class="bi bi-pin-map-fill text-principal" style="font-size:3rem;"></i>
                    <div style="font-weight:600;color:#0D9571;">Fazenda Ressacada — UFSC</div>
                    <div style="font-size:0.82rem;color:#888;">Florianópolis, Santa Catarina</div>
                    <a href="https://maps.google.com/?q=Fazenda+Ressacada+UFSC+Florianopolis" target="_blank" rel="noopener" class="btn btn-outline-principal btn-sm mt-1">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Abrir no Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
