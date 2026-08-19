<?php

namespace App\Http\Controllers\Vagas;

use App\Http\Controllers\Controller;
use App\Services\ViaCepService;
use Illuminate\Http\JsonResponse;

class CepController extends Controller
{
    public function __construct(private ViaCepService $service) {}

    public function buscar(string $cep): JsonResponse
    {
        $dados = $this->service->buscar($cep);

        if (! $dados) {
            return response()->json(['erro' => 'CEP não encontrado.'], 404);
        }

        return response()->json($dados);
    }
}
