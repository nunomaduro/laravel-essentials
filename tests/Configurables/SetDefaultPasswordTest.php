<?php

use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rules\Password;
use NunoMaduro\Essentials\Configurables\SetDefaultPassword;

beforeEach(function (): void {
    Password::defaults(null);
});

it('sets default password rules in production', function (): void {
    App::shouldReceive('isProduction')->once()->andReturn(true);

    $setDefaultPassword = new SetDefaultPassword;
    $setDefaultPassword->configure();

    $passwordRules = Password::default()->appliedRules();

    expect($passwordRules)->toBeInstanceOf(Password::class)
        ->and($passwordRules['min'])->toBe(12)
        ->and($passwordRules['max'])->toBe(255)
        ->and($passwordRules['uncompromised'])->toBeTrue();
});

it('is enabled by default', function (): void {
    $setDefaultPassword = new SetDefaultPassword;

    expect($setDefaultPassword->enabled())->toBeTrue();
});

it('can be disabled via configuration', function (): void {
    config()->set('essentials.'.SetDefaultPassword::class, false);

    $setDefaultPassword = new SetDefaultPassword;

    expect($setDefaultPassword->enabled())->toBeFalse();
});
