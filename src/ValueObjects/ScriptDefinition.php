<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\ValueObjects;

final readonly class ScriptDefinition
{
    public function __construct(
        public string $name,
        public string $command,
        public string $package,
        public string $version,
        public string $description,
    ) {}
}
