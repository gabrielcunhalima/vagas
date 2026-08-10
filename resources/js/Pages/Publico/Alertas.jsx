import { Link, useForm } from '@inertiajs/react';
import { BellRing, Loader2, Mail } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { CONTAINER_LARGO } from '@/lib/layout';

function GrupoCheckbox({ titulo, descricao, opcoes, selecionados, onToggle, colunas = 'sm:grid-cols-3', className = '' }) {
    return (
        <div className={className}>
            <h3 className="text-sm font-semibold">{titulo}</h3>
            {descricao && <p className="mt-0.5 text-xs text-muted-foreground">{descricao}</p>}
            <div className={`mt-3 grid grid-cols-2 gap-2 ${colunas}`}>
                {opcoes.map(([valor, label]) => {
                    const ativo = selecionados.includes(valor);
                    return (
                        <label
                            key={valor}
                            className={`flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition-colors ${
                                ativo ? 'border-primary bg-accent/50 font-medium' : 'border-input hover:bg-muted/50'
                            }`}
                        >
                            <Checkbox checked={ativo} onCheckedChange={() => onToggle(valor)} />
                            {label}
                        </label>
                    );
                })}
            </div>
        </div>
    );
}

export default function Alertas({ areas, modalidades, tipos, email, alerta }) {
    const { data, setData, post, processing } = useForm({
        areas: alerta?.areas ?? [],
        tipos: alerta?.tipos ?? [],
        modalidades: alerta?.modalidades ?? [],
    });

    function toggle(campo, valor) {
        const atual = data[campo];
        setData(campo, atual.includes(valor) ? atual.filter((v) => v !== valor) : [...atual, valor]);
    }

    function submit(e) {
        e.preventDefault();
        post(route('alertas.store'), { preserveScroll: true });
    }

    return (
        <PublicLayout title="Alertas de vagas">
            <div className={`${CONTAINER_LARGO} py-10`}>
                {/* A ordem dos blocos aqui (identificação → preferências → envio) é a ordem
                    empilhada abaixo de xl e a ordem de tabulação: o teclado passa pelas
                    preferências antes de chegar ao envio. Em xl são as classes col-start/row-start
                    que remontam as duas colunas — não mexa na ordem do JSX para ajustar o visual. */}
                <form
                    onSubmit={submit}
                    className="grid gap-x-10 gap-y-8 rounded-xl bg-card p-6 ring-1 ring-foreground/10 xl:grid-cols-[320px_minmax(0,1fr)] xl:grid-rows-[auto_1fr] xl:p-8"
                >
                    {/* A — identificação */}
                    <div className="flex flex-col gap-6 xl:col-start-1 xl:row-start-1">
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">Alertas de vagas</h1>
                            <p className="mt-1 text-sm text-muted-foreground">
                                Receba por e-mail as novas vagas do seu interesse, assim que forem publicadas.
                            </p>
                        </div>

                        {/* Destino não é escolhido: é o e-mail da conta, e acompanha a troca dela.
                            Permanece visível junto do envio para ninguém confirmar às cegas. */}
                        <div>
                            <span className="text-sm font-medium">Enviaremos para</span>
                            <p className="mt-1 flex items-center gap-2 rounded-lg bg-muted px-3 py-2 text-sm">
                                <Mail className="size-4 shrink-0 text-muted-foreground" />
                                <span className="truncate">{email}</span>
                            </p>
                            <p className="mt-1.5 text-xs text-muted-foreground">
                                Para receber em outro endereço, altere o e-mail em{' '}
                                <Link href={route('candidato.perfil.edit')} className="font-medium text-primary hover:underline">
                                    Meus dados
                                </Link>
                                .
                            </p>
                        </div>
                    </div>

                    {/* B — preferências. Atravessa as duas linhas em xl, então o border-l vira um
                        filete contínuo entre as áreas (separa conteúdo, não marca seleção). */}
                    <div className="flex flex-col gap-6 xl:col-start-2 xl:row-span-2 xl:row-start-1 xl:border-l xl:pl-10">
                        <GrupoCheckbox
                            titulo="Áreas de interesse"
                            descricao="Deixe em branco para receber vagas de todas as áreas."
                            opcoes={areas.map((a) => [a, a])}
                            selecionados={data.areas}
                            onToggle={(v) => toggle('areas', v)}
                            colunas="sm:grid-cols-2 xl:grid-cols-3"
                        />

                        {/* Tipos e modalidades têm 3 opções cada: lado a lado fecham o bloco na altura
                            das áreas, em vez de deixarem duas linhas quase vazias. O filete só existe
                            quando estão lado a lado — empilhados, o gap já separa os dois. */}
                        <div className="grid gap-6 sm:grid-cols-2 sm:gap-8">
                            <GrupoCheckbox
                                titulo="Tipos de vaga"
                                opcoes={Object.entries(tipos)}
                                selecionados={data.tipos}
                                onToggle={(v) => toggle('tipos', v)}
                            />

                            <GrupoCheckbox
                                titulo="Modalidades"
                                opcoes={Object.entries(modalidades)}
                                selecionados={data.modalidades}
                                onToggle={(v) => toggle('modalidades', v)}
                                className="sm:border-l sm:pl-8"
                            />
                        </div>
                    </div>

                    {/* C — envio. O bloco estica até a altura de B (grid-rows auto_1fr), e o sticky
                        vai no wrapper interno: um item de grid esticado não teria curso.
                        O consentimento saiu daqui: já foi concedido na criação da conta. */}
                    <div className="xl:col-start-1 xl:row-start-2">
                        <div className="flex flex-col gap-5 border-t pt-6 xl:sticky xl:top-20 xl:border-t-0 xl:pt-0">
                            <Button type="submit" className="h-10" disabled={processing}>
                                {processing ? (
                                    <Loader2 className="animate-spin" data-icon="inline-start" />
                                ) : (
                                    <BellRing data-icon="inline-start" />
                                )}
                                {alerta?.ativo ? 'Salvar preferências' : 'Ativar alertas'}
                            </Button>

                            <p className="text-xs text-muted-foreground">
                                Você pode cancelar a qualquer momento pelo link presente em cada e-mail, sem precisar
                                entrar na conta.
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </PublicLayout>
    );
}
