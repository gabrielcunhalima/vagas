<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Uma versão de currículo. Nunca é sobrescrita: substituir o currículo do perfil
 * cria uma versão nova e move o ponteiro `curriculo_atual_id` do candidato.
 * As versões anteriores permanecem para identificar qual PDF um processo julgou
 * — até a retenção vencer.
 *
 * @property Carbon $enviado_em
 * @property Carbon|null $renovado_em
 */
class CandidatoCurriculo extends Model
{
    /**
     * Quanto tempo um currículo fica guardado sem uso.
     *
     * Conta a partir de `renovado_em`: o envio do PDF ou a última candidatura
     * feita com ele. Depois disso a rotina `vagas:remover-curriculos-expirados`
     * apaga o arquivo e a versão.
     */
    public const RETENCAO_MESES = 6;

    protected $table = 'candidato_curriculos';

    protected $fillable = [
        'candidato_id',
        'path',
        'nome_original',
        'enviado_em',
        'renovado_em',
    ];

    protected function casts(): array
    {
        return [
            'enviado_em' => 'datetime',
            'renovado_em' => 'datetime',
        ];
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    /** Versões sem uso há mais que o prazo de retenção. */
    public function scopeExpirados(Builder $query, ?Carbon $referencia = null): Builder
    {
        $limite = ($referencia ?? now())->copy()->subMonths(self::RETENCAO_MESES);

        return $query->where(fn (Builder $q) => $q
            ->where('renovado_em', '<=', $limite)
            ->orWhere(fn (Builder $q) => $q->whereNull('renovado_em')->where('enviado_em', '<=', $limite)));
    }

    /** Quando esta versão será removida se não for usada de novo. */
    public function expiraEm(): Carbon
    {
        return ($this->renovado_em ?? $this->enviado_em)->copy()->addMonths(self::RETENCAO_MESES);
    }

    /** Uma candidatura com esta versão conta como se ela tivesse sido enviada hoje. */
    public function renovar(): void
    {
        $this->forceFill(['renovado_em' => now()])->save();
    }
}
