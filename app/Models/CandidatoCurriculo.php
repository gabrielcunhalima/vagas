<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Uma versão de currículo. Nunca é sobrescrita: substituir o currículo do perfil
 * cria uma versão nova e move o ponteiro `curriculo_atual_id` do candidato.
 * As versões anteriores permanecem para identificar qual PDF um processo julgou.
 */
class CandidatoCurriculo extends Model
{
    protected $table = 'candidato_curriculos';

    protected $fillable = [
        'candidato_id',
        'path',
        'nome_original',
        'enviado_em',
    ];

    protected function casts(): array
    {
        return [
            'enviado_em' => 'datetime',
        ];
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }
}
