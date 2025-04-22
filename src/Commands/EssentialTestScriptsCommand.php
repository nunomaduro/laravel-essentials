<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use NunoMaduro\Essentials\ValueObjects\ScriptDefinition;

final class EssentialTestScriptsCommand extends Command
{
    protected $signature = 'essentials:add-scripts {--skip-checks : Skip dependency checks}';

    protected $description = 'Add useful development scripts to composer.json';

    public function handle(): int
    {
        $composerPath = $this->getComposerPath();

        if (! File::exists($composerPath)) {
            $this->error('composer.json not found in the current directory.');

            return Command::FAILURE;
        }

        $composer = $this->loadComposerJson($composerPath);
        $scripts = $composer['scripts'] ?? [];
        $requires = $this->getRequiredPackages($composer);
        $skipChecks = (bool) $this->option('skip-checks');

        [
            'availableScripts' => $availableScripts,
            'missingPackages' => $missingPackages,
            'individualScripts' => $individualScripts
        ] = $this->processScriptDefinitions($requires, $skipChecks);
        $scripts = array_merge($scripts, $individualScripts);

        $hasTestScripts = $this->hasTestScripts($availableScripts);
        if ($hasTestScripts || $skipChecks) {
            $scripts = $this->processTestScripts($scripts, $requires, $skipChecks);
        }

        $composer['scripts'] = $scripts;
        $this->saveComposerJson($composerPath, $composer);

        $this->displayAddedScriptsInfo($availableScripts, $scripts);

        if ($missingPackages->isNotEmpty() && ! $skipChecks) {
            $this->recommendMissingPackages($missingPackages, $this->getScriptDefinitions());
        }

        return Command::SUCCESS;
    }

    /**
     * Get the path to composer.json
     */
    private function getComposerPath(): string
    {
        return getcwd().'/composer.json';
    }

    /**
     * Load and parse a composer.json file
     *
     * @return array{scripts?: array<string, mixed>, require?: array<string, string>, require-dev?: array<string, string>}
     */
    private function loadComposerJson(string $composerPath): array
    {
        $composerContent = File::get($composerPath);
        $data = json_decode($composerContent, true);

        if (! is_array($data)) {
            return [];
        }

        /** @var array{scripts?: array<string, mixed>, require?: array<string, string>, require-dev?: array<string, string>} $data */
        return $data;
    }

    /**
     * Save the updated composer.json file
     *
     * @param  array<string, mixed>  $composer
     */
    private function saveComposerJson(string $composerPath, array $composer): void
    {
        File::put(
            $composerPath,
            (string) json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Get all required packages from composer.json
     *
     * @param  array{require?: array<string, string>, require-dev?: array<string, string>}  $composer
     * @return array<string, string>
     */
    private function getRequiredPackages(array $composer): array
    {
        return array_merge(
            $composer['require'] ?? [],
            $composer['require-dev'] ?? []
        );
    }

    /**
     * Check if there are any test scripts available
     *
     * @param  Collection<int, ScriptDefinition>  $availableScripts
     */
    private function hasTestScripts(Collection $availableScripts): bool
    {
        return $availableScripts->contains(
            fn (ScriptDefinition $script): bool => str_starts_with($script->name, 'test:')
        );
    }

    /**
     * Process script definitions to determine which are available
     *
     * @param  array<string, string>  $requires
     * @return array{
     *     availableScripts: Collection<int, ScriptDefinition>,
     *     missingPackages: Collection<string, string>,
     *     individualScripts: array<string, string>
     *  }
     */
    private function processScriptDefinitions(array $requires, bool $skipChecks): array
    {
        $scriptDefinitions = $this->getScriptDefinitions();
        /** @var Collection<int, ScriptDefinition> $availableScripts */
        $availableScripts = collect();
        /** @var Collection<string, string> $missingPackages */
        $missingPackages = collect();
        $individualScripts = [];

        foreach ($scriptDefinitions as $script) {
            if ($this->isPackageInstalled($script->package, $requires) || $skipChecks) {
                $availableScripts->push($script);
                $individualScripts[$script->name] = $script->command;
            } else {
                $missingPackages->put($script->package, $script->version);
            }
        }

        return [
            'availableScripts' => $availableScripts,
            'missingPackages' => $missingPackages,
            'individualScripts' => $individualScripts,
        ];
    }

    /**
     * Process and organize test scripts
     *
     * @param  array<string, mixed>  $scripts
     * @param  array<string, string>  $requires
     * @return array<string, mixed>
     */
    private function processTestScripts(array $scripts, array $requires, bool $skipChecks): array
    {
        /** @var array<int, string>|string|null $existingTestScript */
        $existingTestScript = $scripts['test'] ?? null;

        [$customScripts, $existingTestScripts] = $this->separateScripts($existingTestScript);
        $orderedTestScripts = $this->getOrderedTestScripts($requires, $skipChecks);
        $resultScripts = $this->combineScripts($customScripts, $orderedTestScripts);

        if ($resultScripts !== []) {
            $scripts['test'] = $resultScripts;
        }

        return $scripts;
    }

    /**
     * Separate custom scripts from test scripts
     *
     * @param  array<int, string>|string|null  $existingTestScript
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function separateScripts(mixed $existingTestScript): array
    {
        $customScripts = [];
        $existingTestScripts = [];

        if ($existingTestScript !== null) {
            if (! is_array($existingTestScript)) {
                $existingTestScript = [$existingTestScript];
            }

            foreach ($existingTestScript as $script) {
                if (str_starts_with($script, '@test:')) {
                    $existingTestScripts[] = $script;
                } else {
                    $customScripts[] = $script;
                }
            }
        }

        return [$customScripts, $existingTestScripts];
    }

    /**
     * Get ordered test scripts based on script definitions
     *
     * @param  array<string, string>  $requires
     * @return array<int, string>
     */
    private function getOrderedTestScripts(array $requires, bool $skipChecks): array
    {
        $orderedTestScripts = [];

        foreach ($this->getScriptDefinitions() as $definition) {
            if (str_starts_with($definition->name, 'test:')) {
                $scriptRef = '@'.$definition->name;
                if ($this->isPackageInstalled($definition->package, $requires) || $skipChecks) {
                    $orderedTestScripts[] = $scriptRef;
                }
            }
        }

        return $orderedTestScripts;
    }

    /**
     * Combine custom scripts with ordered test scripts
     *
     * @param  array<int, string>  $customScripts
     * @param  array<int, string>  $orderedTestScripts
     * @return array<int, string>
     */
    private function combineScripts(array $customScripts, array $orderedTestScripts): array
    {
        $resultScripts = $customScripts;

        foreach ($orderedTestScripts as $script) {
            if (! in_array($script, $resultScripts, true)) {
                $resultScripts[] = $script;
            }
        }

        return $resultScripts;
    }

    /**
     * Display information about added scripts
     *
     * @param  Collection<int, ScriptDefinition>  $availableScripts
     * @param  array<string, mixed>  $scripts
     */
    private function displayAddedScriptsInfo(Collection $availableScripts, array $scripts): void
    {
        if ($availableScripts->isNotEmpty()) {
            $this->info('The following scripts have been added to composer.json:');

            $availableScripts->each(function (ScriptDefinition $script): void {
                $this->line("• composer {$script->name}: {$script->description}");
            });

            if (isset($scripts['test'])) {
                $this->line('• composer test: Run all checks in sequence');
            }
        }
    }

    /**
     * Collection of script definitions with their dependencies.
     *
     * @return Collection<int, ScriptDefinition>
     */
    private function getScriptDefinitions(): Collection
    {
        return collect([
            new ScriptDefinition(
                name: 'lint',
                command: 'pint',
                package: 'laravel/pint',
                version: '^1.0',
                description: 'Format your code using Laravel Pint'
            ),
            new ScriptDefinition(
                name: 'refactor',
                command: 'rector',
                package: 'rector/rector',
                version: '^2.0',
                description: 'Refactor your code using Rector'
            ),
            new ScriptDefinition(
                name: 'test:spellcheck',
                command: 'peck',
                package: 'peckphp/peck',
                version: '^0.1',
                description: 'Check for spelling errors using Peck'
            ),
            new ScriptDefinition(
                name: 'test:refactor',
                command: 'rector --dry-run',
                package: 'rector/rector',
                version: '^2.0',
                description: 'Check for possible refactoring opportunities'
            ),
            new ScriptDefinition(
                name: 'test:lint',
                command: 'pint --test',
                package: 'laravel/pint',
                version: '^1.0',
                description: 'Check if code needs formatting'
            ),
            new ScriptDefinition(
                name: 'test:types',
                command: 'phpstan analyse --ansi',
                package: 'larastan/larastan',
                version: '^3.0',
                description: 'Run static analysis using PHPStan'
            ),
            new ScriptDefinition(
                name: 'test:unit',
                command: 'pest --colors=always --coverage --parallel',
                package: 'pestphp/pest',
                version: '^3.0',
                description: 'Run unit tests with coverage and parallel execution'
            ),
            new ScriptDefinition(
                name: 'test:type-coverage',
                command: 'pest --type-coverage --min=100',
                package: 'pestphp/pest-plugin-type-coverage',
                version: '^3.0',
                description: 'Check type coverage (minimum 100%)'
            ),
        ]);
    }

    /**
     * Check if a package is installed by looking for it in the requirement array.
     *
     * @param  string  $package  The package name to check
     * @param  array<string, string>  $requires  The dependencies from composer.json
     */
    private function isPackageInstalled(string $package, array $requires): bool
    {
        return isset($requires[$package]);
    }

    /**
     * @param  Collection<string, string>  $missingPackages
     * @param  Collection<int, ScriptDefinition>  $scriptDefinitions
     */
    private function recommendMissingPackages(Collection $missingPackages, Collection $scriptDefinitions): void
    {
        $this->newLine();
        $this->warn('Some dependencies are missing for all scripts to work properly.');
        $this->line('Install the following packages to enable more features:');

        $command = 'composer require --dev ';
        /** @var array<int, string> $packagesWithVersions */
        $packagesWithVersions = [];

        $missingPackages->each(function (string $version, string $package) use (&$packagesWithVersions, $scriptDefinitions): void {
            $relatedScripts = $scriptDefinitions->filter(fn (
                ScriptDefinition $script): bool => $script->package === $package);

            $scriptDescriptions = $relatedScripts->map(fn (
                ScriptDefinition $script): string => "composer {$script->name}")->join(', ');

            $this->line("• {$package} ({$version}) - Enables: {$scriptDescriptions}");
            $packagesWithVersions[] = "{$package}:{$version}";
        });

        $this->newLine();
        $this->line('You can install all missing packages with:');
        $this->components->info($command.implode(' ', $packagesWithVersions));
    }
}
