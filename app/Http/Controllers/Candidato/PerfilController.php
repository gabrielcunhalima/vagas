<?php

namespace App\Http\Controllers\Candidato;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use App\Services\AnonimizacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    private function formacoesParaFrontend(Candidato $candidato): array
    {
        return $candidato->formacoes->map(fn ($f) => [
            'nivel_escolaridade' => $f->nivel_escolaridade,
            'situacao_curso' => $f->situacao_curso,
            'curso' => $f->curso,
            'instituicao' => $f->instituicao,
            'semestre' => $f->semestre,
            'previsao_conclusao' => $f->previsao_conclusao?->format('Y-m-d'),
        ])->values()->all();
    }

    public function edit()
    {
        $candidato = $this->candidato();
        $candidato->load('formacoes');

        return view('candidato.perfil.edit', [
            'candidato' => array_merge($candidato->only([
                'nome', 'nome_social', 'nacionalidade', 'email', 'cpf', 'telefone', 'linkedin',
                'outras_formacoes_mec', 'outros_cursos',
                'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
                'pretensao_salarial', 'disponibilidade', 'pcd', 'pcd_tipo',
                'created_at',
            ]), [
                'formacoes' => $this->formacoesParaFrontend($candidato),
                'tem_curriculo' => $candidato->temCurriculo(),
                'curriculo_nome_original' => $candidato->curriculoAtual?->nome_original,
                'curriculo_expira_em' => $candidato->curriculoAtual?->expiraEm()->format('d/m/Y'),
                'possui_acessibilidade' => $candidato->possui_acessibilidade,
                'acessibilidade_detalhe' => $candidato->acessibilidade_detalhe,
            ]),
        ]);
    }

    public function update(Request $request)
    {
        $candidato = $this->candidato();

        /*
         * Nada aqui é obrigatório: o perfil pode ficar incompleto pelo tempo que o
         * candidato quiser. O que estes campos precisam garantir é o formato — a
         * exigência de preenchimento é do gate da candidatura, e o critério de
         * completude mora em Candidato::CAMPOS_OBRIGATORIOS.
         */
        $dados = $request->validate([
            'nome' => ['nullable', 'string', 'max:255'],
            'nome_social' => ['nullable', 'string', 'max:255'],
            'nacionalidade' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('candidatos')->ignore($candidato->id)],
            'cpf' => ['required', 'string', 'size:14', Rule::unique('candidatos')->ignore($candidato->id)],
            'telefone' => ['nullable', 'string', 'max:20'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'formacoes' => ['nullable', 'array'],
            'formacoes.*.nivel_escolaridade' => ['nullable', 'string', 'max:50'],
            'formacoes.*.situacao_curso' => ['nullable', 'in:cursando,concluido'],
            'formacoes.*.curso' => ['nullable', 'string', 'max:255'],
            'formacoes.*.instituicao' => ['nullable', 'string', 'max:255'],
            'formacoes.*.semestre' => ['nullable', 'string', 'max:10'],
            'formacoes.*.previsao_conclusao' => ['nullable', 'date'],
            'outras_formacoes_mec' => ['nullable', 'string', 'max:2000'],
            'outros_cursos' => ['nullable', 'string', 'max:2000'],
            'possui_acessibilidade' => ['nullable', 'boolean'],
            'acessibilidade_detalhe' => ['nullable', 'string', 'max:2000', 'required_if:possui_acessibilidade,1'],
            'cep' => ['nullable', 'string', 'max:9'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
            'pretensao_salarial' => ['nullable', 'numeric', 'min:0'],
            'disponibilidade' => ['nullable', 'string', 'max:50'],
            'pcd' => ['nullable', 'boolean'],
            'pcd_tipo' => ['nullable', 'string', 'max:100'],
            'curriculo' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        // Versão nova em vez de sobrescrita: a anterior precisa continuar
        // identificável pelos eventos dos processos que a julgaram.
        if ($request->hasFile('curriculo')) {
            $candidato->adicionarCurriculo($request->file('curriculo'));
        }

        $dados['cpf'] = preg_replace('/\D/', '', $dados['cpf']);
        $dados['pcd'] = $request->boolean('pcd');

        // Vazio é "ainda não respondeu" e precisa continuar nulo — do contrário
        // o cast para boolean gravaria "não" e a pendência sumiria sem resposta.
        $dados['possui_acessibilidade'] = $request->filled('possui_acessibilidade')
            ? $request->boolean('possui_acessibilidade')
            : null;

        $formacoes = $dados['formacoes'] ?? [];
        unset($dados['curriculo'], $dados['formacoes']);

        DB::transaction(function () use ($candidato, $dados, $formacoes) {
            $candidato->update($dados);

            // Substitui a lista inteira a cada envio: nenhuma outra tabela
            // referencia uma formação específica por id, então recriar é
            // mais simples e seguro do que casar por id enviado.
            $candidato->formacoes()->delete();

            foreach ($formacoes as $formacao) {
                if (collect($formacao)->filter(fn ($valor) => filled($valor))->isEmpty()) {
                    continue;
                }

                $candidato->formacoes()->create($formacao);
            }
        });

        // Mexer no perfil é uso da conta tanto quanto entrar nela.
        $candidato->registrarAtividade();

        return back()->with('success', 'Dados atualizados com sucesso!');
    }

    public function updateSenha(Request $request)
    {
        $candidato = $this->candidato();

        $request->validate([
            'senha_atual' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'senha_atual.required' => 'Informe sua senha atual.',
            'password.required' => 'Informe a nova senha.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min' => 'A nova senha deve ter no mínimo 8 caracteres, com maiúscula, minúscula, número e símbolo.',
        ]);

        if (! Hash::check($request->senha_atual, $candidato->password)) {
            return back()->withErrors(['senha_atual' => 'Senha atual incorreta.']);
        }

        $candidato->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Senha alterada com sucesso!');
    }

    public function removerCurriculo(Request $request)
    {
        $candidato = $this->candidato();

        // O arquivo permanece armazenado; só deixa de ser a versão vigente.
        $candidato->removerCurriculoAtual();

        return back()->with('success', 'Currículo removido. Seu perfil ficou incompleto.');
    }

    public function downloadCurriculo()
    {
        $candidato = $this->candidato();

        abort_unless($candidato->temCurriculo(), 404, 'Currículo não encontrado.');

        $versao = $candidato->curriculoAtual;

        // O arquivo não é alcançável pela web: a raiz do disco fica fora do
        // diretório servido, e este é o único caminho de leitura.
        return Storage::disk(Candidato::DISCO_CURRICULOS)->download(
            $versao->path,
            $versao->nome_original ?? 'curriculo.pdf'
        );
    }

    /**
     * O mesmo arquivo do download, mas para abrir no navegador: o visualizador
     * de "Meus dados" carrega esta rota num iframe.
     */
    public function visualizarCurriculo()
    {
        $candidato = $this->candidato();

        abort_unless($candidato->temCurriculo(), 404, 'Currículo não encontrado.');

        $versao = $candidato->curriculoAtual;

        return Storage::disk(Candidato::DISCO_CURRICULOS)->response(
            $versao->path,
            $versao->nome_original ?? 'curriculo.pdf',
            ['Content-Type' => 'application/pdf'],
            'inline'
        );
    }

    public function exportarDados()
    {
        $candidato = $this->candidato();
        $candidato->load(['candidaturas.vaga', 'curriculos', 'formacoes']);

        $dados = [
            'exportado_em' => now()->toIso8601String(),
            'perfil' => array_merge($candidato->only([
                'id', 'nome', 'nome_social', 'nacionalidade', 'email', 'cpf', 'telefone', 'linkedin',
                'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'pais',
                'pretensao_salarial', 'disponibilidade', 'pcd', 'pcd_tipo',
                'possui_acessibilidade', 'acessibilidade_detalhe',
                'outras_formacoes_mec', 'outros_cursos',
                'lgpd_consentimento', 'lgpd_consentimento_em', 'created_at',
            ]), [
                'formacoes' => $this->formacoesParaFrontend($candidato),
            ]),
            // Uma entrada por versão: exportar só a vigente esconderia currículos
            // que a pessoa enviou e que ainda constam de processos anteriores.
            'curriculos' => $candidato->curriculos->map(fn ($cv) => [
                'nome_original' => $cv->nome_original,
                'enviado_em' => $cv->enviado_em?->toIso8601String(),
                'vigente' => $cv->id === $candidato->curriculo_atual_id,
            ]),
            'candidaturas' => $candidato->candidaturas->map(fn ($c) => [
                'vaga' => $c->vaga?->titulo,
                'status' => $c->statusLabel,
                'enviada_em' => $c->created_at?->toIso8601String(),
                'carta_apresentacao' => $c->carta_apresentacao,
                'conflito_interesse' => $c->conflito_interesse,
                'conflito_interesse_detalhe' => $c->conflito_interesse_detalhe,
                'codigo_conduta_aceito_em' => $c->codigo_conduta_aceito_em?->toIso8601String(),
                'entrevista_data' => $c->entrevista_data?->toIso8601String(),
                'entrevista_local' => $c->entrevista_local,
            ]),
        ];

        $nomeArquivo = 'meus-dados-'.now()->format('Y-m-d').'.json';

        return response()->json($dados, 200, [
            'Content-Disposition' => "attachment; filename=\"{$nomeArquivo}\"",
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function excluirConta(Request $request)
    {
        $request->validate([
            'confirmar_exclusao' => ['accepted'],
            'password' => ['required'],
        ], [
            'confirmar_exclusao.accepted' => 'Confirme que deseja excluir sua conta.',
            'password.required' => 'Informe sua senha para confirmar.',
        ]);

        $candidato = $this->candidato();

        if (! Hash::check($request->password, $candidato->password)) {
            return back()->withErrors(['password' => 'Senha incorreta.']);
        }

        Auth::guard('candidato')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Uma chamada, um lugar: as candidaturas leem do perfil, então não há
        // cópia a percorrer. Elas permanecem como registro de processo.
        $this->anonimizacao->anonimizarCandidato($candidato);

        return redirect()->route('home')
            ->with('success', 'Sua conta foi excluída. Seus dados foram anonimizados conforme a LGPD.');
    }
}
