<p align="center"><img src="art/logo.svg" width="50%" alt="Logo Laravel Essentials"></p>

<p align="center">
    <a href="https://github.com/nunomaduro/essentials/actions"><img src="https://github.com/nunomaduro/essentials/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/dt/nunomaduro/essentials" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/v/nunomaduro/essentials" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/l/nunomaduro/essentials" alt="License"></a>
</p>

# Essentials

Essentials provides **better defaults** for your Laravel applications including: strict models, automatically eager loaded relationships, immutable dates, and more! 

> **Requires [PHP 8.3+](https://php.net/releases/)**, **[Laravel 11+](https://laravel.com/docs/11.x/)**.

> **Note:** This package is a **work in progress (don't use it yet)**, and it modifies the default behavior of Laravel. It is recommended to use it in new projects or when you are comfortable with the changes it introduces.

## Installation

⚡️ Get started by requiring the package using [Composer](https://getcomposer.org):

```bash
composer require nunomaduro/essentials:@dev
```

## Features

Essentials provides a set of configurable features that enhance your Laravel application with better defaults. Each feature can be enabled or disabled individually through configuration.

### Model Enhancements

#### Strict Mode (`ShouldBeStrict`)

* **Rationale**: Laravel's Eloquent models are very permissive by default, allowing undefined attributes, silent attribute discarding, and lazy loading in contexts where it might cause performance issues.
* **What it does**: Enables strict mode for Eloquent models, which:
    - Prevents accessing missing attributes (throws exceptions instead of returning null)
    - Prevents lazy loading (forces you to explicitly load relationships)
    - Prevents silently discarding attributes (throws exceptions when setting attributes that don't exist)
* **Benefits**: Catches potential bugs early, improves performance by preventing N+1 query issues, and makes your code more explicit and predictable.

#### Automatic Eager Loading (`AutomaticallyEagerLoadRelationships`)

* **Rationale**: N+1 query problems are one of the most common performance issues in Laravel applications.
* **What it does**: Automatically eager loads relationships defined in your model's `$with` property, eliminating the need to manually call `with()` in your queries.
* **Benefits**: Reduces database queries, improves application performance, and helps prevent N+1 query issues without requiring manual intervention.

#### Unguarded Models (`Unguard`)

* **Rationale**: Laravel's mass assignment protection is important for security, but in controlled environments or during development, it can sometimes be cumbersome.
* **What it does**: Disables mass assignment protection for all models, allowing you to create and update models without explicitly defining which attributes are fillable.
* **Benefits**: Simplifies development workflow and reduces boilerplate code. Note that this is disabled by default and should only be enabled in controlled environments.

### Date Handling

#### Immutable Dates (`ImmutableDates`)

* **Rationale**: Mutable dates can lead to unexpected bugs when date objects are modified after being passed around in your application.
* **What it does**: Configures Laravel to use Carbon Immutable for all date handling, ensuring that date objects cannot be modified after creation.
* **Benefits**: Prevents unexpected side effects from date mutations, makes date handling more predictable, and encourages a more functional programming style.

### Security Enhancements

#### Force HTTPS (`ForceScheme`)

* **Rationale**: HTTPS is essential for secure web applications, but Laravel doesn't force HTTPS by default.
* **What it does**: Forces all generated URLs to use HTTPS, ensuring that your application's links are always secure.
* **Benefits**: Improves security, prevents mixed content warnings, and ensures a consistent experience for users.

#### Prohibit Destructive Commands (`ProhibitDestructiveCommands`)

* **Rationale**: Accidentally running destructive commands (like migrations:fresh) in production can be catastrophic.
* **What it does**: Prevents destructive commands from running in production environments, adding an extra layer of protection against accidental data loss.
* **Benefits**: Reduces the risk of accidental data loss in production environments and provides peace of mind when running commands.

### Frontend Optimizations

#### Aggressive Prefetching (`AggressivePrefetching`)

* **Rationale**: Modern web applications often require multiple JavaScript and CSS files, which can slow down page loads if not optimized.
* **What it does**: Configures Laravel Vite to use aggressive prefetching for assets, which preloads resources before they're needed.
* **Benefits**: Improves perceived performance, reduces load times for subsequent page visits, and enhances the overall user experience.

## Configuration

All features are configurable through the `essentials.php` config file. By default, most features are enabled, but you can disable any feature by setting its configuration value to `false`:

```php
// config/essentials.php
return [
    NunoMaduro\Essentials\Configurables\ShouldBeStrict::class => true,
    NunoMaduro\Essentials\Configurables\Unguard::class => false,
    // other configurables...
];
```

## Roadmap

- Better defaults before each test case, like: freeze time, prevent HTTP requests, etc.
- Better Pint configuration by default
- General cleanup of the skeleton
- Additional configurables for common Laravel patterns

## License

**Essentials** was created by **[Nuno Maduro](https://twitter.com/enunomaduro)** under the **[MIT license](https://opensource.org/licenses/MIT)**.