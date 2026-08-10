import { Link } from '@inertiajs/react';
import {
    Award,
    Briefcase,
    Building2,
    Dna,
    Droplets,
    ExternalLink,
    Fish,
    FlaskConical,
    Flower2,
    Globe,
    GraduationCap,
    HeartPulse,
    Landmark,
    MapPin,
    Network,
    Shield,
    Sprout,
    Trees,
    Waves,
    Zap,
} from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

const CORES = {
    verde: 'bg-emerald-500/12 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
    azul: 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
    violeta: 'bg-violet-500/12 text-violet-700 dark:bg-violet-400/10 dark:text-violet-300',
    laranja: 'bg-orange-500/12 text-orange-700 dark:bg-orange-400/10 dark:text-orange-300',
    teal: 'bg-teal-500/12 text-teal-700 dark:bg-teal-400/10 dark:text-teal-300',
};

function SectionLabel({ children }) {
    return (
        <div className="text-xs font-bold uppercase tracking-widest text-primary">{children}</div>
    );
}

function SectionTitle({ children, className }) {
    return <h2 className={cn('mt-2 text-2xl font-bold tracking-tight sm:text-3xl', className)}>{children}</h2>;
}

function CardArea({ icon: Icon, cor, titulo, children, centro = false }) {
    return (
        <div className={cn('rounded-xl bg-card p-5 ring-1 ring-foreground/10', centro && 'text-center')}>
            <div className={cn('flex size-10 items-center justify-center rounded-lg', cor, centro && 'mx-auto')}>
                <Icon className="size-5" />
            </div>
            <h3 className="mt-3 text-sm font-bold">{titulo}</h3>
            <p className="mt-1.5 text-sm leading-relaxed text-muted-foreground">{children}</p>
        </div>
    );
}

const ESPECIES = [
    {
        nome: 'Ostra-do-Pacífico',
        cientifico: 'Crassostrea gigas',
        cor: CORES.verde,
        texto: 'Principal espécie de maricultura do estado. Santa Catarina responde por mais de 95% da produção nacional, e a UFSC contribui com genética e manejo de ponta.',
        pills: ['Maricultura', 'Espécie-chave'],
    },
    {
        nome: 'Camarão Vannamei',
        cientifico: 'Litopenaeus vannamei',
        cor: CORES.azul,
        texto: 'Espécie dominante na carcinicultura mundial. Pesquisas focam em nutrição, genética de resistência a doenças e sistemas biofloc para produção sustentável.',
        pills: ['Carcinicultura', 'Biofloc'],
    },
    {
        nome: 'Mexilhão-perna-perna',
        cientifico: 'Perna perna',
        cor: CORES.violeta,
        texto: 'Espécie nativa cultivada em long-lines na Baía Sul de Florianópolis. Alvo de estudos sobre qualidade microbiológica, sanidade e rastreabilidade.',
        pills: ['Maricultura', 'Nativa'],
    },
    {
        nome: 'Vieira',
        cientifico: 'Nodipecten nodosus',
        cor: CORES.laranja,
        texto: 'Molusco de alto valor de mercado. Pesquisas em reprodução controlada e larvicultura viabilizam o fornecimento de sementes para produtores catarinenses.',
        pills: ['Maricultura', 'Alto valor'],
    },
    {
        nome: 'Tilápia-do-Nilo',
        cientifico: 'Oreochromis niloticus',
        cor: CORES.verde,
        texto: 'Estudada em sistemas de recirculação de água (RAS) e aquaponia. Pesquisas avaliam bem-estar animal, nutrição funcional e integração com horticultura.',
        pills: ['Piscicultura', 'RAS'],
    },
    {
        nome: 'Microalgas',
        cientifico: 'Nannochloropsis, Chaetoceros spp.',
        cor: CORES.teal,
        texto: 'Produzidas como alimento vivo para larvas de moluscos e camarões. Também investigadas como fonte de ômega-3, pigmentos e biocombustíveis de terceira geração.',
        pills: ['Ficicultura', 'Biocombustível'],
    },
];

const TIMELINE = [
    {
        ano: '1983',
        titulo: 'Fundação da Estação',
        texto: 'Implantação da Estação de Aquicultura Marinha da UFSC com foco inicial no cultivo experimental de ostras e mexilhões na Baía Norte de Florianópolis.',
    },
    {
        ano: '1991',
        titulo: 'Laboratório de Camarões Marinhos',
        texto: 'Criação do LCM, Laboratório de Camarões Marinhos, que se tornaria referência internacional em pesquisa com camarões peneídeos e desenvolvimento do sistema Biofloc.',
    },
    {
        ano: '2000',
        titulo: 'Reconhecimento nacional',
        texto: 'O Departamento de Aquicultura passa a integrar o Sistema Nacional de Pesquisa Agropecuária, ampliando parcerias com Embrapa, EPAGRI e órgãos federais.',
    },
    {
        ano: '2010',
        titulo: 'Programa de Pós-Graduação',
        texto: 'Consolidação do PPGAQUI, Programa de Pós-Graduação em Aquicultura, com conceito CAPES 5, formando mestres e doutores para o setor produtivo e academia.',
    },
    {
        ano: 'Hoje',
        titulo: 'Polo de Inovação Aquícola',
        texto: 'Mais de 200 pesquisadores ativos, projetos financiados por CNPq, FINEP e FAPESC, e parcerias com empresas do setor produtivo nacional e internacional.',
    },
];

const PARCEIROS = [
    { icon: Building2, nome: 'UFSC', desc: 'Universidade Federal de Santa Catarina' },
    { icon: Network, nome: 'CNPq', desc: 'Conselho Nacional de Pesquisa' },
    { icon: Landmark, nome: 'FAPESC', desc: 'Fundação de Amparo à Pesquisa de SC' },
    { icon: Zap, nome: 'FINEP', desc: 'Financiadora de Estudos e Projetos' },
    { icon: Sprout, nome: 'EPAGRI', desc: 'Empresa de Pesquisa Agropecuária de SC' },
    { icon: Shield, nome: 'MAPA', desc: 'Ministério da Agricultura, Pecuária e Abastecimento' },
];

export default function FazendaRessacada() {
    return (
        <PublicLayout title="Fazenda Ressacada: Projeto UFSC">
            {/* Hero */}
            <section className="bg-brand-deep">
                <div className="mx-auto w-full max-w-6xl px-4 py-16 lg:py-20">
                    <div className="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-white">
                        <GraduationCap className="size-3.5" /> Projeto Universitário · UFSC
                    </div>
                    <h1 className="mt-5 text-4xl font-bold tracking-tight text-white sm:text-5xl">Fazenda Ressacada</h1>
                    <p className="mt-4 max-w-xl text-sm leading-relaxed text-white/80 sm:text-base">
                        Estação de Aquicultura da Universidade Federal de Santa Catarina, referência nacional em
                        pesquisa, ensino e extensão no cultivo sustentável de organismos aquáticos.
                    </p>
                    <div className="mt-8 flex flex-wrap gap-3">
                        {[
                            ['+40', 'Anos de pesquisa'],
                            ['12 ha', 'Área total'],
                            ['6', 'Espécies cultivadas'],
                            ['200+', 'Pesquisadores'],
                        ].map(([num, label]) => (
                            <div
                                key={label}
                                className="min-w-28 rounded-xl border border-white/15 bg-white/10 px-5 py-3 text-center"
                            >
                                <div className="text-xl font-bold text-white">{num}</div>
                                <div className="mt-0.5 text-[0.825rem] text-white/65">{label}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Sobre */}
            <section className="mx-auto w-full max-w-6xl px-4 py-14">
                <div className="grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <SectionLabel>Sobre o projeto</SectionLabel>
                        <SectionTitle>Uma estação de referência em aquicultura sustentável</SectionTitle>
                        <div className="mt-4 flex flex-col gap-3 text-sm leading-relaxed text-muted-foreground">
                            <p>
                                Localizada em Florianópolis, a Fazenda Ressacada é uma Unidade de Pesquisa, Ensino e
                                Extensão vinculada ao Centro de Ciências Agrárias (CCA) da Universidade Federal de
                                Santa Catarina (UFSC).
                            </p>
                            <p>
                                Fundada na década de 1980, a estação atua como laboratório vivo onde estudantes de
                                graduação, pós-graduação e pesquisadores desenvolvem estudos sobre o cultivo de
                                camarões, ostras, mexilhões, peixes e algas marinhas, contribuindo para o
                                desenvolvimento científico e tecnológico da aquicultura brasileira.
                            </p>
                            <p>
                                Além da pesquisa, a Fazenda Ressacada promove ações de extensão junto às comunidades de
                                maricultores e aquicultores de Santa Catarina, transferindo tecnologia e boas práticas
                                que fortalecem a produção regional.
                            </p>
                        </div>
                    </div>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <CardArea icon={Droplets} cor={CORES.verde} titulo="Maricultura">
                            Cultivo de ostras, mexilhões e vieiras em ambiente marinho controlado.
                        </CardArea>
                        <CardArea icon={Waves} cor={CORES.azul} titulo="Carcinicultura">
                            Pesquisa com camarão-branco do Pacífico em sistemas intensivos e superintensivos.
                        </CardArea>
                        <CardArea icon={Fish} cor={CORES.violeta} titulo="Piscicultura">
                            Estudos com espécies nativas e exóticas em sistemas de recirculação de água (RAS).
                        </CardArea>
                        <CardArea icon={Flower2} cor={CORES.laranja} titulo="Algas Marinhas">
                            Cultivo de microalgas e macroalgas para alimentação, biocombustíveis e cosméticos.
                        </CardArea>
                    </div>
                </div>
            </section>

            {/* Espécies */}
            <section className="border-y bg-card/50">
                <div className="mx-auto w-full max-w-6xl px-4 py-14">
                    <div className="mx-auto max-w-xl text-center">
                        <SectionLabel>Biodiversidade</SectionLabel>
                        <SectionTitle>Espécies cultivadas na Fazenda</SectionTitle>
                        <p className="mt-3 text-sm text-muted-foreground">
                            A estação mantém cultivos ativos de diversas espécies, integrando produção, pesquisa e
                            material didático para as turmas do AQI.
                        </p>
                    </div>
                    <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {ESPECIES.map((e) => (
                            <div key={e.nome} className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                                <h3 className="text-sm font-bold">{e.nome}</h3>
                                <p className="text-xs italic text-muted-foreground">{e.cientifico}</p>
                                <p className="mt-2.5 text-sm leading-relaxed text-muted-foreground">{e.texto}</p>
                                <div className="mt-3 flex flex-wrap gap-1.5">
                                    {e.pills.map((p) => (
                                        <span
                                            key={p}
                                            className={cn('rounded-full px-2.5 py-0.5 text-[0.825rem] font-semibold', e.cor)}
                                        >
                                            {p}
                                        </span>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Timeline */}
            <section className="mx-auto w-full max-w-6xl px-4 py-14">
                <div className="grid gap-10 lg:grid-cols-[2fr_3fr]">
                    <div>
                        <SectionLabel>Histórico</SectionLabel>
                        <SectionTitle>Mais de quatro décadas de ciência aquícola</SectionTitle>
                        <p className="mt-4 text-sm leading-relaxed text-muted-foreground">
                            Da implantação pioneira nos anos 1980 à liderança atual em pesquisa aquícola nacional, a
                            Fazenda Ressacada consolidou-se como polo de excelência científica e transferência de
                            tecnologia para o setor produtivo.
                        </p>
                        <div className="mt-6 rounded-xl bg-card p-4 ring-1 ring-foreground/10">
                            <div className="flex items-center gap-2 text-sm font-semibold">
                                <MapPin className="size-4 text-primary" /> Localização
                            </div>
                            <p className="mt-1.5 text-sm text-muted-foreground">
                                Rodovia Admar Gonzaga, 1.346, Itacorubi
                                <br />
                                Florianópolis, SC, CEP 88034-001
                            </p>
                        </div>
                    </div>
                    <ol className="relative flex flex-col gap-7 border-l pl-6">
                        {TIMELINE.map((t, i) => (
                            <li key={t.ano} className="relative">
                                <span className="absolute -left-[31px] flex size-2.5 items-center justify-center rounded-full bg-primary ring-4 ring-background" />
                                <div className="text-xs font-bold uppercase tracking-wide text-primary">{t.ano}</div>
                                <h3 className="mt-0.5 text-sm font-bold">{t.titulo}</h3>
                                <p className="mt-1 text-sm leading-relaxed text-muted-foreground">{t.texto}</p>
                            </li>
                        ))}
                    </ol>
                </div>
            </section>

            {/* Linhas de pesquisa */}
            <section className="border-y bg-card/50">
                <div className="mx-auto w-full max-w-6xl px-4 py-14">
                    <div className="text-center">
                        <SectionLabel>Linhas de pesquisa</SectionLabel>
                        <SectionTitle>O que a Fazenda Ressacada investiga</SectionTitle>
                    </div>
                    <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <CardArea icon={Dna} cor={CORES.verde} titulo="Genética e Melhoramento" centro>
                            Seleção de linhagens resistentes a patógenos e com maior eficiência de conversão alimentar.
                        </CardArea>
                        <CardArea icon={FlaskConical} cor={CORES.azul} titulo="Nutrição e Alimentação" centro>
                            Desenvolvimento de rações funcionais com ingredientes regionais sustentáveis e avaliação de
                            aditivos naturais.
                        </CardArea>
                        <CardArea icon={HeartPulse} cor={CORES.violeta} titulo="Sanidade Aquícola" centro>
                            Diagnóstico e controle de enfermidades, uso racional de antibióticos e desenvolvimento de
                            vacinas.
                        </CardArea>
                        <CardArea icon={Trees} cor={CORES.laranja} titulo="Sustentabilidade" centro>
                            Avaliação de impacto ambiental, sistemas integrados multitróficos (IMTA) e certificações
                            ambientais.
                        </CardArea>
                    </div>
                </div>
            </section>

            {/* Citação */}
            <section className="mx-auto w-full max-w-6xl px-4 py-14">
                <div className="grid items-center gap-8 rounded-2xl bg-brand-deep p-8 sm:p-10 lg:grid-cols-[3fr_1fr]">
                    <div>
                        <p className="text-lg font-semibold leading-relaxed text-white sm:text-xl">
                            “A Fazenda Ressacada representa a ponte entre o conhecimento gerado na universidade e a
                            realidade do aquicultor brasileiro, formando profissionais capazes de transformar ciência
                            em produção sustentável.”
                        </p>
                        <p className="mt-3 text-sm text-white/65">
                            Departamento de Aquicultura · Centro de Ciências Agrárias · UFSC
                        </p>
                    </div>
                    <div className="flex flex-col items-center gap-2 rounded-xl bg-white/10 px-6 py-6 text-center">
                        <Award className="size-9 text-white" />
                        <span className="text-base font-bold text-white">Conceito CAPES 5</span>
                        <span className="text-xs text-white/65">Programa de Pós-Graduação</span>
                    </div>
                </div>
            </section>

            {/* Parceiros */}
            <section className="border-y bg-card/50">
                <div className="mx-auto w-full max-w-6xl px-4 py-14">
                    <div className="text-center">
                        <SectionLabel>Parceiros e fomento</SectionLabel>
                        <SectionTitle>Quem apoia a Fazenda Ressacada</SectionTitle>
                    </div>
                    <div className="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                        {PARCEIROS.map((p) => (
                            <div key={p.nome} className="rounded-xl bg-card p-4 text-center ring-1 ring-foreground/10">
                                <p.icon className="mx-auto size-6 text-primary" />
                                <div className="mt-2 text-sm font-bold">{p.nome}</div>
                                <div className="mt-0.5 text-[0.825rem] leading-snug text-muted-foreground">{p.desc}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Localização + CTA */}
            <section className="mx-auto w-full max-w-6xl px-4 py-14">
                <div className="grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <SectionLabel>Venha conhecer</SectionLabel>
                        <SectionTitle>Localização e contato</SectionTitle>
                        <p className="mt-4 text-sm leading-relaxed text-muted-foreground">
                            A Fazenda Ressacada está aberta para visitas técnicas agendadas, recebe estagiários e
                            alunos de iniciação científica vinculados à UFSC e a outras instituições parceiras.
                        </p>
                        <ul className="mt-6 flex flex-col gap-4">
                            {[
                                [MapPin, 'Endereço', 'Rodovia Admar Gonzaga, 1.346, Bairro Ressacada\nFlorianópolis, SC, CEP 88034-001'],
                                [Globe, 'Departamento', 'Departamento de Aquicultura (AQI)\nCentro de Ciências Agrárias, UFSC'],
                                [Briefcase, 'Vagas e estágios', 'Oportunidades vinculadas ao projeto são divulgadas neste portal.'],
                            ].map(([Icon, titulo, texto]) => (
                                <li key={titulo} className="flex items-start gap-3">
                                    <div className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-accent">
                                        <Icon className="size-4 text-accent-foreground" />
                                    </div>
                                    <div>
                                        <div className="text-sm font-semibold">{titulo}</div>
                                        <p className="whitespace-pre-line text-sm text-muted-foreground">{texto}</p>
                                    </div>
                                </li>
                            ))}
                        </ul>
                        <Button asChild className="mt-7">
                            <Link href={route('vagas.publicas.index')}>
                                <Briefcase data-icon="inline-start" /> Ver vagas do projeto
                            </Link>
                        </Button>
                    </div>
                    <div className="flex flex-col items-center gap-2 rounded-2xl border border-dashed p-10 text-center">
                        <MapPin className="size-10 text-primary" />
                        <div className="font-semibold">Fazenda Ressacada, UFSC</div>
                        <div className="text-sm text-muted-foreground">Florianópolis, Santa Catarina</div>
                        <Button asChild variant="outline" size="sm" className="mt-2">
                            <a
                                href="https://maps.google.com/?q=Fazenda+Ressacada+UFSC+Florianopolis"
                                target="_blank"
                                rel="noreferrer"
                            >
                                <ExternalLink data-icon="inline-start" /> Abrir no Google Maps
                            </a>
                        </Button>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
