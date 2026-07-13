<?php

namespace App\Http\Controllers\Candidato;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use App\Services\AnonimizacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function __construct(private AnonimizacaoService $anonimizacao) {}

    private function candidato(): Candidato
    {
        return Auth::guard('candidato')->user();
    }

    public function edit()
    {
        return view('candidato.perfil.edit', ['candidato' => $this->candidato()]);
    }

    public function update(Request $request)
    {
        $candidato = $this->candidato();

        $dados = $request->validate([
            'nome'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', Rule::unique('candidatos')->ignore($candidato->id)],
            'cpf'                => ['required', 'string', 'size:14', Rule::unique('candidatos')->ignore($candidato->id)],
            'telefone'           => ['nullable', 'string', 'max:20'],
            'linkedin'           => ['nullable', 'url', 'max:255'],
            'curso'              => ['nullable', 'string', 'max:255'],
            'instituicao'        => ['nullable', 'string', 'max:255'],
            'semestre'           => ['nullable', 'string', 'max:10'],
            'previsao_conclusao' => ['nullable', 'date'],
            'cep'                => ['nullable', 'string', 'max:9'],
            'logradouro'         => ['nullable', 'string', 'max:255'],
            'numero'             => ['nullable', 'string', 'max:20'],
            'complemento'        => ['nullable', 'string', 'max:255'],
            'bairro'             => ['nullable', 'string', 'max:255'],
            'cidade'             => ['nullable', 'string', 'max:255'],
            'estado'             => ['nullable', 'string', 'size:2'],
            'pretensao_salarial' => ['nullable', 'numeric', 'min:0'],
            'disponibilidade'    => ['nullable', 'string', 'max:50'],
            'pcd'                => ['nullable', 'boolean'],
            'pcd_tipo'           => ['nullable', 'string', 'max:100'],
            'curriculo'          => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        if ($request->hasFile('curriculo')) {
            if ($candidato->curriculo_path) {
                Storage::disk('local')->delete($candidato->curriculo_path);
            }
            $arquivo = $request->file('curriculo');
            $dados['curriculo_path']          = $arquivo->store('candidatos/curriculos', 'local');
            $dados['curriculo_nome_original'] = $arquivo->getClientOriginalName();
        }

        $dados['cpf'] = preg_replace('/\D/', '', $dados['cpf']);
        $dados['pcd'] = $request->boolean('pcd');

        unset($dados['curriculo']);

        $candidato->update($dados);

        return back()->with('success', 'Dados atualizados com sucesso!');
    }

    public function updateSenha(Request $request)
    {
        $candidato = $this->candidato();

        $request->validate([
            'senha_atual'  => ['required'],
            'password'     => ['required', 'confirmed', Password::min(8)],
        ], [
            'senha_atual.required'  => 'Informe sua senha atual.',
            'password.required'     => 'Informe a nova senha.',
            'password.confirmed'    => 'As senhas não coincidem.',
            'password.min'          => 'A nova senha deve ter no mínimo 8 caracteres.',
        ]);

        if (!Hash::check($request->senha_atual, $candidato->password)) {
            return back()->withErrors(['senha_atual' => 'Senha atual incorreta.']);
        }

        $candidato->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Senha alterada com sucesso!');
    }

    public function removerCurriculo(Request $request)
    {
        $candidato = $this->candidato();

        if ($candidato->curriculo_path) {
            Storage::disk('local')->delete($candidato->curriculo_path);
            $candidato->update([
                'curriculo_path'          => null,
                'curriculo_nome_original' => null,
            ]);
        }

        return back()->with('success', 'Currículo removido.');
    }

    public function downloadCurriculo()
    {
        $candidato = $this->candidato();

        abort_unless($candidato->temCurriculo(), 404, 'Currículo não encontrado.');

        return Storage::disk('local')->download(
            $candidato->curriculo_path,
            $candidato->curriculo_nome_original ?? 'curriculo.pdf'
        );
    }

    public function exportarDados()
    {
        $candidato = $this->candidato();
        $candidato->load(['candidaturas.vaga']);

        $dados = [
            'exportado_em' => now()->toIso8601String(),
            'perfil' => $candidato->only([
                'id', 'nome', 'nome_social', 'nacionalidade', 'email', 'cpf', 'telefone', 'linkedin',
                'curso', 'instituicao', 'nivel_escolaridade', 'situacao_curso', 'semestre', 'previsao_conclusao',
                'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'pais',
                'pretensao_salarial', 'disponibilidade', 'pcd', 'pcd_tipo',
                'possui_acessibilidade', 'acessibilidade_detalhe',
                'conflito_interesse', 'conflito_interesse_detalhe',
                'curriculo_nome_original', 'lgpd_consentimento', 'lgpd_consentimento_em',
                'codigo_conduta_aceito_em', 'created_at',
            ]),
            'candidaturas' => $candidato->candidaturas->map(fn ($c) => [
                'vaga'                   => $c->vaga?->titulo,
                'status'                 => $c->statusLabel,
                'enviada_em'             => $c->created_at?->toIso8601String(),
                'curriculo_nome_original' => $c->curriculo_nome_original,
                'carta_apresentacao'     => $c->carta_apresentacao,
                'entrevista_data'        => $c->entrevista_data?->toIso8601String(),
                'entrevista_local'       => $c->entrevista_local,
            ]),
        ];

        $nomeArquivo = 'meus-dados-' . now()->format('Y-m-d') . '.json';

        return response()->json($dados, 200, [
            'Content-Disposition' => "attachment; filename=\"{$nomeArquivo}\"",
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function excluirConta(Request $request)
    {
        $request->validate([
            'confirmar_exclusao' => ['accepted'],
            'password'           => ['required'],
        ], [
            'confirmar_exclusao.accepted' => 'Confirme que deseja excluir sua conta.',
            'password.required'           => 'Informe sua senha para confirmar.',
        ]);

        $candidato = $this->candidato();

        if (!Hash::check($request->password, $candidato->password)) {
            return back()->withErrors(['password' => 'Senha incorreta.']);
        }

        Auth::guard('candidato')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        foreach ($candidato->candidaturas as $candidatura) {
            $this->anonimizacao->anonimizarCandidatura($candidatura);
        }

        if ($candidato->curriculo_path) {
            Storage::disk('local')->delete($candidato->curriculo_path);
        }

        $anonimo = 'excluido_' . $candidato->id . '_' . substr(hash('sha256', $candidato->id . $candidato->cpf), 0, 16);

        $candidato->update([
            'nome'                       => 'Candidato excluído',
            'nome_social'                => null,
            'email'                      => $anonimo . '@removido.invalid',
            'cpf'                        => substr(hash('sha256', $candidato->cpf), 0, 14),
            'telefone'                   => null,
            'linkedin'                   => null,
            'cep'                        => null,
            'logradouro'                 => null,
            'numero'                     => null,
            'complemento'                => null,
            'bairro'                     => null,
            'cidade'                     => null,
            'estado'                     => null,
            'acessibilidade_detalhe'     => null,
            'conflito_interesse_detalhe' => null,
            'curriculo_path'             => null,
            'curriculo_nome_original'    => null,
            'lgpd_consentimento'         => false,
            'ativo'                      => false,
        ]);
        $candidato->delete();

        return redirect()->route('home')
            ->with('success', 'Sua conta foi excluída. Seus dados foram anonimizados conforme a LGPD.');
    }
}
