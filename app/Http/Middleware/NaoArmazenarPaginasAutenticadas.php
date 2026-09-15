<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Página de quem está logado não fica no cache do navegador.
 *
 * O padrão do Laravel é `Cache-Control: no-cache, private`, e o botão voltar do
 * Chrome ignora `no-cache`: ele reapresenta a cópia em disco sem perguntar ao
 * servidor. Com duas contas criadas em sequência no mesmo navegador, voltar
 * mostrava a página — nome, e-mail, menu — da conta anterior, embora a sessão
 * já fosse da nova. Só `no-store` impede a cópia.
 *
 * Visitantes continuam com o cache normal: a página deles não carrega dado de
 * ninguém, e a listagem de vagas consulta o DRHFlow a cada requisição.
 */
class NaoArmazenarPaginasAutenticadas
{
    private const GUARDS = ['candidato', 'web'];

    public function handle(Request $request, Closure $next): Response
    {
        $antes = $this->identidade();

        $response = $next($request);

        // Avaliado depois da requisição: o login ou o cadastro que acabou de
        // acontecer já conta, e o logout já deixou de contar.
        $depois = $this->identidade();

        if (array_filter($depois) !== []) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        /*
         * Troca de conta (login, logout, cadastro): o `no-store` vale para o que
         * vem daqui em diante, mas não apaga cópias gravadas antes dele. Pedir
         * ao navegador que limpe o cache no momento da troca garante que o
         * botão voltar não encontre página de outra conta.
         */
        if ($antes !== $depois) {
            $response->headers->set('Clear-Site-Data', '"cache"');
        }

        return $response;
    }

    /** @return array<string, int|string|null> */
    private function identidade(): array
    {
        return array_combine(
            self::GUARDS,
            array_map(fn (string $guard) => Auth::guard($guard)->id(), self::GUARDS),
        );
    }
}
