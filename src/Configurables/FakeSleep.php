<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Configurables;

use Illuminate\Support\Sleep;
use NunoMaduro\Essentials\Contracts\Configurable;

final readonly class FakeSleep implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        $enabled = config()->boolean(sprintf('essentials.%s', self::class), true);
        $testing = app()->runningUnitTests();

        return $enabled && $testing;
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Sleep::fake();
    }
}
