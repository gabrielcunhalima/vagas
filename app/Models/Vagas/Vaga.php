<?php

namespace App\Models\Vagas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Vaga extends Model
{
    use SoftDeletes;

    protected $table = 'vagas';

    protected $fillable = [
        'titulo',
        'descricao',
        'requisitos',
        'requisitos_desejaveis',
        'beneficios',
        'tipo',
        'area',
        'curso_desejado',
        'remuneracao',
        'remuneracao_max',
        'carga_horaria',
        'modalidade',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'pais',
        'local_trabalho',
        'data_encerramento',
        'notificar_email',
        'projeto_nome',
        'projeto_codigo',
        'coordenador_id',
        'gestor_id',
        'status',
        'motivo_recusa',
        'autorizada_em',
        'encerrada_em',
    ];

    protected $casts = [
        'data_encerramento' => 'date',
        'autorizada_em'     => 'datetime',
        'encerrada_em'      => 'datetime',
        'remuneracao'       => 'decimal:2',
        'remuneracao_max'   => 'decimal:2',
        'notificar_email'   => 'boolean',
        'curso_desejado'    => 'array',
    ];

    public static array $cursos = [
        'Administração',
        'Agronomia',
        'Análise e Desenvolvimento de Sistemas',
        'Arquitetura e Urbanismo',
        'Arquivologia',
        'Biblioteconomia',
        'Biologia',
        'Biomedicina',
        'Ciência da Computação',
        'Ciências Contábeis',
        'Ciências Econômicas',
        'Comunicação Social',
        'Design Gráfico',
        'Direito',
        'Educação Física',
        'Enfermagem',
        'Engenharia Ambiental',
        'Engenharia Civil',
        'Engenharia de Produção',
        'Engenharia Elétrica',
        'Engenharia Mecânica',
        'Engenharia Química',
        'Engenharia de Software',
        'Farmácia',
        'Física',
        'Fisioterapia',
        'Geografia',
        'Gestão de Recursos Humanos',
        'Gestão Financeira',
        'História',
        'Jornalismo',
        'Letras',
        'Logística',
        'Marketing',
        'Matemática',
        'Medicina',
        'Medicina Veterinária',
        'Nutrição',
        'Odontologia',
        'Pedagogia',
        'Psicologia',
        'Publicidade e Propaganda',
        'Química',
        'Relações Internacionais',
        'Relações Públicas',
        'Secretariado Executivo',
        'Serviço Social',
        'Sistemas de Informação',
        'Tecnologia da Informação',
        'Turismo',
        'Zootecnia',
    ];

    public static array $areas = [
        'Administração',
        'Arquitetura e Urbanismo',
        'Ciências Contábeis',
        'Ciências Econômicas',
        'Comunicação e Marketing',
        'Direito',
        'Educação',
        'Engenharia',
        'Meio Ambiente',
        'Recursos Humanos',
        'Saúde',
        'Tecnologia da Informação',
        'Ciências Sociais',
    ];

    public static array $tiposLabel = [
        'estagio' => 'Estágio',
        'emprego' => 'CLT',
        'bolsa'   => 'Bolsa',
    ];

    public static array $modalidadesLabel = [
        'presencial' => 'Presencial',
        'remoto'     => 'Remoto',
        'hibrido'    => 'Híbrido',
    ];

    public static array $statusLabel = [
        'rascunho'               => 'Rascunho',
        'aguardando_autorizacao' => 'Aguardando Autorização',
        'ativa'                  => 'Ativa',
        'encerrada'              => 'Encerrada',
        'recusada'               => 'Recusada',
        'inativa'                => 'Inativa',
    ];

    public static array $statusCor = [
        'rascunho'               => 'secondary',
        'aguardando_autorizacao' => 'warning',
        'ativa'                  => 'success',
        'encerrada'              => 'dark',
        'recusada'               => 'danger',
        'inativa'                => 'secondary',
    ];

    public function getTipoLabelAttribute(): string
    {
        return self::$tiposLabel[$this->tipo] ?? $this->tipo;
    }

    public function getModalidadeLabelAttribute(): string
    {
        return self::$modalidadesLabel[$this->modalidade] ?? $this->modalidade;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabel[$this->status] ?? $this->status;
    }

    public function getStatusCorAttribute(): string
    {
        return self::$statusCor[$this->status] ?? 'secondary';
    }

    public function getEstaAbertaAttribute(): bool
    {
        return $this->status === 'ativa'
            && $this->data_encerramento
            && $this->data_encerramento->isFuture();
    }

    public function getDiasRestantesAttribute(): int
    {
        if (!$this->data_encerramento) {
            return 0;
        }
        return max(0, (int) Carbon::today()->diffInDays($this->data_encerramento, false));
    }

    public function getIsNovaAttribute(): bool
    {
        return $this->created_at && $this->created_at->diffInDays(now()) <= 3;
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

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class, 'vaga_id');
    }

    public function coordenador()
    {
        return $this->belongsTo(\App\Models\User::class, 'coordenador_id');
    }

    public function gestor()
    {
        return $this->belongsTo(\App\Models\User::class, 'gestor_id');
    }

    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('status', 'ativa')
                     ->where('data_encerramento', '>=', Carbon::today());
    }

    public function scopeAguardandoAutorizacao(Builder $query): Builder
    {
        return $query->where('status', 'aguardando_autorizacao');
    }

    public function scopePorArea(Builder $query, string $area): Builder
    {
        return $query->where('area', $area);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorModalidade(Builder $query, string $modalidade): Builder
    {
        return $query->where('modalidade', $modalidade);
    }

    public function scopePorCurso(Builder $query, string $curso): Builder
    {
        return $query->whereJsonContains('curso_desejado', $curso);
    }

    public function scopeBusca(Builder $query, string $termo): Builder
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('titulo', 'like', "%{$termo}%")
              ->orWhere('descricao', 'like', "%{$termo}%")
              ->orWhere('area', 'like', "%{$termo}%")
              ->orWhere('projeto_nome', 'like', "%{$termo}%");
        });
    }

    public function totalCandidaturas(): int
    {
        return $this->candidaturas()->count();
    }

    public function candidaturasPorStatus(string $status): int
    {
        return $this->candidaturas()->where('status', $status)->count();
    }
}
