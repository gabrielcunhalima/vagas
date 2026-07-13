<?php

namespace App\Http\Requests\Concerns;

trait ValidaCpf
{
    private function validarCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int)$cpf[$i] * ($t + 1 - $i);
            }
            $resto = $soma % 11;
            if ((int)$cpf[$t] !== ($resto < 2 ? 0 : 11 - $resto)) {
                return false;
            }
        }
        return true;
    }
}
