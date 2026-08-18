<?php

namespace App\Support;

class Cpf
{
    /** 00000000000 -> 000.000.000-00. Espera 11 dígitos; devolve como veio se não bater. */
    public static function mascara(?string $cpf): ?string
    {
        $digitos = preg_replace('/\D/', '', (string) $cpf);

        if (strlen($digitos) !== 11) {
            return $cpf;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digitos);
    }
}
