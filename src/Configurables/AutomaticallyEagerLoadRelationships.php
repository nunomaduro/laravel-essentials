<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Configurables;

use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Essentials\Contracts\Configurable;

final readonly class AutomaticallyEagerLoadRelationships implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return config()->boolean('essentials.automatically-eager-load-relationships.enabled', true);
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        // @phpstan-ignore-next-line
        if (! method_exists(Model::class, 'automaticallyEagerLoadRelationships')) {
            return;
        }

        Model::automaticallyEagerLoadRelationships();
    }
}
