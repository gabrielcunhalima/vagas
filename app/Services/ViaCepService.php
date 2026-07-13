<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ViaCepService
{
    private const ENDPOINT = 'https://viacep.com.br/ws';
    private const TIMEOUT  = 5;
    private const CACHE_TTL = 86400;

    public function buscar(string $cep): ?array
    {
        $cepLimpo = preg_replace('/\D/', '', $cep);

        if (strlen($cepLimpo) !== 8) {
            return null;
        }

        return Cache::remember("viacep:{$cepLimpo}", self::CACHE_TTL, function () use ($cepLimpo) {
            try {
                $resposta = Http::timeout(self::TIMEOUT)
                    ->get(self::ENDPOINT . "/{$cepLimpo}/json/");

                if (!$resposta->successful()) {
                    return null;
                }

                $dados = $resposta->json();

                if (!is_array($dados) || !empty($dados['erro'])) {
                    return null;
                }

                return [
                    'cep'         => $dados['cep']        ?? $cepLimpo,
                    'logradouro'  => $dados['logradouro'] ?? '',
                    'complemento' => $dados['complemento'] ?? '',
                    'bairro'      => $dados['bairro']     ?? '',
                    'cidade'      => $dados['localidade'] ?? '',
                    'estado'      => $dados['uf']         ?? '',
                    'pais'        => 'Brasil',
                ];
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
