<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\ValidaCpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CandidatoRegistroRequest extends FormRequest
{
    use ValidaCpf;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Etapa 1 — Credenciais
            'cpf' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $cpfLimpo = preg_replace('/\D/', '', $value);
                    if (strlen($cpfLimpo) !== 11 || !$this->validarCpf($cpfLimpo)) {
                        $fail('CPF inválido.');
                    }
                },
                'unique:candidatos,cpf',
            ],
            'email'    => ['required', 'email', 'max:255', 'unique:candidatos,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],

            // Etapa 2 — Dados pessoais
            'nome'          => ['required', 'string', 'max:255'],
            'nome_social'   => ['nullable', 'string', 'max:255'],
            'nacionalidade' => ['required', 'string', 'max:100'],
            'telefone'      => ['nullable', 'string', 'max:20'],

            // Etapa 3 — Endereço
            'cep'         => ['nullable', 'string', 'max:9'],
            'estado'      => ['nullable', 'string', 'size:2'],
            'cidade'      => ['nullable', 'string', 'max:100'],
            'bairro'      => ['nullable', 'string', 'max:100'],
            'logradouro'  => ['nullable', 'string', 'max:200'],
            'numero'      => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],

            // Etapa 4 — Formação
            'nivel_escolaridade' => ['required', 'string', 'max:50'],
            'situacao_curso'     => ['required', 'in:cursando,concluido'],
            'curso'              => ['required', 'string', 'max:200'],
            'instituicao'        => ['required', 'string', 'max:200'],
            'semestre'           => ['required_if:situacao_curso,cursando', 'nullable', 'string', 'max:10'],
            'previsao_conclusao' => ['required', 'date'],
            'curriculo'          => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            // Etapa 5 — Acessibilidade
            'possui_acessibilidade'   => ['required', 'boolean'],
            'acessibilidade_detalhe'  => ['required_if:possui_acessibilidade,1', 'nullable', 'string', 'max:2000'],

            // Etapa 6 — Conflito de interesse + aceites
            'conflito_interesse'         => ['required', 'boolean'],
            'conflito_interesse_detalhe' => ['required_if:conflito_interesse,1', 'nullable', 'string', 'max:2000'],
            'codigo_conduta_aceite'      => ['accepted'],
            'lgpd_consentimento'         => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required'                   => 'Informe seu CPF.',
            'cpf.unique'                      => 'Este CPF já está cadastrado.',
            'email.required'                  => 'Informe seu e-mail.',
            'email.unique'                     => 'Este e-mail já está cadastrado.',
            'password.required'               => 'Defina uma senha.',
            'password.confirmed'               => 'As senhas não coincidem.',
            'nome.required'                    => 'Informe seu nome completo.',
            'nacionalidade.required'           => 'Informe sua nacionalidade.',
            'nivel_escolaridade.required'      => 'Selecione seu nível de escolaridade.',
            'situacao_curso.required'          => 'Informe a situação do seu curso.',
            'curso.required'                   => 'Informe seu curso.',
            'instituicao.required'             => 'Informe sua instituição de ensino.',
            'semestre.required_if'             => 'Informe o semestre atual.',
            'previsao_conclusao.required'      => 'Informe a data de conclusão (ou previsão).',
            'curriculo.mimes'                  => 'O currículo deve ser um arquivo PDF.',
            'curriculo.max'                    => 'O currículo não pode exceder 5MB.',
            'acessibilidade_detalhe.required_if' => 'Descreva a acessibilidade necessária.',
            'conflito_interesse_detalhe.required_if' => 'Detalhe a relação informada.',
            'codigo_conduta_aceite.accepted'   => 'Você precisa aceitar o código de conduta da FAPEU.',
            'lgpd_consentimento.accepted'      => 'Você precisa aceitar os termos da LGPD para continuar.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->cpf) {
            $this->merge(['cpf' => preg_replace('/\D/', '', $this->cpf)]);
        }
    }
}
