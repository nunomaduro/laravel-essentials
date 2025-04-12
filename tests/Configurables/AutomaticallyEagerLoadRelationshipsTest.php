<?php

use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Essentials\Configurables\AutomaticallyEagerLoadRelationships;

beforeEach(function (): void {
    Model::automaticallyEagerLoadRelationships(false);
})->skip(fn (): bool => ! method_exists(Model::class, 'automaticallyEagerLoadRelationships'));

it('enables automatic eager loading', function (): void {
    $eagerLoad = new AutomaticallyEagerLoadRelationships;
    $eagerLoad->configure();

    expect(Model::isAutomaticallyEagerLoadingRelationships())->toBeTrue();
});

it('is enabled by default', function (): void {
    $eagerLoad = new AutomaticallyEagerLoadRelationships;

    expect($eagerLoad->enabled())->toBeTrue();
});

it('can be disabled via configuration', function (): void {
    config()->set('essentials.'.AutomaticallyEagerLoadRelationships::class, false);

    $eagerLoad = new AutomaticallyEagerLoadRelationships;

    expect($eagerLoad->enabled())->toBeFalse();
});
