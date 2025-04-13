<?php

use Illuminate\Support\Sleep;
use NunoMaduro\Essentials\Configurables\FakeSleep;

beforeEach(function (): void {
    Sleep::fake(false);
});

it('fake sleep', function (): void {
    $fakeSleep = new FakeSleep;
    $fakeSleep->configure();

    Sleep::usleep(1);

    Sleep::assertSleptTimes(1);
});

it('is disabled by default', function (): void {
    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeFalse();
});

it('can be enabled via configuration but ignored as not during testing', function (): void {
    config()->set('essentials.'.FakeSleep::class, true);

    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeFalse();
});

it('can be enabled via configuration when during testing', function (): void {
    config()->set('essentials.'.FakeSleep::class, true);
    app()->detectEnvironment(fn (): string => 'testing');

    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeTrue();
});
