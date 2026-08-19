<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ViaCepService
{
    private const ENDPOINT = 'https://viacep.com.br/ws';

    private const TIMEOUT = 5;

    private const CACHE_TTL = 86400;

    public function buscar(string $cep): ?array
    {
        $cepLimpo = preg_replace('/\D/', '', $cep);

        if (strlen($cepLimpo) !== 8) {
            return null;
        }

        $cacheKey = "viacep:{$cepLimpo}";

        $cacheado = Cache::get($cacheKey);
        if ($cacheado !== null) {
            return $cacheado;
        }

        try {
            $resposta = Http::timeout(self::TIMEOUT)
                ->get(self::ENDPOINT."/{$cepLimpo}/json/");

            if (! $resposta->successful()) {
                return null;
            }

            $dados = $resposta->json();

            if (! is_array($dados) || ! empty($dados['erro'])) {
                return null;
            }

            $resultado = [
                'cep' => $dados['cep'] ?? $cepLimpo,
                'logradouro' => $dados['logradouro'] ?? '',
                'complemento' => $dados['complemento'] ?? '',
                'bairro' => $dados['bairro'] ?? '',
                'cidade' => $dados['localidade'] ?? '',
                'estado' => $dados['uf'] ?? '',
                'pais' => 'Brasil',
            ];

            Cache::put($cacheKey, $resultado, self::CACHE_TTL);

            return $resultado;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
