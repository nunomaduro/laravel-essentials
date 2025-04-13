<?php

declare(strict_types=1);

use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Support\Facades\DB;
use NunoMaduro\Essentials\Configurables\ProhibitDestructiveCommands;

beforeEach(function (): void {
    DB::prohibitDestructiveCommands(false);
});

it('prohibits destructive commands', function (): void {
    $prohibitDestructiveCommands = new ProhibitDestructiveCommands;
    $prohibitDestructiveCommands->configure();

    $isProhibited = (
        fn () => static::$prohibitedFromRunning
    )->call(new FreshCommand(app('migrator')));

    expect($isProhibited)->toBeTrue();
});

it('is enabled by default', function (): void {
    $prohibitDestructiveCommands = new ProhibitDestructiveCommands;

    expect($prohibitDestructiveCommands->enabled())->toBeTrue();
});

it('can be disabled via configuration', function (): void {
    config()->set('essentials.'.ProhibitDestructiveCommands::class, false);

    $prohibitDestructiveCommands = new ProhibitDestructiveCommands;

    expect($prohibitDestructiveCommands->enabled())->toBeFalse();
});
