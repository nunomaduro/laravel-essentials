<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use NunoMaduro\Essentials\Contracts\Configurable;

/**
 * @internal
 */
final class EssentialsServiceProvider extends BaseServiceProvider
{
    /**
     * The list of configurables.
     *
     * @var list<class-string<Configurable>>
     */
    private array $configurables = [
        Configurables\AggressivePrefetching::class,
        Configurables\AutomaticallyEagerLoadRelationships::class,
        Configurables\ForceScheme::class,
        Configurables\ImmutableDates::class,
        Configurables\ProhibitDestructiveCommands::class,
        Configurables\ShouldBeStrict::class,
        Configurables\Unguard::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        collect($this->configurables)
            ->map(fn (string $configurable) => $this->app->make($configurable))
            ->filter(fn (Configurable $configurable): bool => $configurable->enabled())
            ->each(fn (Configurable $configurable) => $configurable->configure());
        $this->publishes([
            __DIR__.'stubs/rector.stub' => $this->app->basePath('rector.php'),
        ]);
    }
}
