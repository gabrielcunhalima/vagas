<?php

namespace App\Support\Drhflow;

/**
 * Normalização usada em todo casamento por nome contra os domínios do DRHFlow
 * (município, país, curso superior).
 *
 * O casamento por nome erra com acentuação, caixa e espaço duplicado — e um
 * código errado gravado no registro do candidato não é diagnosticável, enquanto
 * um nulo é. Por isso a normalização é agressiva e mora em um lugar só: duas
 * implementações divergindo produziriam correspondências diferentes para o
 * mesmo nome em pontos diferentes do código.
 */
class Normalizador
{
    /** Chave de comparação: sem acento, sem caixa, sem espaço redundante. */
    public static function chave(?string $texto): string
    {
        if ($texto === null) {
            return '';
        }

        $semAcento = self::semAcento($texto);
        $minusculo = mb_strtolower($semAcento, 'UTF-8');
        $semPontuacao = preg_replace('/[^a-z0-9]+/', ' ', $minusculo) ?? '';

        return trim(preg_replace('/\s+/', ' ', $semPontuacao) ?? '');
    }

    /** Só dígitos — usado no CPF, que o DRHFlow guarda sem máscara. */
    public static function digitos(?string $texto): string
    {
        return preg_replace('/\D/', '', (string) $texto) ?? '';
    }

    private static function semAcento(string $texto): string
    {
        $transliterado = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);

        // iconv depende do locale e pode devolver false ou lixo com aspas
        // ("Florianópolis" -> "Florian'opolis"); o de-para explícito é o que
        // garante o mesmo resultado em qualquer servidor.
        if ($transliterado === false || str_contains($transliterado, '?')) {
            $transliterado = $texto;
        }

        return strtr($transliterado, [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N',
            "'" => '', '`' => '', '^' => '', '~' => '', '"' => '',
        ]);
    }
}
