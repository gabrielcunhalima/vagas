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

    /**
     * Só o que é próprio da inscrição. Identidade, contato, formação, endereço e
     * currículo pertencem ao perfil do candidato — ver CAMPOS_DO_PERFIL.
     */
    protected $fillable = [
        'vaga_id',
        'candidato_id',
        'carta_apresentacao',
        'conflito_interesse',
        'conflito_interesse_detalhe',
        'codigo_conduta_aceito_em',
        'status',
        'entrevista_data',
        'entrevista_local',
        'entrevista_observacoes',
        'observacoes_internas',
    ];

    protected $casts = [
        'entrevista_data'          => 'datetime',
        'conflito_interesse'       => 'boolean',
        'codigo_conduta_aceito_em' => 'datetime',
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

    /**
     * Campos que deixam de pertencer à candidatura e passam a ser lidos do perfil.
     *
     * A candidatura guardava uma cópia congelada de cada um deles; agora a fonte é
     * o candidato, e toda leitura reflete o valor atual. As colunas homônimas ainda
     * existem no banco até o passo 4 do plano de migração — a delegação abaixo
     * garante que ninguém as leia enquanto isso.
     */
    protected const CAMPOS_DO_PERFIL = [
        'nome', 'email', 'cpf', 'telefone', 'linkedin',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'pais',
        'pretensao_salarial', 'disponibilidade', 'pcd', 'pcd_tipo',
    ];

    public function getAttribute($key)
    {
        if (in_array($key, self::CAMPOS_DO_PERFIL, true)) {
            return $this->candidato?->getAttribute($key);
        }

        return parent::getAttribute($key);
    }

    /** Formações do candidato vinculado — a candidatura nunca guarda cópia própria. */
    public function getFormacoesAttribute()
    {
        return $this->candidato?->formacoes ?? collect();
    }

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

    /** Registro do processo: submissão e transições de status, do mais antigo ao mais recente. */
    public function eventos()
    {
        return $this->hasMany(CandidaturaEvento::class, 'candidatura_id')
                    ->orderBy('ocorrido_em');
    }

    /** Momento da decisão final — base do prazo de decaimento de acesso do coordenador. */
    public function eventoDecisao()
    {
        return $this->hasOne(CandidaturaEvento::class, 'candidatura_id')
                    ->whereIn('status_novo', ['aprovado', 'reprovado'])
                    ->latestOfMany('ocorrido_em');
    }

    public function scopePorStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /** Filtra pelos dados do perfil, já que a candidatura não os guarda mais. */
    public function scopeBusca(Builder $query, string $termo): Builder
    {
        return $query->whereHas('candidato', function ($q) use ($termo) {
            $q->where('nome', 'like', "%{$termo}%")
              ->orWhere('email', 'like', "%{$termo}%")
              ->orWhere('cpf', 'like', "%{$termo}%")
              ->orWhereHas('formacoes', function ($fq) use ($termo) {
                  $fq->where('curso', 'like', "%{$termo}%");
              });
        });
    }

    public function podeTransicionarPara(string $novoStatus): bool
    {
        return in_array($novoStatus, self::$proximosStatus[$this->status] ?? []);
    }

    /** O currículo da candidatura é a versão vigente do perfil, não uma cópia do envio. */
    public function temCurriculo(): bool
    {
        return $this->candidato?->temCurriculo() ?? false;
    }

    public function getCurriculoPathAttribute(): ?string
    {
        return $this->candidato?->curriculoAtual?->path;
    }

    public function getCurriculoNomeOriginalAttribute(): ?string
    {
        return $this->candidato?->curriculoAtual?->nome_original;
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
