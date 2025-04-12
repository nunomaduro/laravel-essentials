<?php

use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Essentials\Configurables\Unguard;

it('unguards all Eloquent models when enabled', function (): void {
    config()->set('essentials.'.Unguard::class, true);

    $unguard = new Unguard;

    expect($unguard->enabled())->toBeTrue();

    expect(Model::isUnguarded())->toBeFalse();

    $unguard->configure();

    expect(Model::isUnguarded())->toBeTrue();
});
