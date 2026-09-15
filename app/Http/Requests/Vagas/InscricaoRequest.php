<?php

namespace App\Http\Requests\Vagas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Só o que é próprio desta inscrição.
 *
 * Identidade, contato, formação, endereço e currículo pertencem ao perfil e são
 * validados lá; aqui a exigência é que o perfil esteja completo, verificada no
 * controller. O que sobra são os campos que mudam a cada vaga: a carta, a
 * declaração de conflito de interesse e o aceite da Política de Privacidade.
 */
class InscricaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('candidato')->check();
    }

    public function rules(): array
    {
        return [
            '_honeypot' => 'present|max:0',

            'carta_apresentacao' => 'nullable|string|max:5000',

            'conflito_interesse' => 'required|boolean',
            'conflito_interesse_detalhe' => 'required_if:conflito_interesse,1|nullable|string|max:2000',

            'politica_privacidade_aceite' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            '_honeypot.max' => 'Envio inválido.',
            'conflito_interesse.required' => 'Informe se você tem vínculo com alguém da equipe desta vaga.',
            'conflito_interesse_detalhe.required_if' => 'Detalhe a relação informada.',
            'politica_privacidade_aceite.accepted' => 'Você precisa aceitar a Política de Privacidade da FAPEU.',
        ];
    }
}
