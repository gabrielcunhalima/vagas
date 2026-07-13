<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Vagas\Vaga;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'perfil',
        'ativo',
        'cpf',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'ativo'             => 'boolean',
            'perfil'            => 'string',
        ];
    }

    public function isCoordenador(): bool
    {
        return $this->perfil === 'coordenador';
    }

    public function isGestor(): bool
    {
        return $this->perfil === 'gestor';
    }

    public function isAdmin(): bool
    {
        return $this->perfil === 'admin';
    }

    public function podeAutorizarVagas(): bool
    {
        return in_array($this->perfil, ['gestor', 'admin']);
    }

    public function podeCriarVagas(): bool
    {
        return in_array($this->perfil, ['coordenador', 'admin']);
    }

    public function vagasCriadas()
    {
        return $this->hasMany(Vaga::class, 'coordenador_id');
    }

    public function vagasAutorizadas()
    {
        return $this->hasMany(Vaga::class, 'gestor_id');
    }
}
