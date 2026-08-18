<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A parte da inscrição que vive no portal.
 *
 * A inscrição em si é a linha de `EN_CANDIDATO_VAGA_EMPREGO` no DRHFlow. Este
 * registro guarda apenas o que a origem não tem campo para receber, correlato
 * pelo mesmo par CPF + código da vaga.
 *
 * Nada aqui duplica dado do DRHFlow — ver a migration para a lista do que ficou
 * de fora e por quê.
 */
class InscricaoComplemento extends Model
{
    protected $table = 'inscricao_complementos';

    protected $fillable = [
        'candidato_id',
        'cpf',
        'cd_vaga_emprego',
        'carta_apresentacao',
        'conflito_interesse',
        'conflito_interesse_detalhe',
        'curriculo_id_vigente',
        'enviada_em',
    ];

    protected function casts(): array
    {
        return [
            'cd_vaga_emprego' => 'integer',
            'conflito_interesse' => 'boolean',
            'enviada_em' => 'datetime',
        ];
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    /** A versão de currículo que estava vigente no envio — não a atual do perfil. */
    public function curriculoVigente(): BelongsTo
    {
        return $this->belongsTo(CandidatoCurriculo::class, 'curriculo_id_vigente');
    }
}
