<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Commands;

use Illuminate\Console\Command;

final class EssentialsPintCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essentials:pint
        {--force : Force the operation to run without confirmation}
        {--backup : Create a backup of existing pint.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will publish an opinionated Pint configuration file for your project.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force') && ! $this->components->confirm('Do you wish to publish the Pint configuration file? This will override the existing [pint.json] file.', true)) {
            return 0;
        }

        $stub_path = __DIR__.'/../../stubs/pint.stub';
        $destination_path = base_path('pint.json');

        if (! file_exists($stub_path)) {
            $this->components->error('Pint configuration stub file not found.');

            return 1;
        }

        if (file_exists($destination_path) && $this->option('backup')) {
            copy($destination_path, $destination_path.'.backup');
            $this->components->info('Backup created at: '.$destination_path.'.backup');
        }

        $this->components->info('Publishing Pint configuration file...');

        if (! copy($stub_path, $destination_path)) {
            $this->components->error('Failed to publish the Pint configuration file.');

            return 1;
        }

        $this->components->info('Pint configuration file published successfully at: '.$destination_path);

        return 0;
    }
}
