<?php

use Illuminate\Validation\Rules\Password;
use NunoMaduro\Essentials\Configurables\SetDefaultPassword;

beforeEach(function (): void {
    Password::defaults(null);
});

it('sets default password rules', function (): void {
    $setDefaultPassword = new SetDefaultPassword;
    $setDefaultPassword->configure();

    $passwordRules = Password::default()->appliedRules();

    expect($passwordRules['min'])->toBe(12)
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
