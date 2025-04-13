<?php

use Illuminate\Support\Facades\File;
use NunoMaduro\Essentials\Commands\PublishPintConfigCommand;

beforeEach(function (): void {
    // Clean up any existing pint.json files
    if (file_exists(base_path('pint.json'))) {
        unlink(base_path('pint.json'));
    }

    if (file_exists(base_path('pint.json.backup'))) {
        unlink(base_path('pint.json.backup'));
    }
});

it('publishes pint configuration file', function (): void {
    $command = new PublishPintConfigCommand();

    $this->artisan('essentials:pint', ['--force' => true])
        ->assertExitCode(0)
        ->expectsOutput('Publishing Pint configuration file...')
        ->expectsOutput('Pint configuration file published successfully at: '.base_path('pint.json'));

    expect(file_exists(base_path('pint.json')))->toBeTrue();
});

it('creates a backup when requested', function (): void {
    // Create a dummy pint.json file first
    File::put(base_path('pint.json'), '{"test": "original"}');

    $this->artisan('essentials:pint', ['--backup' => true, '--force' => true])
        ->assertExitCode(0)
        ->expectsOutput('Backup created at: '.base_path('pint.json').'.backup');

    expect(file_exists(base_path('pint.json.backup')))->toBeTrue();
});

it('warns when file exists and no force option', function (): void {
    // Create a dummy pint.json file first
    File::put(base_path('pint.json'), '{"test": "original"}');

    $this->artisan('essentials:pint')
        ->expectsConfirmation('Do you wish to publish the Pint configuration file? This will override the existing pint.json file.', 'no')
        ->assertExitCode(0);

    // File should remain unchanged
    expect(file_get_contents(base_path('pint.json')))->toBe('{"test": "original"}');
});

afterEach(function (): void {
    // Clean up any created files
    if (file_exists(base_path('pint.json'))) {
        unlink(base_path('pint.json'));
    }

    if (file_exists(base_path('pint.json.backup'))) {
        unlink(base_path('pint.json.backup'));
    }
});
