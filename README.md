Essentials provides **better defaults** for your Laravel applications, including:

Done:

- Models: unguarded, strict, automatically eager loaded relationships.
- Dates: immutable, strict.
- Commands: prohibit destructive commands in production.
- And force https URLs, aggresive prefeching on vite, and much more...

Roadmap:

- Better defaults before each test case, like: freeze time, prevent http requests, etc.
- Better Pint configuration by default.
- General cleanup of the skeleton.
- etc...

> **Requires [PHP 8.3+](https://php.net/releases/)**, **[Laravel 11+](https://laravel.com/docs/11.x/)**.

> **Note:** This package is a **work in progress (don't use it yet)**, and it modifies the default behavior of Laravel. It is recommended to use it in new projects or when you are comfortable with the changes it introduces.

⚡️ Get started by requiring the package using [Composer](https://getcomposer.org):

```bash
composer require nunomaduro/essentials:@dev
```

**Essentials** was created by **[Nuno Maduro](https://twitter.com/enunomaduro)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
