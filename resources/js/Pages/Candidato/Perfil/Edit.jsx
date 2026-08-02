import { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { Download, FileText, Loader2, Save, ShieldAlert, Trash2 } from 'lucide-react';
import PublicLayout from '@/Layouts/PublicLayout';
import CurriculoDropzone from '@/components/CurriculoDropzone';
import Field from '@/components/Field';
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { maskCep, maskCpf, maskTelefone, onlyDigits } from '@/lib/cpf';
import { disponibilidades, ufs } from '@/lib/enums';
import { toDateInput } from '@/lib/format';

function Secao({ titulo, descricao, children }) {
    return (
        <section className="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
            <h2 className="text-sm font-bold tracking-tight">{titulo}</h2>
            {descricao && <p className="mt-0.5 text-xs text-muted-foreground">{descricao}</p>}
            <div className="mt-4">{children}</div>
        </section>
    );
}

export default function Edit({ candidato }) {
    const perfil = useForm({
        _method: 'put',
        nome: candidato.nome ?? '',
        email: candidato.email ?? '',
        cpf: candidato.cpf ? maskCpf(candidato.cpf) : '',
        telefone: candidato.telefone ? maskTelefone(candidato.telefone) : '',
        linkedin: candidato.linkedin ?? '',
        curso: candidato.curso ?? '',
        instituicao: candidato.instituicao ?? '',
        semestre: candidato.semestre ?? '',
        previsao_conclusao: toDateInput(candidato.previsao_conclusao),
        cep: candidato.cep ? maskCep(candidato.cep) : '',
        logradouro: candidato.logradouro ?? '',
        numero: candidato.numero ?? '',
        complemento: candidato.complemento ?? '',
        bairro: candidato.bairro ?? '',
        cidade: candidato.cidade ?? '',
        estado: candidato.estado ?? '',
        pretensao_salarial: candidato.pretensao_salarial ?? '',
        disponibilidade: candidato.disponibilidade ?? '',
        pcd: Boolean(candidato.pcd),
        pcd_tipo: candidato.pcd_tipo ?? '',
        curriculo: null,
    });

    const senha = useForm({
        senha_atual: '',
        password: '',
        password_confirmation: '',
    });

    const exclusao = useForm({
        confirmar_exclusao: false,
        password: '',
    });

    const [buscandoCep, setBuscandoCep] = useState(false);

    function salvarPerfil(e) {
        e.preventDefault();
        perfil.post(route('candidato.perfil.update'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => perfil.setData('curriculo', null),
        });
    }

    function salvarSenha(e) {
        e.preventDefault();
        senha.put(route('candidato.perfil.senha'), {
            preserveScroll: true,
            onSuccess: () => senha.reset(),
        });
    }

    function excluirConta(e) {
        e.preventDefault();
        exclusao.delete(route('candidato.excluir'));
    }

    function removerCurriculo() {
        router.delete(route('candidato.perfil.curriculo.remover'), { preserveScroll: true });
    }

    async function buscarCep() {
        const digits = onlyDigits(perfil.data.cep);
        if (digits.length !== 8) return;
        setBuscandoCep(true);
        try {
            const res = await fetch(route('api.cep', { cep: digits }));
            if (res.ok) {
                const d = await res.json();
                if (d && !d.erro) {
                    perfil.setData((prev) => ({
                        ...prev,
                        logradouro: d.logradouro || prev.logradouro,
                        bairro: d.bairro || prev.bairro,
                        cidade: d.cidade || prev.cidade,
                        estado: d.estado || prev.estado,
                    }));
                }
            }
        } catch {
            /* preenchimento manual segue disponível */
        } finally {
            setBuscandoCep(false);
        }
    }

    const e = perfil.errors;

    return (
        <PublicLayout title="Meus dados">
            <div className="mx-auto w-full max-w-3xl px-4 pt-10">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Meus dados</h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Mantenha seu perfil atualizado, ele preenche suas candidaturas automaticamente.
                        </p>
                    </div>
                    <Button asChild variant="outline" size="sm">
                        <a href={route('candidato.perfil.exportar')}>
                            <Download data-icon="inline-start" /> Exportar meus dados
                        </a>
                    </Button>
                </div>

                <form onSubmit={salvarPerfil} className="mt-7 flex flex-col gap-5">
                    <Secao titulo="Dados pessoais">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <Field label="Nome completo" htmlFor="nome" required error={e.nome} className="sm:col-span-2">
                                <Input id="nome" value={perfil.data.nome} onChange={(ev) => perfil.setData('nome', ev.target.value)} required />
                            </Field>
                            <Field label="E-mail" htmlFor="email" required error={e.email}>
                                <Input id="email" type="email" value={perfil.data.email} onChange={(ev) => perfil.setData('email', ev.target.value)} required />
                            </Field>
                            <Field label="CPF" htmlFor="cpf" required error={e.cpf}>
                                <Input
                                    id="cpf"
                                    inputMode="numeric"
                                    value={perfil.data.cpf}
                                    onChange={(ev) => perfil.setData('cpf', maskCpf(ev.target.value))}
                                    required
                                />
                            </Field>
                            <Field label="Telefone" htmlFor="telefone" error={e.telefone}>
                                <Input
                                    id="telefone"
                                    inputMode="numeric"
                                    value={perfil.data.telefone}
                                    onChange={(ev) => perfil.setData('telefone', maskTelefone(ev.target.value))}
                                    placeholder="(48) 99999-9999"
                                />
                            </Field>
                            <Field label="LinkedIn" htmlFor="linkedin" error={e.linkedin}>
                                <Input
                                    id="linkedin"
                                    type="url"
                                    value={perfil.data.linkedin}
                                    onChange={(ev) => perfil.setData('linkedin', ev.target.value)}
                                    placeholder="https://linkedin.com/in/voce"
                                />
                            </Field>
                        </div>
                    </Secao>

                    <Secao titulo="Formação">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <Field label="Curso" htmlFor="curso" error={e.curso}>
                                <Input id="curso" value={perfil.data.curso} onChange={(ev) => perfil.setData('curso', ev.target.value)} />
                            </Field>
                            <Field label="Instituição" htmlFor="instituicao" error={e.instituicao}>
                                <Input id="instituicao" value={perfil.data.instituicao} onChange={(ev) => perfil.setData('instituicao', ev.target.value)} />
                            </Field>
                            <Field label="Semestre" htmlFor="semestre" error={e.semestre}>
                                <Input id="semestre" value={perfil.data.semestre} onChange={(ev) => perfil.setData('semestre', ev.target.value)} placeholder="Ex.: 5º" />
                            </Field>
                            <Field label="Previsão de conclusão" htmlFor="previsao_conclusao" error={e.previsao_conclusao}>
                                <Input
                                    id="previsao_conclusao"
                                    type="date"
                                    value={perfil.data.previsao_conclusao}
                                    onChange={(ev) => perfil.setData('previsao_conclusao', ev.target.value)}
                                />
                            </Field>
                        </div>
                    </Secao>

                    <Secao titulo="Endereço" descricao="Informe o CEP para preenchimento automático.">
                        <div className="grid gap-4 sm:grid-cols-6">
                            <Field label="CEP" htmlFor="cep" error={e.cep} className="sm:col-span-2" hint={buscandoCep ? 'Buscando endereço…' : undefined}>
                                <Input
                                    id="cep"
                                    inputMode="numeric"
                                    value={perfil.data.cep}
                                    onChange={(ev) => perfil.setData('cep', maskCep(ev.target.value))}
                                    onBlur={buscarCep}
                                    placeholder="00000-000"
                                />
                            </Field>
                            <Field label="Cidade" htmlFor="cidade" error={e.cidade} className="sm:col-span-3">
                                <Input id="cidade" value={perfil.data.cidade} onChange={(ev) => perfil.setData('cidade', ev.target.value)} />
                            </Field>
                            <Field label="UF" htmlFor="estado" error={e.estado} className="sm:col-span-1">
                                <Select value={perfil.data.estado || undefined} onValueChange={(v) => perfil.setData('estado', v)}>
                                    <SelectTrigger id="estado" className="w-full">
                                        <SelectValue placeholder="UF" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {ufs.map((uf) => (
                                            <SelectItem key={uf} value={uf}>
                                                {uf}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field label="Bairro" htmlFor="bairro" error={e.bairro} className="sm:col-span-3">
                                <Input id="bairro" value={perfil.data.bairro} onChange={(ev) => perfil.setData('bairro', ev.target.value)} />
                            </Field>
                            <Field label="Logradouro" htmlFor="logradouro" error={e.logradouro} className="sm:col-span-3">
                                <Input id="logradouro" value={perfil.data.logradouro} onChange={(ev) => perfil.setData('logradouro', ev.target.value)} />
                            </Field>
                            <Field label="Número" htmlFor="numero" error={e.numero} className="sm:col-span-2">
                                <Input id="numero" value={perfil.data.numero} onChange={(ev) => perfil.setData('numero', ev.target.value)} />
                            </Field>
                            <Field label="Complemento" htmlFor="complemento" error={e.complemento} className="sm:col-span-4">
                                <Input id="complemento" value={perfil.data.complemento} onChange={(ev) => perfil.setData('complemento', ev.target.value)} />
                            </Field>
                        </div>
                    </Secao>

                    <Secao titulo="Preferências">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <Field label="Pretensão salarial (R$)" htmlFor="pretensao_salarial" error={e.pretensao_salarial}>
                                <Input
                                    id="pretensao_salarial"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={perfil.data.pretensao_salarial}
                                    onChange={(ev) => perfil.setData('pretensao_salarial', ev.target.value)}
                                />
                            </Field>
                            <Field label="Disponibilidade para início" htmlFor="disponibilidade" error={e.disponibilidade}>
                                <Select
                                    value={perfil.data.disponibilidade || undefined}
                                    onValueChange={(v) => perfil.setData('disponibilidade', v)}
                                >
                                    <SelectTrigger id="disponibilidade" className="w-full">
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {disponibilidades.map((d) => (
                                            <SelectItem key={d} value={d}>
                                                {d}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <div className="flex flex-col gap-3 sm:col-span-2">
                                <label className="flex items-center gap-3">
                                    <Switch checked={perfil.data.pcd} onCheckedChange={(v) => perfil.setData('pcd', Boolean(v))} />
                                    <span className="text-sm font-medium">Sou pessoa com deficiência (PcD)</span>
                                </label>
                                {perfil.data.pcd && (
                                    <Field label="Tipo de deficiência" htmlFor="pcd_tipo" error={e.pcd_tipo}>
                                        <Input id="pcd_tipo" value={perfil.data.pcd_tipo} onChange={(ev) => perfil.setData('pcd_tipo', ev.target.value)} />
                                    </Field>
                                )}
                            </div>
                        </div>
                    </Secao>

                    <Secao titulo="Currículo" descricao="Usado como padrão nas suas candidaturas.">
                        {candidato.tem_curriculo && !perfil.data.curriculo && (
                            <div className="mb-4 flex flex-wrap items-center gap-3 rounded-lg bg-muted/50 px-3 py-2.5 ring-1 ring-foreground/10">
                                <FileText className="size-4 shrink-0 text-primary" />
                                <span className="min-w-0 flex-1 truncate text-sm font-medium">
                                    {candidato.curriculo_nome_original}
                                </span>
                                <div className="flex items-center gap-1">
                                    <Button asChild variant="ghost" size="sm">
                                        <a href={route('candidato.perfil.curriculo.download')}>
                                            <Download data-icon="inline-start" /> Baixar
                                        </a>
                                    </Button>
                                    <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                            <Button type="button" variant="destructive" size="sm">
                                                <Trash2 data-icon="inline-start" /> Remover
                                            </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>Remover currículo?</AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    O arquivo será excluído do seu perfil. Candidaturas já enviadas não são
                                                    afetadas.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>Cancelar</AlertDialogCancel>
                                                <Button type="button" variant="destructive" onClick={removerCurriculo}>
                                                    Remover
                                                </Button>
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </div>
                            </div>
                        )}

                        <Field
                            error={e.curriculo}
                            hint={candidato.tem_curriculo ? 'Enviar um novo arquivo substitui o atual.' : undefined}
                        >
                            <CurriculoDropzone
                                file={perfil.data.curriculo}
                                onChange={(f) => perfil.setData('curriculo', f)}
                                error={e.curriculo}
                            />
                        </Field>
                    </Secao>

                    <Button type="submit" className="h-10 sm:self-end sm:px-8" disabled={perfil.processing}>
                        {perfil.processing ? <Loader2 className="animate-spin" data-icon="inline-start" /> : <Save data-icon="inline-start" />}
                        Salvar alterações
                    </Button>
                </form>

                {/* Alterar senha */}
                <form onSubmit={salvarSenha} className="mt-10 rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                    <h2 className="text-sm font-bold tracking-tight">Alterar senha</h2>
                    <div className="mt-4 grid gap-4 sm:grid-cols-3">
                        <Field label="Senha atual" htmlFor="senha_atual" error={senha.errors.senha_atual}>
                            <Input
                                id="senha_atual"
                                type="password"
                                value={senha.data.senha_atual}
                                onChange={(ev) => senha.setData('senha_atual', ev.target.value)}
                                autoComplete="current-password"
                                required
                            />
                        </Field>
                        <Field label="Nova senha" htmlFor="password" error={senha.errors.password}>
                            <Input
                                id="password"
                                type="password"
                                value={senha.data.password}
                                onChange={(ev) => senha.setData('password', ev.target.value)}
                                autoComplete="new-password"
                                required
                            />
                        </Field>
                        <Field label="Confirmar nova senha" htmlFor="password_confirmation" error={senha.errors.password_confirmation}>
                            <Input
                                id="password_confirmation"
                                type="password"
                                value={senha.data.password_confirmation}
                                onChange={(ev) => senha.setData('password_confirmation', ev.target.value)}
                                autoComplete="new-password"
                                required
                            />
                        </Field>
                    </div>
                    <Button type="submit" variant="outline" className="mt-4" disabled={senha.processing}>
                        {senha.processing && <Loader2 className="animate-spin" data-icon="inline-start" />}
                        Atualizar senha
                    </Button>
                </form>

                {/* Zona de perigo */}
                <div className="mt-10 rounded-xl border border-destructive/30 bg-destructive/5 p-5 sm:p-6">
                    <div className="flex items-start gap-3">
                        <ShieldAlert className="mt-0.5 size-5 shrink-0 text-destructive" />
                        <div className="flex-1">
                            <h2 className="text-sm font-bold tracking-tight text-destructive">Excluir minha conta</h2>
                            <p className="mt-1 text-sm leading-relaxed text-muted-foreground">
                                Seus dados pessoais e candidaturas serão anonimizados de forma irreversível, conforme a
                                LGPD.
                            </p>

                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button type="button" variant="destructive" size="sm" className="mt-4">
                                        <Trash2 data-icon="inline-start" /> Excluir conta
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <form onSubmit={excluirConta}>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Excluir conta permanentemente?</AlertDialogTitle>
                                            <AlertDialogDescription>
                                                Esta ação é irreversível. Seus dados e candidaturas serão anonimizados e
                                                você perderá o acesso à conta.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>

                                        <div className="mt-4 flex flex-col gap-4">
                                            <label className="flex items-start gap-2.5">
                                                <Checkbox
                                                    checked={exclusao.data.confirmar_exclusao}
                                                    onCheckedChange={(v) => exclusao.setData('confirmar_exclusao', Boolean(v))}
                                                    className="mt-0.5"
                                                />
                                                <span className="text-sm">Entendo que esta ação não pode ser desfeita.</span>
                                            </label>
                                            {exclusao.errors.confirmar_exclusao && (
                                                <p className="text-xs font-medium text-destructive">
                                                    {exclusao.errors.confirmar_exclusao}
                                                </p>
                                            )}
                                            <Field label="Confirme sua senha" htmlFor="senha_exclusao" error={exclusao.errors.password}>
                                                <Input
                                                    id="senha_exclusao"
                                                    type="password"
                                                    value={exclusao.data.password}
                                                    onChange={(ev) => exclusao.setData('password', ev.target.value)}
                                                    autoComplete="current-password"
                                                    required
                                                />
                                            </Field>
                                        </div>

                                        <AlertDialogFooter className="mt-5">
                                            <AlertDialogCancel type="button">Cancelar</AlertDialogCancel>
                                            <Button type="submit" variant="destructive" disabled={exclusao.processing}>
                                                {exclusao.processing && <Loader2 className="animate-spin" data-icon="inline-start" />}
                                                Excluir definitivamente
                                            </Button>
                                        </AlertDialogFooter>
                                    </form>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
