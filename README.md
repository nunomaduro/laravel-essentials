<p align="center"><img src="art/logo.svg" width="50%" alt="Logo Laravel Essentials"></p>

<p align="center">
    <a href="https://github.com/nunomaduro/essentials/actions"><img src="https://github.com/nunomaduro/essentials/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/dt/nunomaduro/essentials" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/v/nunomaduro/essentials" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/l/nunomaduro/essentials" alt="License"></a>
</p>

<a name="introduction"></a>
Essentials provides **better defaults** for your Laravel applications including: strict models, automatically eager loaded relationships, immutable dates, and more! 

> **Requires [PHP 8.3+](https://php.net/releases/)**, **[Laravel 11+](https://laravel.com/docs/11.x/)**.

> **Note:** This package is a **work in progress (don't use it yet)**, and it modifies the default behavior of Laravel. It is recommended to use it in new projects or when you are comfortable with the changes it introduces.

⚡️ Get started by requiring the package using [Composer](https://getcomposer.org):

```bash
composer require nunomaduro/essentials:@dev
```

Done:

- Models: strict, automatically eager loaded relationships.
- Dates: immutable, strict.
- Commands: prohibit destructive commands in production.
- And force https URLs, aggresive prefeching on vite, and much more...

Roadmap:

- Better defaults before each test case, like: freeze time, prevent http requests, etc.
- Better Pint configuration by default.
- General cleanup of the skeleton.
- etc...

**Essentials** was created by **[Nuno Maduro](https://twitter.com/enunomaduro)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
