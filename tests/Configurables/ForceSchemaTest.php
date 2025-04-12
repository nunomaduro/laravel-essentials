<?php

use Illuminate\Support\Facades\URL;
use NunoMaduro\Essentials\Configurables\ForceScheme;

it('forces HTTPS scheme when enabled', function (): void {
    config()->set('essentials.'.ForceScheme::class, true);

    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeTrue();

    $forceScheme->configure();

    $generated = URL::to('/example');

    expect($generated)->toStartWith('https://');
});
