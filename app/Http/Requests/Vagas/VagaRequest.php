<?php

namespace App\Http\Requests\Vagas;

use Illuminate\Foundation\Http\FormRequest;

class VagaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'titulo'                => 'required|string|min:5|max:200',
            'descricao'             => 'required|string|min:20',
            'requisitos'            => 'required|string|min:10',
            'requisitos_desejaveis' => 'nullable|string|max:3000',
            'beneficios'            => 'nullable|string|max:2000',
            'tipo'                  => 'required|in:estagio,emprego,bolsa',
            'area'                  => 'required|in:' . implode(',', \App\Models\Vagas\Vaga::$areas),
            'curso_desejado'        => 'nullable|array',
            'curso_desejado.*'      => 'string|max:200',
            'remuneracao'           => 'nullable|numeric|min:0|max:99999.99',
            'remuneracao_max'       => 'nullable|numeric|min:0|max:99999.99',
            'carga_horaria'         => 'nullable|integer|min:1|max:44',
            'modalidade'            => 'required|in:presencial,remoto,hibrido',
            'cep'                   => 'nullable|string|max:9',
            'logradouro'            => 'nullable|string|max:200',
            'numero'                => 'nullable|string|max:20',
            'complemento'           => 'nullable|string|max:100',
            'bairro'                => 'nullable|string|max:100',
            'cidade'                => 'nullable|string|max:100',
            'estado'                => 'nullable|string|size:2',
            'pais'                  => 'nullable|string|max:50',
            'local_trabalho'        => 'nullable|string|max:200',
            'data_encerramento'     => 'required|date|after:today',
            'notificar_email'       => 'boolean',
            'projeto_nome'          => 'nullable|string|max:200',
            'projeto_codigo'        => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'            => 'Informe o título da vaga.',
            'titulo.min'                 => 'O título deve ter pelo menos 5 caracteres.',
            'descricao.required'         => 'Informe a descrição da vaga.',
            'descricao.min'              => 'A descrição deve ter pelo menos 20 caracteres.',
            'requisitos.required'        => 'Informe os requisitos da vaga.',
            'tipo.required'              => 'Selecione o tipo da vaga.',
            'tipo.in'                    => 'Tipo inválido.',
            'area.required'              => 'Informe a área da vaga.',
            'modalidade.required'        => 'Selecione a modalidade.',
            'modalidade.in'              => 'Modalidade inválida.',
            'data_encerramento.required' => 'Informe a data de encerramento.',
            'data_encerramento.after'    => 'A data de encerramento deve ser futura.',
            'remuneracao.numeric'        => 'Remuneração deve ser um valor numérico.',
            'carga_horaria.integer'      => 'Carga horária deve ser um número inteiro.',
            'carga_horaria.max'          => 'Carga horária não pode exceder 44 horas.',
        ];
    }
}
