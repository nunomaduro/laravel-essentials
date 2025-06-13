<?php

declare(strict_types=1);

use Illuminate\Support\Facades\URL;
use NunoMaduro\Essentials\Configurables\ForceScheme;

beforeEach(function (): void {
    URL::forceScheme(null);
});

it('forces the URL scheme to HTTPS', function (): void {
    $forceScheme = new ForceScheme;
    $forceScheme->configure();

    $url = URL::to('/test');

    expect($url)->toStartWith('https://');
});

it('is enabled by default', function (): void {
    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeTrue();
});

it('can be disabled via configuration', function (): void {
    config()->set('essentials.'.ForceScheme::class, false);

    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeFalse();
});

it('environments to be forced can be set via configuration', function (): void {
    config()->set('essentials.environments.'.ForceScheme::class, ['local', 'testing']);

    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeFalse();
});
