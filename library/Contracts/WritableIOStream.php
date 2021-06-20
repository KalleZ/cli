<?php

declare(strict_types=1);

namespace Cli\Contracts;

use Cli\Color;
use Cli\Cursor;

interface WritableIOStream
{
    public function write(string|\Stringable $text): int;
    public function writeF(string $format, mixed ...$args): int;
    public function writeLine(string|\Stringable $text): int;
    public function writeLineF(string $format, mixed ...$args): int;
    public function writeLines(array $lines): int;

    public function writeBox(string|array $text, Color $color, Color $backgroundColor): int;
    public function writeSuccessBox(string|array $text): int;
    public function writeErrorBox(string|array $text): int;

    public function writeEol(int $times = 1): int;
    public function getEol(): string;

    public function cursor(Cursor ...$cursors): void;

    public function scrollUp(int $lines): void;
    public function scrollDown(int $lines): void;
}