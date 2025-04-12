<?php

namespace NunoMaduro\Essentials\Configurables;

use Illuminate\Validation\Rules\Password;
use NunoMaduro\Essentials\Contracts\Configurable;

class SetDefaultPassword implements Configurable
{
    /**
     * {@inheritDoc}
     */
    public function enabled(): bool
    {
        return config()->boolean(sprintf('essentials.%s', self::class), true);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(): void
    {
        Password::defaults(fn (): ?Password => app()->isProduction() ? Password::min(12)->max(255)->uncompromised() : null);
    }
}
