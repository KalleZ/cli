<?php

declare(strict_types=1);

namespace Cli\Contracts;

use Cli\Di;

interface IOStream
{
    public function __construct(Di $di);
    public function getDi(): Di;
    public function getStream();

    public function setLineLength(int $bytes): void;
    public function getLineLength(): int;
    public function setPaddingLength(int $times): void;
    public function getPaddingLength(): int;

    public function wordwrap(string $text): string;
    public function padText(string $text, bool $front = true, bool $back = true): string;
}