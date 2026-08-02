<?php

namespace App\Models;

use App\Notifications\Candidato\RedefinirSenhaCandidato;
use App\Notifications\Candidato\VerificarEmailCandidato;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Vagas\Candidatura;

class Candidato extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, Notifiable, MustVerifyEmailTrait;

    protected $table = 'candidatos';

    protected $fillable = [
        'email',
        'password',
        'nome',
        'nome_social',
        'nacionalidade',
        'cpf',
        'telefone',
        'linkedin',
        'curso',
        'instituicao',
        'nivel_escolaridade',
        'situacao_curso',
        'semestre',
        'previsao_conclusao',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'pais',
        'pretensao_salarial',
        'disponibilidade',
        'pcd',
        'pcd_tipo',
        'possui_acessibilidade',
        'acessibilidade_detalhe',
        'curriculo_path',
        'curriculo_nome_original',
        'lgpd_consentimento',
        'lgpd_consentimento_em',
        'conflito_interesse',
        'conflito_interesse_detalhe',
        'codigo_conduta_aceito_em',
        'ativo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'previsao_conclusao'  => 'date',
            'pretensao_salarial'  => 'decimal:2',
            'pcd'                 => 'boolean',
            'possui_acessibilidade' => 'boolean',
            'ativo'               => 'boolean',
            'lgpd_consentimento'  => 'boolean',
            'lgpd_consentimento_em' => 'datetime',
            'conflito_interesse'  => 'boolean',
            'codigo_conduta_aceito_em' => 'datetime',
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerificarEmailCandidato());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new RedefinirSenhaCandidato($token));
    }

    public function candidaturas()
    {
        return $this->hasMany(Candidatura::class, 'candidato_id');
    }

    public function getCpfFormatadoAttribute(): string
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public function temCurriculo(): bool
    {
        return !empty($this->curriculo_path);
    }

    public function jaSeInscreveuNa(int $vagaId): bool
    {
        return $this->candidaturas()->where('vaga_id', $vagaId)->exists();
    }

    /** Retorna array pronto para pré-preencher candidatura */
    public function dadosParaCandidatura(): array
    {
        return [
            'nome'                  => $this->nome,
            'email'                 => $this->email,
            'cpf'                   => $this->cpf,
            'telefone'              => $this->telefone,
            'curso'                 => $this->curso,
            'instituicao'           => $this->instituicao,
            'semestre'              => $this->semestre,
            'previsao_conclusao'    => $this->previsao_conclusao?->format('Y-m-d'),
            'cep'                   => $this->cep,
            'logradouro'            => $this->logradouro,
            'numero'                => $this->numero,
            'complemento'           => $this->complemento,
            'bairro'                => $this->bairro,
            'cidade'                => $this->cidade,
            'estado'                => $this->estado,
            'linkedin'              => $this->linkedin,
            'pretensao_salarial'    => $this->pretensao_salarial,
            'disponibilidade'       => $this->disponibilidade,
            'pcd'                   => $this->pcd ? '1' : '0',
            'pcd_tipo'              => $this->pcd_tipo,
        ];
    }
}
