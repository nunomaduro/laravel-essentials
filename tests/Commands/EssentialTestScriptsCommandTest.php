<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    $this->tempDir = sys_get_temp_dir().'/essentials-test-'.uniqid();
    mkdir($this->tempDir);

    $sampleContent = [
        'name' => 'test/test',
        'require' => [
            'php' => '^8.3',
        ],
        'require-dev' => [
            'phpunit/phpunit' => '^10.0',
        ],
        'scripts' => [
            'custom-script' => 'custom-command',
        ],
    ];

    File::put(
        $this->tempDir.'/composer.json',
        json_encode($sampleContent, JSON_PRETTY_PRINT)
    );

    $this->originalCwd = getcwd();

    chdir($this->tempDir);
});

afterEach(function (): void {
    chdir($this->originalCwd);

    if (File::exists($this->tempDir.'/composer.json')) {
        File::delete($this->tempDir.'/composer.json');
    }

    if (File::exists($this->tempDir)) {
        rmdir($this->tempDir);
    }
});

it('adds only scripts for installed packages', function (): void {
    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['require-dev']['laravel/pint'] = '^1.0';
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $this->artisan('essentials:add-scripts')
        ->assertSuccessful();

    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);

    expect($composerJson['scripts'])->toHaveKey('lint')
        ->and($composerJson['scripts'])->toHaveKey('test:lint')
        ->and($composerJson['scripts'])->not->toHaveKeys(['refactor', 'test:types', 'test:unit']);

    expect($composerJson['scripts'])->toHaveKey('test')
        ->and($composerJson['scripts']['test'])->toContain('@test:lint')
        ->and($composerJson['scripts']['test'])->not->toContain('@test:unit');

    expect($composerJson['scripts'])->toHaveKey('custom-script')
        ->and($composerJson['scripts']['custom-script'])->toBe('custom-command');
});

it('adds all scripts when using --skip-checks option', function (): void {
    $this->artisan('essentials:add-scripts', ['--skip-checks' => true])
        ->assertSuccessful();

    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);

    expect($composerJson['scripts'])->toHaveKeys([
        'lint',
        'refactor',
        'test:spellcheck',
        'test:type-coverage',
        'test:lint',
        'test:unit',
        'test:types',
        'test:refactor',
        'test',
    ]);

    expect($composerJson['scripts']['test'])->toBeArray()
        ->and($composerJson['scripts']['test'])->toContain(
            '@test:type-coverage',
            '@test:unit',
            '@test:lint',
            '@test:types',
            '@test:refactor',
            '@test:spellcheck'
        );

    expect($composerJson['scripts'])->toHaveKey('custom-script')
        ->and($composerJson['scripts']['custom-script'])->toBe('custom-command');
});

it('fails gracefully when composer json is not found', function (): void {
    $emptyDir = sys_get_temp_dir().'/essentials-empty-'.uniqid();
    mkdir($emptyDir);

    try {
        chdir($emptyDir);

        $this->artisan('essentials:add-scripts')
            ->assertFailed()
            ->expectsOutput('composer.json not found in the current directory.');
    } finally {
        if (is_dir($emptyDir)) {
            rmdir($emptyDir);
        }
    }
});

it('recommends missing packages', function (): void {
    $this->artisan('essentials:add-scripts')
        ->assertSuccessful()
        ->expectsOutput('Some dependencies are missing for all scripts to work properly.')
        ->expectsOutput('Install the following packages to enable more features:')
        ->expectsOutput('You can install all missing packages with:')
        ->expectsOutputToContain('composer require --dev');
});

it('adds multiple package scripts when multiple dependencies are available', function (): void {
    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['require-dev']['laravel/pint'] = '^1.0';
    $composerJson['require-dev']['larastan/larastan'] = '^3.0';
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $this->artisan('essentials:add-scripts')
        ->assertSuccessful();

    $updatedComposerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);

    expect($updatedComposerJson['scripts'])->toHaveKey('lint')
        ->and($updatedComposerJson['scripts'])->toHaveKeys(['test:lint', 'test:types'])
        ->and($updatedComposerJson['scripts'])->not->toHaveKey('test:unit');

    expect($updatedComposerJson['scripts']['test'])->toContain('@test:lint')
        ->and($updatedComposerJson['scripts']['test'])->toContain('@test:types');
});

it('preserves existing test scripts when adding new ones', function (): void {
    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['scripts']['test'] = [
        '@php artisan config:clear --ansi',
        'echo "Hello world, this should be preserved"',
        '@custom:test',
    ];
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['require-dev']['laravel/pint'] = '^1.0';
    $composerJson['require-dev']['larastan/larastan'] = '^3.0';
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $this->artisan('essentials:add-scripts')
        ->assertSuccessful();

    $updatedComposerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);

    expect($updatedComposerJson['scripts']['test'])->toContain(
        '@php artisan config:clear --ansi',
        'echo "Hello world, this should be preserved"',
        '@custom:test',
        '@test:lint',
        '@test:types',
    );
});

it('maintains the correct order of test scripts based on script definitions', function (): void {
    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['scripts']['test'] = [
        '@php artisan config:clear --ansi',
        '@test:unit',
        'echo "Custom script in the middle"',
        '@test:types',
        '@test:lint',
    ];
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['require-dev']['laravel/pint'] = '^1.0';
    $composerJson['require-dev']['larastan/larastan'] = '^3.0';
    $composerJson['require-dev']['pestphp/pest'] = '^3.0';
    $composerJson['require-dev']['pestphp/pest-plugin-type-coverage'] = '^3.0';
    $composerJson['require-dev']['rector/rector'] = '^2.0';
    $composerJson['require-dev']['peckphp/peck'] = '^0.1';
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $this->artisan('essentials:add-scripts')
        ->assertSuccessful();

    $updatedComposerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);

    expect($updatedComposerJson['scripts']['test'][0])->toBe('@php artisan config:clear --ansi');

    expect($updatedComposerJson['scripts']['test'])->toContain('echo "Custom script in the middle"');

    $testScriptIndexes = [];
    foreach ($updatedComposerJson['scripts']['test'] as $index => $script) {
        if (str_starts_with((string) $script, '@test:')) {
            $testScriptIndexes[$script] = $index;
        }
    }

    expect($testScriptIndexes['@test:spellcheck'])->toBeLessThan($testScriptIndexes['@test:refactor'])
        ->and($testScriptIndexes['@test:refactor'])->toBeLessThan($testScriptIndexes['@test:lint'])
        ->and($testScriptIndexes['@test:lint'])->toBeLessThan($testScriptIndexes['@test:types'])
        ->and($testScriptIndexes['@test:types'])->toBeLessThan($testScriptIndexes['@test:unit'])
        ->and($testScriptIndexes['@test:unit'])->toBeLessThan($testScriptIndexes['@test:type-coverage']);
});

it('handles a string test script and converts it to an array', function (): void {
    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['scripts']['test'] = '@php artisan test --parallel';
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['require-dev']['laravel/pint'] = '^1.0';
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $this->artisan('essentials:add-scripts')
        ->assertSuccessful();

    $updatedComposerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);

    expect($updatedComposerJson['scripts']['test'])->toBeArray()
        ->and($updatedComposerJson['scripts']['test'])->toContain(
            '@php artisan test --parallel',
            '@test:lint'
        );
});

it('creates empty test array with only test scripts when no test script exists', function (): void {
    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    if (isset($composerJson['scripts']['test'])) {
        unset($composerJson['scripts']['test']);
    }
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $composerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);
    $composerJson['require-dev']['laravel/pint'] = '^1.0';
    $composerJson['require-dev']['larastan/larastan'] = '^3.0';
    File::put($this->tempDir.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

    $this->artisan('essentials:add-scripts')
        ->assertSuccessful();

    $updatedComposerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);

    expect($updatedComposerJson['scripts'])->toHaveKey('test');
    expect($updatedComposerJson['scripts']['test'])->toBeArray();

    foreach ($updatedComposerJson['scripts']['test'] as $script) {
        expect($script)->toStartWith('@test:');
    }

    $lintIndex = array_search('@test:lint', $updatedComposerJson['scripts']['test']);
    $typesIndex = array_search('@test:types', $updatedComposerJson['scripts']['test']);
    expect($lintIndex)->toBeLessThan($typesIndex);
});

it('handles invalid JSON in composer.json file', function (): void {
    File::put($this->tempDir.'/composer.json', '{invalid json content');

    $this->artisan('essentials:add-scripts')
        ->assertSuccessful();

    $updatedComposerJson = json_decode(File::get($this->tempDir.'/composer.json'), true);

    expect($updatedComposerJson)
        ->toBeArray()
        ->and->toHaveKey('scripts');
});
