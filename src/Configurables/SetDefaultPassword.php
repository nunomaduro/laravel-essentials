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
        return config()->boolean(sprintf('essentials.%s', self::class), false);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(): void
    {
        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? $this->passwordDefaults()
                : null
        );
    }

    private function passwordDefaults(): Password
    {
        return Password::min(8)
            // ->letters()
            // ->mixedCase()
            // ->numbers()
            // ->symbols()
            ->uncompromised();
    }
}
