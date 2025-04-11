<?php

declare(strict_types=1);

namespace Tests;

use NunoMaduro\Essentials\EssentialsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get the application package providers.
     */
    protected function getPackageProviders($app): array
    {
        app()->detectEnvironment(fn (): string => 'production');

        return [
            EssentialsServiceProvider::class,
        ];
    }
}
