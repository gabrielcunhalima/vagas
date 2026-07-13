<?php

namespace App\Models\Vagas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AlertaVaga extends Model
{
    protected $table = 'vaga_alertas';

    protected $fillable = ['email', 'areas', 'modalidades', 'tipos', 'ativo', 'token', 'lgpd_consentimento', 'lgpd_consentimento_em'];

    protected $casts = [
        'areas'      => 'array',
        'modalidades'=> 'array',
        'tipos'      => 'array',
        'ativo'      => 'boolean',
        'lgpd_consentimento'    => 'boolean',
        'lgpd_consentimento_em' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->token = $model->token ?? Str::random(64);
        });
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function compativel(Vaga $vaga): bool
    {
        if (!empty($this->areas) && !in_array($vaga->area, $this->areas)) return false;
        if (!empty($this->modalidades) && !in_array($vaga->modalidade, $this->modalidades)) return false;
        if (!empty($this->tipos) && !in_array($vaga->tipo, $this->tipos)) return false;
        return true;
    }
}
