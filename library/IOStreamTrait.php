<?php

declare(strict_types=1);

namespace Cli;

/**
 * @method getStream()
 */
trait IOStreamTrait
{
    private Di $di;

    private int $lineLength = 128;
    private int $paddingLength = 8;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function getDi(): Di
    {
        return $this->di;
    }

    public function getEol(): string
    {
        return \PHP_EOL;
    }

    public function setLineLength(int $bytes): void
    {
        $this->lineLength = $bytes;
    }

    public function getLineLength(): int
    {
        return $this->lineLength;
    }

    public function setPaddingLength(int $times): void
    {
        $this->paddingLength = $times;
    }

    public function getPaddingLength(): int
    {
        return $this->paddingLength;
    }

    public function wordwrap(string $text): string
    {
        return \wordwrap(
            $text,
            $this->getLineLength(),
            $this->getEol(),
            true,
        );
    }

    public function padText(string $text, bool $front = true, bool $back = true): string
    {
        if ($front) {
            $text = \str_repeat(' ', $this->getPaddingLength()) . $text;
        }

        if ($back) {
            $text .= \str_repeat(' ' , $this->getPaddingLength());
        }

        return $text;
    }
}