<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    $actionsPath = app_path('Actions');

    if (File::isDirectory($actionsPath)) {
        File::deleteDirectory($actionsPath);
    }
});

it('creates a new action file', function (): void {
    $actionName = 'CreateUserAction';
    $exitCode = Artisan::call('make:action', ['name' => $actionName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Actions/'.$actionName.'.php');
    expect(File::exists($expectedPath))->toBeTrue();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Actions;')
        ->toContain('class '.$actionName)
        ->toContain('public function handle(): void');
});

it('fails when the action already exists', function (): void {
    $actionName = 'CreateUserAction';
    Artisan::call('make:action', ['name' => $actionName]);
    $exitCode = Artisan::call('make:action', ['name' => $actionName]);

    expect($exitCode)->toBe(1);
});

it('add suffix "Action" to action name if not provided', function (string $actionName): void {
    $exitCode = Artisan::call('make:action', ['name' => $actionName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Actions/CreateUserAction.php');
    expect(File::exists($expectedPath))->toBeTrue();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Actions;')
        ->toContain('class CreateUserAction')
        ->toContain('public function handle(): void');
})->with([
    'CreateUser',
    'CreateUser.php',
]);
