<?php

declare(strict_types=1);

namespace Cli\Contracts;

interface ReadableIOStream
{
    public function read(int $maxLength = -1, int $offset = -1, bool $trim = true): ?string;

    public function getBooleanInput(): bool;
    public function askYesNo(string $question, bool $includeHelp = true): bool;
}