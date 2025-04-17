<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Essentials\Configurables\Unguard;

beforeEach(function (): void {
    Model::reguard();
});

it('enables unguarded mode for Eloquent models', function (): void {
    $unguard = new Unguard;
    $unguard->configure();

    expect(Model::isUnguarded())->toBeTrue();
});

it('is disabled by default', function (): void {
    $unguard = new Unguard;

    expect($unguard->enabled())->toBeFalse();
});

it('can be enabled via configuration', function (): void {
    config()->set('essentials.'.Unguard::class, true);

    $unguard = new Unguard;

    expect($unguard->enabled())->toBeTrue();
});
