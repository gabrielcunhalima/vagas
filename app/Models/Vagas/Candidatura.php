<?php

namespace App\Models\Vagas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Candidato;

class Candidatura extends Model
{
    use SoftDeletes;

    protected $table = 'candidaturas';

    protected $fillable = [
        'vaga_id',
        'candidato_id',
        'nome',
        'email',
        'cpf',
        'telefone',
        'curso',
        'instituicao',
        'semestre',
        'previsao_conclusao',
        'carta_apresentacao',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'pais',
        'curriculo_path',
        'curriculo_nome_original',
        'status',
        'entrevista_data',
        'entrevista_local',
        'entrevista_observacoes',
        'observacoes_internas',
        'linkedin',
        'pretensao_salarial',
        'disponibilidade',
        'pcd',
        'pcd_tipo',
    ];

    protected $casts = [
        'previsao_conclusao' => 'date',
        'entrevista_data'    => 'datetime',
        'pretensao_salarial' => 'decimal:2',
        'pcd'                => 'boolean',
    ];

    public static array $statusLabel = [
        'recebida'   => 'Recebida',
        'em_analise' => 'Em Análise',
        'entrevista' => 'Entrevista',
        'aprovado'   => 'Aprovado',
        'reprovado'  => 'Reprovado',
    ];

    public static array $statusCor = [
        'recebida'   => 'info',
        'em_analise' => 'warning',
        'entrevista' => 'primary',
        'aprovado'   => 'success',
        'reprovado'  => 'danger',
    ];

    public static array $proximosStatus = [
        'recebida'   => ['em_analise', 'entrevista', 'reprovado'],
        'em_analise' => ['entrevista', 'reprovado'],
        'entrevista' => ['aprovado', 'reprovado'],
        'aprovado'   => [],
        'reprovado'  => [],
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabel[$this->status] ?? $this->status;
    }

    public function getStatusCorAttribute(): string
    {
        return self::$statusCor[$this->status] ?? 'secondary';
    }

    public function getCpfFormatadoAttribute(): string
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public function getEnderecoCompletoAttribute(): string
    {
        $partes = array_filter([
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->bairro,
            $this->cidade && $this->estado ? "{$this->cidade}/{$this->estado}" : $this->cidade,
            $this->cep,
        ]);
        return implode(', ', $partes);
    }

    public function vaga(): BelongsTo
    {
        return $this->belongsTo(Vaga::class, 'vaga_id');
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    public function scopePorStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeBusca(Builder $query, string $termo): Builder
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('nome', 'like', "%{$termo}%")
              ->orWhere('email', 'like', "%{$termo}%")
              ->orWhere('cpf', 'like', "%{$termo}%")
              ->orWhere('curso', 'like', "%{$termo}%");
        });
    }

    public function podeTransicionarPara(string $novoStatus): bool
    {
        return in_array($novoStatus, self::$proximosStatus[$this->status] ?? []);
    }

    public function temCurriculo(): bool
    {
        return !empty($this->curriculo_path);
    }

    public function getPassoProgressoAttribute(): int
    {
        return match ($this->status) {
            'recebida'   => 1,
            'em_analise' => 2,
            'entrevista' => 3,
            'aprovado'   => 5,
            'reprovado'  => 4,
            default      => 1,
        };
    }
}
