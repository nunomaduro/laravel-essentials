<?php

use Illuminate\Database\Console\Migrations\FreshCommand;

it('prohibits destructive commands', function (): void {
    $isProhibited = (
        fn () => static::$prohibitedFromRunning
    )->call(new FreshCommand(app('migrator')));

    expect($isProhibited)->toBeTrue();
});
