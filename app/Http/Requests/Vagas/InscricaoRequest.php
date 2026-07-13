<?php

namespace App\Http\Requests\Vagas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Vagas\Candidatura;
use App\Http\Requests\Concerns\ValidaCpf;

class InscricaoRequest extends FormRequest
{
    use ValidaCpf;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vagaId = $this->route('vaga')->id;

        return [
            '_honeypot'          => 'present|max:0',

            'nome'               => 'required|string|min:3|max:200',
            'email'              => 'required|email|max:255',
            'cpf'                => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($vagaId) {
                    $cpfLimpo = preg_replace('/\D/', '', $value);
                    if (strlen($cpfLimpo) !== 11 || !$this->validarCpf($cpfLimpo)) {
                        $fail('CPF inválido.');
                        return;
                    }
                    $existe = Candidatura::where('vaga_id', $vagaId)
                        ->where('cpf', $cpfLimpo)
                        ->exists();
                    if ($existe) {
                        $fail('Você já se candidatou a esta vaga.');
                    }
                },
            ],
            'telefone'           => 'nullable|string|max:20',
            'curso'              => 'required|string|min:3|max:200',
            'instituicao'        => 'required|string|min:2|max:200',
            'semestre'           => 'nullable|string|max:10',
            'previsao_conclusao' => 'nullable|date|after:today',
            'carta_apresentacao' => 'nullable|string|max:5000',

            'cep'                => 'nullable|string|max:9',
            'logradouro'         => 'nullable|string|max:200',
            'numero'             => 'nullable|string|max:20',
            'complemento'        => 'nullable|string|max:100',
            'bairro'             => 'nullable|string|max:100',
            'cidade'             => 'nullable|string|max:100',
            'estado'             => 'nullable|string|size:2',
            'pais'               => 'nullable|string|max:50',

            // Currículo opcional se candidato logado já possui um
            'curriculo'          => $this->curriculoObrigatorio()
                                    ? 'required|file|mimes:pdf|max:5120'
                                    : 'nullable|file|mimes:pdf|max:5120',

            'linkedin'           => 'nullable|url|max:255',
            'pretensao_salarial' => 'nullable|numeric|min:0|max:99999.99',
            'disponibilidade'    => 'nullable|string|max:50',
            'pcd'                => 'nullable|boolean',
            'pcd_tipo'           => 'nullable|string|max:100',

            // Consentimento LGPD — exigido apenas para visitantes não-logados
            'lgpd_consentimento' => Auth::guard('candidato')->check() ? 'nullable' : 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            '_honeypot.max'            => 'Envio inválido.',
            'nome.required'            => 'Informe seu nome completo.',
            'email.required'           => 'Informe seu e-mail.',
            'email.email'              => 'E-mail inválido.',
            'cpf.required'             => 'Informe seu CPF.',
            'curso.required'           => 'Informe seu curso.',
            'instituicao.required'     => 'Informe sua instituição de ensino.',
            'previsao_conclusao.after' => 'A previsão de conclusão deve ser futura.',
            'curriculo.required'              => 'Anexe seu currículo em PDF.',
            'curriculo.mimes'                 => 'O currículo deve ser um arquivo PDF.',
            'curriculo.max'                   => 'O currículo não pode exceder 5MB.',
            'lgpd_consentimento.accepted'     => 'Você precisa autorizar o uso dos seus dados para continuar.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->cpf) {
            $this->merge(['cpf' => preg_replace('/\D/', '', $this->cpf)]);
        }
    }

    private function curriculoObrigatorio(): bool
    {
        $candidato = Auth::guard('candidato')->user();
        // Obrigatório se não há candidato logado ou o candidato não tem currículo no perfil
        return !$candidato || !$candidato->temCurriculo();
    }
}
