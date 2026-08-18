<?php

namespace App\Support\Drhflow;

use RuntimeException;
use Throwable;

/**
 * O DRHFlow não respondeu.
 *
 * Existe para que a indisponibilidade do banco externo chegue às telas como um
 * estado explícito. Sem ela, um `catch` genérico devolveria uma coleção vazia e
 * o candidato leria "não há vagas abertas" — que é o modo de falha que a
 * capacidade `vagas-drhflow` proíbe.
 */
class DrhflowIndisponivelException extends RuntimeException
{
    public static function aoConsultar(string $operacao, Throwable $anterior): self
    {
        return new self("Falha ao consultar o DRHFlow ({$operacao}).", 0, $anterior);
    }
}
