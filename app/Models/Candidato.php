<?php

namespace App\Models;

use App\Notifications\Candidato\RedefinirSenhaCandidato;
use App\Notifications\Candidato\VerificarEmailCandidato;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Vagas\Candidatura;

class Candidato extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, SoftDeletes, Notifiable, MustVerifyEmailTrait;

    protected $table = 'candidatos';

    /**
     * O que "perfil completo" significa — critério único do portal, consultado
     * tanto para bloquear a candidatura quanto para desenhar o progresso na tela.
     *
     * Duas listas divergindo (uma no FormRequest, outra no componente React) é o
     * modo de falha previsível aqui: a interface libera o botão e o servidor
     * recusa, ou o contrário. Por isso o cálculo mora só aqui.
     *
     * A formação (lista) e `possui_acessibilidade` e o currículo têm regra
     * própria e são tratados em pendencias().
     */
    public const CAMPOS_OBRIGATORIOS = [
        'nome'          => 'Nome completo',
        'nacionalidade' => 'Nacionalidade',
        'telefone'      => 'Telefone',
    ];

    protected $fillable = [
        'email',
        'password',
        'nome',
        'nome_social',
        'nacionalidade',
        'cpf',
        'telefone',
        'linkedin',
        'outras_formacoes_mec',
        'outros_cursos',
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
        'ativo',
        'ultimo_acesso_em',
        'aviso_inatividade_em',
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
            'pretensao_salarial'  => 'decimal:2',
            'pcd'                 => 'boolean',
            'possui_acessibilidade' => 'boolean',
            'ativo'               => 'boolean',
            'lgpd_consentimento'  => 'boolean',
            'lgpd_consentimento_em' => 'datetime',
            'ultimo_acesso_em'      => 'datetime',
            'aviso_inatividade_em'  => 'datetime',
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

    /** Todas as versões de currículo já enviadas, da mais recente para a mais antiga. */
    public function curriculos()
    {
        return $this->hasMany(CandidatoCurriculo::class, 'candidato_id')
                    ->orderByDesc('enviado_em');
    }

    /** A versão vigente — a única que qualquer consumidor dos dados lê. */
    public function curriculoAtual()
    {
        return $this->belongsTo(CandidatoCurriculo::class, 'curriculo_atual_id');
    }

    public function alerta()
    {
        return $this->hasOne(\App\Models\Vagas\AlertaVaga::class, 'candidato_id');
    }

    /** Todas as formações cadastradas, na ordem em que foram informadas. */
    public function formacoes()
    {
        return $this->hasMany(CandidatoFormacao::class, 'candidato_id')->orderBy('id');
    }

    /**
     * Como chamar o candidato quando ele ainda não preencheu o nome — o cadastro
     * mínimo cria a conta antes disso, e "Olá, !" não serve.
     */
    public function getNomeExibicaoAttribute(): string
    {
        return $this->nome ?: strtok($this->email, '@');
    }

    public function getCpfFormatadoAttribute(): string
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public function temCurriculo(): bool
    {
        return $this->curriculo_atual_id !== null;
    }

    /** Ao menos uma formação da lista com todos os campos exigidos preenchidos. */
    public function temFormacaoCompleta(): bool
    {
        return $this->formacoes->contains(function (CandidatoFormacao $formacao) {
            $completa = filled($formacao->nivel_escolaridade)
                && filled($formacao->situacao_curso)
                && filled($formacao->curso)
                && filled($formacao->instituicao)
                && filled($formacao->previsao_conclusao);

            if (!$completa) {
                return false;
            }

            // Só faz sentido exigir semestre de quem ainda está cursando.
            return $formacao->situacao_curso !== 'cursando' || filled($formacao->semestre);
        });
    }

    /**
     * Campos que faltam para o perfil ficar completo, como ['campo' => 'Rótulo'].
     * Vazio significa completo.
     */
    public function pendencias(): array
    {
        $faltando = [];

        foreach (self::CAMPOS_OBRIGATORIOS as $campo => $rotulo) {
            if (blank($this->{$campo})) {
                $faltando[$campo] = $rotulo;
            }
        }

        if (!$this->temFormacaoCompleta()) {
            $faltando['formacao'] = 'Formação acadêmica';
        }

        // Nulo é "ainda não respondeu"; false é uma resposta válida.
        if ($this->possui_acessibilidade === null) {
            $faltando['possui_acessibilidade'] = 'Necessidade de acessibilidade';
        }

        if (!$this->temCurriculo()) {
            $faltando['curriculo'] = 'Currículo em PDF';
        }

        return $faltando;
    }

    public function perfilCompleto(): bool
    {
        return $this->pendencias() === [];
    }

    /** Estado de completude para a interface — evita que o cliente recalcule o critério. */
    public function estadoCompletude(): array
    {
        $pendencias = $this->pendencias();
        $total      = count(self::CAMPOS_OBRIGATORIOS) + 3; // + formação + acessibilidade + currículo
        $atendidos  = max(0, $total - count($pendencias));

        return [
            'completo'   => $pendencias === [],
            'pendencias' => $pendencias,
            'atendidos'  => $atendidos,
            'total'      => $total,
            'progresso'  => (int) round($atendidos / $total * 100),
        ];
    }

    /**
     * Registra uma nova versão de currículo e move o ponteiro do perfil para ela.
     * Versões anteriores nunca são sobrescritas nem apagadas: um evento de decisão
     * precisa poder identificar qual PDF o processo julgou.
     */
    public function adicionarCurriculo(\Illuminate\Http\UploadedFile $arquivo): CandidatoCurriculo
    {
        $versao = $this->curriculos()->create([
            'path'          => $arquivo->store('candidatos/curriculos', 'local'),
            'nome_original' => $arquivo->getClientOriginalName(),
            'enviado_em'    => now(),
        ]);

        $this->forceFill(['curriculo_atual_id' => $versao->id])->save();
        $this->setRelation('curriculoAtual', $versao);

        return $versao;
    }

    /**
     * Tira o currículo do perfil — que volta a ficar incompleto. A versão em si
     * permanece armazenada, pelo mesmo motivo acima.
     */
    public function removerCurriculoAtual(): void
    {
        $this->forceFill(['curriculo_atual_id' => null])->save();
        $this->unsetRelation('curriculoAtual');
    }

    public function jaSeInscreveuNa(int $vagaId): bool
    {
        return $this->candidaturas()->where('vaga_id', $vagaId)->exists();
    }

    /**
     * Quando esta conta deu sinal de vida pela última vez.
     *
     * Atividade é registrada explicitamente em `ultimo_acesso_em` — não inferida de
     * `updated_at`. Qualquer escrita do sistema bumpa `updated_at` (inclusive o
     * carimbo do aviso de inatividade), e usá-lo faria a conta parecer viva por
     * efeito da própria rotina que a examina.
     */
    public function ultimaAtividadeEm(): ?\Illuminate\Support\Carbon
    {
        $marcos = array_filter([
            $this->ultimo_acesso_em,
            $this->candidaturas()->max('created_at'),
        ]);

        if ($marcos === []) {
            return $this->created_at;
        }

        return collect($marcos)
            ->map(fn ($marco) => $marco instanceof \DateTimeInterface
                ? \Illuminate\Support\Carbon::instance($marco)
                : \Illuminate\Support\Carbon::parse($marco))
            ->max();
    }

    /** Marca uso da conta. Chamado no login e ao salvar o perfil. */
    public function registrarAtividade(): void
    {
        $this->forceFill([
            'ultimo_acesso_em'     => now(),
            'aviso_inatividade_em' => null,
        ])->save();
    }

    /**
     * Guarda contra anonimizar quem ainda está concorrendo: enquanto houver
     * candidatura sem desfecho, a conta serve a um processo em andamento.
     */
    public function temProcessoEmAberto(): bool
    {
        return $this->candidaturas()
            ->whereIn('status', ['recebida', 'em_analise', 'entrevista'])
            ->exists();
    }

}
