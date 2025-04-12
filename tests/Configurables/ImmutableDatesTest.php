<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use NunoMaduro\Essentials\Configurables\ImmutableDates;

beforeEach(function (): void {
    Date::use(Carbon::class);
});

it('marks dates as immutable', function (): void {
    $immutableDates = new ImmutableDates;
    $immutableDates->configure();

    $date = now();

    expect($date->isImmutable())->toBeTrue();
});

it('is enabled by default', function (): void {
    $immutableDates = new ImmutableDates;

    expect($immutableDates->enabled())->toBeTrue();
});

it('can be disabled via configuration', function (): void {
    config()->set('essentials.'.ImmutableDates::class, false);

    $immutableDates = new ImmutableDates;

    expect($immutableDates->enabled())->toBeFalse();
});
