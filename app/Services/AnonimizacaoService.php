<?php

namespace App\Services;

use App\Models\Vagas\Candidatura;
use Illuminate\Support\Facades\Storage;

class AnonimizacaoService
{
    public function anonimizarCandidatura(Candidatura $candidatura): void
    {
        if ($candidatura->curriculo_path) {
            Storage::disk('local')->delete($candidatura->curriculo_path);
        }

        $candidatura->update([
            'nome'                    => 'Candidato excluído',
            'email'                   => 'removido_' . $candidatura->id . '@removido.invalid',
            'cpf'                     => substr(hash('sha256', $candidatura->cpf . $candidatura->id), 0, 14),
            'telefone'                => null,
            'linkedin'                => null,
            'carta_apresentacao'      => null,
            'cep'                     => null,
            'logradouro'              => null,
            'numero'                  => null,
            'complemento'             => null,
            'bairro'                  => null,
            'cidade'                  => null,
            'estado'                  => null,
            'curriculo_path'          => null,
            'curriculo_nome_original' => null,
        ]);
    }
}
