<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Configurables;

use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Essentials\Contracts\Configurable;

final readonly class Unguard implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return config()->boolean(sprintf('essentials.%s', self::class), false);
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Model::unguard();
    }
}
