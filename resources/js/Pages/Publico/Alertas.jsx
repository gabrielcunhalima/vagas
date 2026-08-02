import { Link, useForm } from '@inertiajs/react';
import { BellRing, Loader2 } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import Field from '@/components/Field';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';

function GrupoCheckbox({ titulo, descricao, opcoes, selecionados, onToggle, colunas = 'sm:grid-cols-3' }) {
    return (
        <div>
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

export default function Alertas({ areas, modalidades, tipos, email }) {
    const emailFixo = Boolean(email);

    const { data, setData, post, processing, errors, reset } = useForm({
        email: email ?? '',
        areas: [],
        tipos: [],
        modalidades: [],
        lgpd_consentimento: false,
    });

    function toggle(campo, valor) {
        const atual = data[campo];
        setData(campo, atual.includes(valor) ? atual.filter((v) => v !== valor) : [...atual, valor]);
    }

    function submit(e) {
        e.preventDefault();
        post(route('alertas.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <PublicLayout title="Alertas de vagas">
            <div className="mx-auto w-full max-w-2xl px-4 pt-10">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Alertas de vagas</h1>
                    <p className="text-sm text-muted-foreground">
                        Receba por e-mail as novas vagas do seu interesse, assim que forem publicadas.
                    </p>
                </div>

                <form onSubmit={submit} className="mt-7 flex flex-col gap-6 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
                    <Field label="Seu e-mail" htmlFor="email" required error={errors.email}>
                        <Input
                            id="email"
                            type="email"
                            className="h-10 disabled:opacity-100"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="seu@email.com"
                            required
                            disabled={emailFixo}
                        />
                    </Field>

                    <GrupoCheckbox
                        titulo="Áreas de interesse"
                        descricao="Deixe em branco para receber vagas de todas as áreas."
                        opcoes={areas.map((a) => [a, a])}
                        selecionados={data.areas}
                        onToggle={(v) => toggle('areas', v)}
                        colunas="sm:grid-cols-2"
                    />

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
                    />

                    <div className="border-t pt-5">
                        <label className="flex items-start gap-2.5">
                            <Checkbox
                                checked={data.lgpd_consentimento}
                                onCheckedChange={(v) => setData('lgpd_consentimento', Boolean(v))}
                                className="mt-0.5"
                            />
                            <span className="text-sm leading-relaxed">
                                Autorizo o uso do meu e-mail para envio de alertas de vagas, conforme a{' '}
                                <Link href={route('politica.privacidade')} className="font-semibold text-primary hover:underline">
                                    Política de Privacidade
                                </Link>
                                . Posso cancelar a qualquer momento pelo link presente em cada e-mail.{' '}
                                <span className="text-destructive">*</span>
                            </span>
                        </label>
                        {errors.lgpd_consentimento && (
                            <p className="mt-2 text-xs font-medium text-destructive">{errors.lgpd_consentimento}</p>
                        )}
                    </div>

                    <Button type="submit" className="h-10" disabled={processing}>
                        {processing ? <Loader2 className="animate-spin" data-icon="inline-start" /> : <BellRing data-icon="inline-start" />}
                        Ativar alertas
                    </Button>
                </form>
            </div>
        </PublicLayout>
    );
}
