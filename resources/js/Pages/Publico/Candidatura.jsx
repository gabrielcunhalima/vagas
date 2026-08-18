import { Link, useForm } from '@inertiajs/react';
import { CalendarDays, FileText, Loader2, MapPin, PencilLine, Send, TriangleAlert } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import Field from '@/components/Field';
import { TipoAdmissaoBadge } from '@/components/badges';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Progress } from '@/components/ui/progress';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { niveisEscolaridade } from '@/lib/enums';
import { diasRestantes, faixaSalarial, formatDate, localVaga, prazoInscricao } from '@/lib/format';

function Secao({ titulo, descricao, children }) {
    return (
        <section className="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
            <h2 className="text-sm font-bold tracking-tight">{titulo}</h2>
            {descricao && <p className="mt-0.5 text-xs text-muted-foreground">{descricao}</p>}
            <div className="mt-4">{children}</div>
        </section>
    );
}

function Dado({ rotulo, valor }) {
    if (!valor) return null;
    return (
        <div className="min-w-0">
            <dt className="text-xs text-muted-foreground">{rotulo}</dt>
            <dd className="truncate text-sm font-medium">{valor}</dd>
        </div>
    );
}

function TextoLivre({ rotulo, valor }) {
    if (!valor) return null;
    return (
        <div className="min-w-0 sm:col-span-2">
            <dt className="text-xs text-muted-foreground">{rotulo}</dt>
            <dd className="mt-0.5 whitespace-pre-line text-sm font-medium">{valor}</dd>
        </div>
    );
}

function Formacoes({ formacoes }) {
    if (!formacoes?.length) return null;
    return (
        <div className="min-w-0 sm:col-span-2">
            <dt className="text-xs text-muted-foreground">Formação</dt>
            <dd className="mt-1 flex flex-col gap-2">
                {formacoes.map((formacao, i) => (
                    <div key={i} className="text-sm">
                        <span className="font-medium">{formacao.curso}</span>
                        {formacao.instituicao && <span className="text-muted-foreground"> — {formacao.instituicao}</span>}
                        <div className="text-xs text-muted-foreground">
                            {[
                                niveisEscolaridade[formacao.nivel_escolaridade] || formacao.nivel_escolaridade,
                                formacao.situacao_curso === 'cursando'
                                    ? `Cursando${formacao.semestre ? ` — ${formacao.semestre}` : ''}`
                                    : formacao.situacao_curso === 'concluido'
                                      ? 'Concluído'
                                      : null,
                                formacao.previsao_conclusao ? formatDate(formacao.previsao_conclusao) : null,
                            ]
                                .filter(Boolean)
                                .join(' · ')}
                        </div>
                    </div>
                ))}
            </dd>
        </div>
    );
}

/*
 * Perfil incompleto não tem ficha para o RH avaliar, então a inscrição
 * não segue. A vaga fica identificada aqui para que completar o perfil não custe
 * reencontrá-la depois.
 */
function PerfilIncompleto({ completude, vaga }) {
    const pendentes = Object.values(completude.pendencias);

    return (
        <div className="rounded-xl bg-card p-5 ring-1 ring-amber-500/30 sm:p-6">
            <div className="flex items-start gap-3">
                <TriangleAlert className="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-500" />
                <div className="min-w-0">
                    <h2 className="text-sm font-bold tracking-tight">Complete seu perfil para se candidatar</h2>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Faltam {pendentes.length} {pendentes.length === 1 ? 'informação' : 'informações'} para você
                        concorrer a <span className="font-medium text-foreground">{vaga.titulo}</span>.
                    </p>
                </div>
            </div>

            <Progress value={completude.progresso} className="mt-4 h-1.5" />

            <ul className="mt-3 flex flex-wrap gap-1.5">
                {pendentes.map((rotulo) => (
                    <li key={rotulo} className="rounded-full bg-muted px-2.5 py-1 text-xs font-medium text-muted-foreground">
                        {rotulo}
                    </li>
                ))}
            </ul>

            <Button asChild className="mt-5">
                <Link href={route('candidato.perfil.edit')}>Completar meu perfil</Link>
            </Button>
        </div>
    );
}

export default function Candidatura({ vaga, perfil, completude }) {
    const { data, setData, post, processing, errors } = useForm({
        _honeypot: '',
        carta_apresentacao: '',
        conflito_interesse: '',
        conflito_interesse_detalhe: '',
        codigo_conduta_aceite: false,
    });

    const dias = diasRestantes(vaga.data_encerramento);
    const prazo = prazoInscricao(vaga.data_encerramento);
    const endereco = perfil.cidade && perfil.estado ? `${perfil.cidade}/${perfil.estado}` : perfil.cidade;

    function submit(e) {
        e.preventDefault();
        post(route('inscricao.store', vaga.id));
    }

    return (
        <PublicLayout title={`Candidatura: ${vaga.titulo}`}>
            <div className="mx-auto w-full max-w-6xl px-4 pt-8">
                <h1 className="text-2xl font-bold tracking-tight">Candidatar-se</h1>
                <p className="mt-1 text-sm text-muted-foreground">
                    Confira os dados que o RH vai receber e responda às perguntas desta vaga.
                </p>

                <div className="mt-6 grid items-start gap-6 lg:grid-cols-[1fr_320px]">
                    {completude.completo ? (
                        <form onSubmit={submit} className="flex min-w-0 flex-col gap-5">
                            {/* Honeypot anti-spam: invisível para humanos */}
                            <input
                                type="text"
                                name="_honeypot"
                                value={data._honeypot}
                                onChange={(e) => setData('_honeypot', e.target.value)}
                                className="absolute -left-[9999px] size-px opacity-0"
                                tabIndex={-1}
                                autoComplete="off"
                                aria-hidden="true"
                            />

                            <Secao
                                titulo="Seus dados"
                                descricao="É isto que o RH desta vaga vai ver. Alterar aqui altera os dados da sua conta e vale para todas as suas candidaturas."
                            >
                                <dl className="grid gap-4 sm:grid-cols-2">
                                    <Dado rotulo="Nome completo" valor={perfil.nome} />
                                    <Dado rotulo="Nome social" valor={perfil.nome_social} />
                                    <Dado rotulo="E-mail" valor={perfil.email} />
                                    <Dado rotulo="CPF" valor={perfil.cpf_formatado} />
                                    <Dado rotulo="Telefone" valor={perfil.telefone} />
                                    <Dado rotulo="Nacionalidade" valor={perfil.nacionalidade} />
                                    <Dado rotulo="Cidade" valor={endereco} />
                                    <Dado rotulo="LinkedIn" valor={perfil.linkedin} />
                                    <Dado rotulo="Disponibilidade" valor={perfil.disponibilidade} />
                                    <Formacoes formacoes={perfil.formacoes} />
                                    <TextoLivre rotulo="Outras formações reconhecidas pelo MEC" valor={perfil.outras_formacoes_mec} />
                                    <TextoLivre rotulo="Outros cursos, palestras, etc." valor={perfil.outros_cursos} />
                                </dl>

                                <div className="mt-5 flex flex-wrap items-center justify-between gap-3 border-t pt-4">
                                    <span className="inline-flex min-w-0 items-center gap-2 text-sm">
                                        <FileText className="size-4 shrink-0 text-primary" />
                                        <span className="truncate font-medium">{perfil.curriculo_nome}</span>
                                    </span>
                                    <Button asChild variant="outline" size="sm">
                                        <Link href={route('candidato.perfil.edit')}>
                                            <PencilLine data-icon="inline-start" /> Editar meus dados
                                        </Link>
                                    </Button>
                                </div>
                            </Secao>

                            <Secao titulo="Sobre esta vaga">
                                <div className="grid gap-4">
                                    <Field
                                        label="Carta de apresentação"
                                        htmlFor="carta_apresentacao"
                                        error={errors.carta_apresentacao}
                                        hint="Opcional. Conte por que você se interessou por esta vaga."
                                    >
                                        <Textarea
                                            id="carta_apresentacao"
                                            rows={5}
                                            value={data.carta_apresentacao}
                                            onChange={(e) => setData('carta_apresentacao', e.target.value)}
                                        />
                                    </Field>

                                    <Field
                                        label="Você tem vínculo de parentesco ou relação pessoal com alguém da equipe desta vaga?"
                                        htmlFor="conflito_interesse"
                                        required
                                        error={errors.conflito_interesse}
                                    >
                                        <Select
                                            value={data.conflito_interesse}
                                            onValueChange={(v) => setData('conflito_interesse', v)}
                                        >
                                            <SelectTrigger id="conflito_interesse">
                                                <SelectValue placeholder="Selecione" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="0">Não</SelectItem>
                                                <SelectItem value="1">Sim</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </Field>

                                    {data.conflito_interesse === '1' && (
                                        <Field
                                            label="Descreva a relação"
                                            htmlFor="conflito_interesse_detalhe"
                                            required
                                            error={errors.conflito_interesse_detalhe}
                                        >
                                            <Textarea
                                                id="conflito_interesse_detalhe"
                                                rows={3}
                                                value={data.conflito_interesse_detalhe}
                                                onChange={(e) => setData('conflito_interesse_detalhe', e.target.value)}
                                            />
                                        </Field>
                                    )}
                                </div>
                            </Secao>

                            <div className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                                <label className="flex items-start gap-2.5">
                                    <Checkbox
                                        checked={data.codigo_conduta_aceite}
                                        onCheckedChange={(v) => setData('codigo_conduta_aceite', Boolean(v))}
                                        className="mt-0.5"
                                    />
                                    <span className="text-sm leading-relaxed">
                                        Li e aceito o{' '}
                                        <a
                                            href="https://fapeu.org.br/codigoconduta"
                                            target="_blank"
                                            rel="noreferrer"
                                            className="font-semibold text-primary hover:underline"
                                        >
                                            Código de Conduta da FAPEU
                                        </a>{' '}
                                        para este processo seletivo. <span className="text-destructive">*</span>
                                    </span>
                                </label>
                                {errors.codigo_conduta_aceite && (
                                    <p className="mt-2 text-xs font-medium text-destructive">
                                        {errors.codigo_conduta_aceite}
                                    </p>
                                )}
                            </div>

                            <Button
                                type="submit"
                                size="lg"
                                className="h-11 w-full gap-2 px-6 sm:w-auto sm:self-end"
                                disabled={processing}
                            >
                                {processing ? <Loader2 className="size-4 animate-spin" /> : <Send className="size-4" />}
                                Enviar candidatura
                            </Button>
                        </form>
                    ) : (
                        <PerfilIncompleto completude={completude} vaga={vaga} />
                    )}

                    {/* Resumo da vaga */}
                    <aside className="order-first lg:order-none lg:sticky lg:top-20">
                        <div className="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                            <div className="flex flex-wrap gap-2">
                                <TipoAdmissaoBadge tipo={vaga.tipo} codigo={vaga.tipo_codigo} />
                            </div>
                            <h2 className="mt-3 font-semibold leading-snug">{vaga.titulo}</h2>
                            <div className="mt-3 flex flex-col gap-2 text-sm text-muted-foreground">
                                {localVaga(vaga) && (
                                    <span className="inline-flex items-center gap-2">
                                        <MapPin className="size-4" />
                                        {localVaga(vaga)}
                                    </span>
                                )}
                                {vaga.projeto_nome && (
                                    <span className="line-clamp-2 text-xs">Projeto — {vaga.projeto_nome}</span>
                                )}
                                <span className="inline-flex items-center gap-2">
                                    <CalendarDays className="size-4" />
                                    {dias <= 5 ? <span className="font-semibold text-primary">{prazo}</span> : prazo}
                                </span>
                                <span className="font-medium text-foreground">{faixaSalarial(vaga)}</span>
                            </div>
                            <Link
                                href={route('vagas.publicas.show', vaga.id)}
                                className="mt-4 inline-block text-xs font-medium text-primary hover:underline"
                            >
                                Ver descrição completa da vaga
                            </Link>
                        </div>
                    </aside>
                </div>
            </div>
        </PublicLayout>
    );
}
