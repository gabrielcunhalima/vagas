<?php

namespace Database\Factories;

use App\Models\Candidato;
use App\Models\CandidatoCurriculo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Candidato>
 *
 * O estado padrão é o de PERFIL COMPLETO, porque é a pré-condição da maior parte
 * do que se testa (candidatar-se). Para o estado de quem acabou de se cadastrar,
 * use `minimo()`.
 */
class CandidatoFactory extends Factory
{
    protected $model = Candidato::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'cpf' => self::cpfValido(),
            'password' => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
            'lgpd_consentimento' => true,
            'lgpd_consentimento_em' => now(),
            'ativo' => true,

            // Tudo o que Candidato::CAMPOS_OBRIGATORIOS exige.
            'nome' => fake()->name(),
            'nacionalidade' => 'Brasileira',
            'telefone' => '48999001122',
            'possui_acessibilidade' => false,
        ];
    }

    public function configure(): static
    {
        // Formação e currículo são parte da completude, e vivem em outras tabelas.
        return $this->afterCreating(function (Candidato $candidato) {
            if ($candidato->formacoes()->count() === 0) {
                $candidato->formacoes()->create([
                    'nivel_escolaridade' => 'superior_incompleto',
                    'situacao_curso' => 'cursando',
                    'curso' => 'Ciência da Computação',
                    'instituicao' => 'UFSC',
                    'semestre' => '6',
                    'previsao_conclusao' => now()->addYear()->format('Y-m-d'),
                ]);
            }

            if ($candidato->perfilCompleto() || $candidato->curriculo_atual_id) {
                return;
            }

            $versao = CandidatoCurriculo::create([
                'candidato_id' => $candidato->id,
                'path' => 'candidatos/curriculos/teste-'.$candidato->id.'.pdf',
                'nome_original' => 'curriculo.pdf',
                'enviado_em' => now(),
            ]);

            $candidato->forceFill(['curriculo_atual_id' => $versao->id])->save();
        });
    }

    /** Conta recém-criada pelo cadastro mínimo: só credenciais, perfil vazio. */
    public function minimo(): static
    {
        return $this->state(fn () => [
            'nome' => null,
            'nacionalidade' => null,
            'telefone' => null,
            'possui_acessibilidade' => null,
        ])->afterCreating(function (Candidato $candidato) {
            $candidato->forceFill(['curriculo_atual_id' => null])->save();
            $candidato->curriculos()->delete();
            $candidato->formacoes()->delete();
            // configure() já pode ter carregado (e cacheado) a relação antes deste
            // delete — sem invalidar, pendencias() enxergaria a formação apagada.
            $candidato->unsetRelation('formacoes');
        });
    }

    public function naoVerificado(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function semCurriculo(): static
    {
        return $this->afterCreating(function (Candidato $candidato) {
            $candidato->forceFill(['curriculo_atual_id' => null])->save();
            $candidato->curriculos()->delete();
        });
    }

    /** CPF com dígitos verificadores válidos — a validação do portal os confere. */
    public static function cpfValido(): string
    {
        do {
            $base = '';
            for ($i = 0; $i < 9; $i++) {
                $base .= random_int(0, 9);
            }
        } while (preg_match('/^(\d)\1{8}$/', $base));

        $cpf = $base;

        for ($j = 0; $j < 2; $j++) {
            $soma = 0;
            $peso = strlen($cpf) + 1;
            foreach (str_split($cpf) as $digito) {
                $soma += (int) $digito * $peso--;
            }
            $resto = $soma % 11;
            $cpf .= $resto < 2 ? 0 : 11 - $resto;
        }

        return $cpf;
    }
}
