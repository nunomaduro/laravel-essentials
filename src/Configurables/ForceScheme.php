<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Configurables;

use Illuminate\Support\Facades\URL;
use NunoMaduro\Essentials\Contracts\Configurable;

final readonly class ForceScheme implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return config()->boolean(sprintf('essentials.%s', self::class), true);
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        if (app()->environment(...$this->environments())) {
            URL::forceHttps();
        }
    }

    /**
     * The environments the configurable should be set for.
     *
     * @return array<string>
     */
    private function environments(): array
    {
        /** @var array<int, string> $environments */
        $environments = config()->array('essentials.environments.'.self::class, ['production']);

        return $environments;
    }
}
