<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user('web');
        $candidato = $request->user('candidato');

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'perfil' => $user->perfil,
                ] : null,
                'candidato' => $candidato ? [
                    'id' => $candidato->id,
                    'nome' => $candidato->nome,
                    'email' => $candidato->email,
                    'email_verified' => $candidato->hasVerifiedEmail(),
                ] : null,
            ],

            'flash' => [
                'success' => $request->session()->get('sucesso') ?? $request->session()->get('success'),
                'info' => $request->session()->get('aviso') ?? $request->session()->get('info'),
                'error' => $request->session()->get('erro') ?? $request->session()->get('error'),
            ],
        ];
    }
}
