<?php

use Illuminate\Support\Facades\Http;
use NunoMaduro\Essentials\Configurables\PreventStrayRequests;

beforeEach(function (): void {
    Http::preventStrayRequests(false);
});

it('prevent stray requests', function (): void {
    $preventStrayRequests = new PreventStrayRequests;
    $preventStrayRequests->configure();

    expect(Http::preventingStrayRequests())->toBeTrue();
});

it('is disabled by default', function (): void {
    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeFalse();
});

it('can be enabled via configuration but ignored as not during testing', function (): void {
    config()->set('essentials.'.PreventStrayRequests::class, true);

    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeFalse();
});

it('can be enabled via configuration when during testing', function (): void {
    config()->set('essentials.'.PreventStrayRequests::class, true);
    app()->detectEnvironment(fn (): string => 'testing');

    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeTrue();
});
