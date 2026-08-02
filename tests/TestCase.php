<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    /**
     * Props do payload Inertia da resposta.
     *
     * Com Inertia a página vive no atributo data-page do root view em JSON
     * escapado, então asserções de conteúdo olham as props e não o HTML.
     */
    protected function propsInertia(TestResponse $response): array
    {
        return $response->viewData('page')['props'] ?? [];
    }

    /** Componente Inertia renderizado (substitui o antigo assertViewIs). */
    protected function assertComponenteInertia(TestResponse $response, string $componente): void
    {
        $this->assertSame($componente, $response->viewData('page')['component'] ?? null);
    }

    /** A prop chegou no payload (substitui o antigo assertViewHas). */
    protected function assertPropInertia(TestResponse $response, string $prop): void
    {
        $this->assertArrayHasKey($prop, $this->propsInertia($response));
    }

    /** O texto aparece nas props (substitui o antigo assertSee). */
    protected function assertVeInertia(TestResponse $response, string $texto): void
    {
        $this->assertStringContainsString($texto, $this->propsInertiaJson($response));
    }

    /** O texto não aparece nas props (substitui o antigo assertDontSee). */
    protected function assertNaoVeInertia(TestResponse $response, string $texto): void
    {
        $this->assertStringNotContainsString($texto, $this->propsInertiaJson($response));
    }

    private function propsInertiaJson(TestResponse $response): string
    {
        return json_encode($this->propsInertia($response), JSON_UNESCAPED_UNICODE) ?: '';
    }
}
