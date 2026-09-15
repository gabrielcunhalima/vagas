<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\ValidaCpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Cadastro mínimo: nome, e-mail, senha, CPF e consentimento.
 *
 * O nome entra aqui porque o portal precisa chamar o candidato por ele desde o
 * primeiro acesso. Os demais dados pertencem ao perfil e são preenchidos quando ele
 * quiser — no máximo, cobrados como condição para se candidatar. O aceite do
 * código de conduta saiu daqui: é ato do processo seletivo, não da criação de conta.
 */
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
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $cpfLimpo = preg_replace('/\D/', '', $value);
                    if (strlen($cpfLimpo) !== 11 || ! $this->validarCpf($cpfLimpo)) {
                        $fail('CPF inválido.');
                    }
                },
                'unique:candidatos,cpf',
            ],
            'email' => ['required', 'email', 'max:255', 'unique:candidatos,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],

            // Obrigatório já aqui: há coleta de CPF neste mesmo passo.
            'lgpd_consentimento' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe seu nome completo.',
            'cpf.required' => 'Informe seu CPF.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.required' => 'Informe seu e-mail.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'Defina uma senha.',
            'password.confirmed' => 'As senhas não coincidem.',
            'lgpd_consentimento.accepted' => 'Você precisa aceitar os termos da LGPD para continuar.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->cpf) {
            $this->merge(['cpf' => preg_replace('/\D/', '', $this->cpf)]);
        }

        if ($this->nome) {
            $this->merge(['nome' => preg_replace('/\s+/u', ' ', trim($this->nome))]);
        }
    }
}
