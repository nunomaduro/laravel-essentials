<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class EssentialsPestCommand extends Command
{
    private const string OPENING_TAG = 'added with Essentials Script';

    private const string CLOSING_TAG = 'end: '.self::OPENING_TAG;

    protected $signature = 'essentials:pest';

    protected $description = 'Add essential Pest configuration to your tests';

    public function handle(): int
    {
        $pestFile = base_path('tests/Pest.php');

        if (! File::exists($pestFile)) {
            $this->error('🐞 Show some love to Pest! For Nuno\'s sake, install it first! 🚀');
            $this->line('Run: <fg=gray>composer require pestphp/pest --dev && php artisan pest:install</>');

            return Command::FAILURE;
        }

        $content = File::get($pestFile);

        if (str_contains($content, self::OPENING_TAG)) {
            $this->info('Essential Pest configuration already added.');

            return Command::INVALID;
        }

        $stubPath = $this->getStub();

        $stubContent = File::get($stubPath);

        $newConfig = PHP_EOL.'// '.self::OPENING_TAG.PHP_EOL;
        $newConfig .= $stubContent;
        $newConfig .= PHP_EOL.'// '.self::CLOSING_TAG.PHP_EOL;

        File::append($pestFile, $newConfig);

        $this->info('Essential Pest configuration added successfully.');

        return Command::SUCCESS;
    }

    /**
     * Get the stub file for the generator.
     */
    private function getStub(): string
    {
        return $this->resolveStubPath('/stubs/pest.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    private function resolveStubPath(string $stub): string
    {
        $basePath = base_path(ltrim($stub, '/'));

        return File::exists($basePath)
            ? $basePath
            : __DIR__.'/../../'.$stub;
    }
}
