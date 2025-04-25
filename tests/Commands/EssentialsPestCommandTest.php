<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

$cleanup = function (): void {
    $pestFile = base_path('tests/Pest.php');
    if (File::exists($pestFile)) {
        File::delete($pestFile);
    }

    $stubsPath = base_path('stubs');
    if (File::exists($stubsPath)) {
        File::deleteDirectory($stubsPath);
    }
};

beforeEach(fn () => $cleanup());
afterEach(fn () => $cleanup());

function createBasePestFile(): void
{
    $pestFile = base_path('tests/Pest.php');
    $stubContent = File::get(__DIR__.'/../stubs/pest-base.stub');
    File::put($pestFile, $stubContent);
}

it('adds essential pest configuration when Pest.php exists', function (): void {
    createBasePestFile();
    $pestFile = base_path('tests/Pest.php');

    $this->artisan('essentials:pest')
        ->assertSuccessful();

    $content = File::get($pestFile);
    expect($content)
        ->toContain('// added with Essentials Script')
        ->toContain('Http::preventStrayRequests()')
        ->toContain('Sleep::fake()')
        ->toContain('->in(\'Feature\', \'Unit\')');
});

it('skips adding configuration if already present', function (): void {
    $pestFile = base_path('tests/Pest.php');
    File::put($pestFile, "<?php\n\n// added with Essentials Script\n// some content");

    $this->artisan('essentials:pest')
        ->assertFailed()
        ->expectsOutput('Essential Pest configuration already added.');
});

it('shows a friendly error when Pest.php does not exist', function (): void {
    $this->artisan('essentials:pest')
        ->assertFailed()
        ->expectsOutput('🐞 Show some love to Pest! For Nuno\'s sake, install it first! 🚀');
});

it('uses published stub when available', function (): void {
    createBasePestFile();
    $pestFile = base_path('tests/Pest.php');

    $this->artisan('vendor:publish', ['--tag' => 'essentials-stubs'])
        ->assertSuccessful();

    $publishedStubPath = base_path('stubs/pest.stub');
    File::put($publishedStubPath, "// This is a custom stub\npest()->extend(Tests\TestCase::class)");

    $this->artisan('essentials:pest')
        ->assertSuccessful();

    $content = File::get($pestFile);
    expect($content)
        ->toContain('// added with Essentials Script')
        ->toContain('// This is a custom stub')
        ->not->toContain('Sleep::fake()');
});
