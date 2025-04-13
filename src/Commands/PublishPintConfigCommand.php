<?php

namespace NunoMaduro\Essentials\Commands;

use Illuminate\Console\Command;

class PublishPintConfigCommand extends Command
{
    protected $signature = 'essentials:pint
        {--force : Force the operation to run without confirmation}
        {--backup : Create a backup of existing pint.json}';

    protected $description = 'This command will publish an opinionated Pint configuration file for your project.';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Do you wish to publish the Pint configuration file? This will override the existing pint.json file.', true)) {
            return 0;
        }

        $stub_path = __DIR__.'/../../stubs/pint.stub';
        $destination_path = base_path('pint.json');

        if (! file_exists($stub_path)) {
            $this->error('Pint configuration stub file not found.');

            return 1;
        }

        if (file_exists($destination_path)) {
            if ($this->option('backup')) {
                copy($destination_path, $destination_path.'.backup');
                $this->info('Backup created at: '.$destination_path.'.backup');
            }

            if (! $this->option('force')) {
                $this->warn('Pint configuration file already exists!');

                return 1;
            }
        }

        $this->info('Publishing Pint configuration file...');

        if (! copy($stub_path, $destination_path)) {
            $this->error('Failed to publish the Pint configuration file.');

            return 1;
        }

        $this->info('Pint configuration file published successfully at: '.$destination_path);

        return 0;
    }
}
