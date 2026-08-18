<?php

namespace App\Support;

class Iniciais
{
    public static function de(?string $nome): string
    {
        if (blank($nome)) {
            return '?';
        }

        $partes = preg_split('/\s+/', trim($nome));

        return mb_strtoupper(($partes[0][0] ?? '') . ($partes[1][0] ?? $partes[0][1] ?? ''));
    }
}
