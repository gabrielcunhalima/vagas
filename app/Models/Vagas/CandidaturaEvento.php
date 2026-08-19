<?php

namespace App\Models\Vagas;

use App\Models\CandidatoCurriculo;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de processo: quem decidiu o quê, quando, sobre qual versão de currículo.
 *
 * Nenhum dado de identidade do candidato entra aqui. É o que permite abandonar o
 * snapshot em `candidaturas` sem perder a capacidade de sustentar uma decisão —
 * e, por não conter dado pessoal, não precisa entrar na rotina de anonimização.
 */
class CandidaturaEvento extends Model
{
    protected $table = 'candidatura_eventos';

    public const TIPO_SUBMISSAO = 'submissao';

    public const TIPO_TRANSICAO = 'transicao_status';

    protected $fillable = [
        'candidatura_id',
        'tipo',
        'status_anterior',
        'status_novo',
        'autor_id',
        'curriculo_id_vigente',
        'observacao',
        'ocorrido_em',
    ];

    protected function casts(): array
    {
        return [
            'ocorrido_em' => 'datetime',
        ];
    }

    public function candidatura(): BelongsTo
    {
        return $this->belongsTo(Candidatura::class, 'candidatura_id');
    }

    /** Coordenador que realizou a transição; nulo quando o autor é o candidato. */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function curriculoVigente(): BelongsTo
    {
        return $this->belongsTo(CandidatoCurriculo::class, 'curriculo_id_vigente');
    }

    public function getDescricaoAttribute(): string
    {
        if ($this->tipo === self::TIPO_SUBMISSAO) {
            return 'Candidatura enviada';
        }

        $de = Candidatura::$statusLabel[$this->status_anterior] ?? $this->status_anterior;
        $para = Candidatura::$statusLabel[$this->status_novo] ?? $this->status_novo;

        return "Status alterado de \"{$de}\" para \"{$para}\"";
    }
}
