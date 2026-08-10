<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Uma formação do candidato. O perfil pode ter zero, uma ou várias. */
class CandidatoFormacao extends Model
{
    protected $table = 'candidato_formacoes';

    protected $fillable = [
        'candidato_id',
        'nivel_escolaridade',
        'situacao_curso',
        'curso',
        'instituicao',
        'semestre',
        'previsao_conclusao',
    ];

    protected function casts(): array
    {
        return [
            'previsao_conclusao' => 'date',
        ];
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }
}
