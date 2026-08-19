<?php

namespace App\Support\Drhflow;

/**
 * O de-para entre a escolaridade do perfil e o grau de instrução do RM.
 *
 * O portal descreve formação em dois eixos — nível (`medio`, `tecnico`,
 * `graduacao`, `pos`, `mestrado`, `doutorado`) e situação (`cursando`,
 * `concluido`). O RM tem um eixo só, com 17 degraus que já embutem a situação
 * ("Educação superior incompleto" vs. "completo").
 *
 * A tabela vive aqui, em um lugar só. Espalhada, ela divergiria entre o ponto
 * que grava a inscrição e o que exibe o perfil, e o candidato apareceria ao RH
 * com uma escolaridade que ele nunca informou.
 *
 * Códigos conferidos contra `VW_GRAU_INSTRUCAO_RM` (ver `reconhecimento-banco.md`).
 */
class GrauInstrucao
{
    /**
     * nível do portal => [situação => código do RM]
     *
     * `tecnico` cai no mesmo degrau de `medio`: a escala do RM não tem rung para
     * ensino técnico, e promovê-lo a superior afirmaria uma formação que o
     * candidato não tem.
     *
     * `Pós Dout.` (G e H) não tem equivalente no portal e nunca é gravado.
     */
    private const DE_PARA = [
        'medio' => ['cursando' => '6', 'concluido' => '7'],
        'tecnico' => ['cursando' => '6', 'concluido' => '7'],
        'graduacao' => ['cursando' => '8', 'concluido' => '9'],
        'pos' => ['cursando' => 'A', 'concluido' => 'B'],
        'mestrado' => ['cursando' => 'C', 'concluido' => 'D'],
        'doutorado' => ['cursando' => 'E', 'concluido' => 'F'],
    ];

    /**
     * Ordem crescente dos códigos do RM. É o que define "a formação de maior
     * grau" — a comparação de strings não serve, porque 'A' viria depois de '9'
     * por acaso do ASCII e antes por acaso em outra codificação.
     */
    private const ORDEM = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

    /**
     * Código do RM para um par nível + situação. Nulo quando o par não tem
     * correspondência — um nulo é diagnosticável, um código errado não.
     */
    public static function codigo(?string $nivel, ?string $situacao): ?string
    {
        if (blank($nivel) || blank($situacao)) {
            return null;
        }

        return self::DE_PARA[strtolower(trim($nivel))][strtolower(trim($situacao))] ?? null;
    }

    /**
     * O maior grau entre as formações informadas.
     *
     * `EN_CANDIDATO_VAGA_EMPREGO` tem uma coluna só (`CD_GRAU_INSTRUCAO`) e o
     * perfil do portal tem várias formações. A regra é a do design D7: vale a
     * mais alta.
     *
     * @param  iterable<object|array{nivel_escolaridade?: string|null, situacao_curso?: string|null}>  $formacoes
     */
    public static function maiorGrau(iterable $formacoes): ?string
    {
        $maior = null;

        foreach ($formacoes as $formacao) {
            $nivel = is_array($formacao) ? ($formacao['nivel_escolaridade'] ?? null) : ($formacao->nivel_escolaridade ?? null);
            $situacao = is_array($formacao) ? ($formacao['situacao_curso'] ?? null) : ($formacao->situacao_curso ?? null);

            $codigo = self::codigo($nivel, $situacao);

            if ($codigo === null) {
                continue;
            }

            if ($maior === null || self::posicao($codigo) > self::posicao($maior)) {
                $maior = $codigo;
            }
        }

        return $maior;
    }

    private static function posicao(string $codigo): int
    {
        $posicao = array_search(strtoupper($codigo), self::ORDEM, true);

        return $posicao === false ? -1 : $posicao;
    }
}
