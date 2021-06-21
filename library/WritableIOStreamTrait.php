<?php

declare(strict_types=1);

namespace Cli;

/**
 * @method Di getDi()
 * @method getStream()
 * @method void setLineLength(int $bytes)
 * @method int getLineLength()
 * @method void setPaddingLength(int $times)
 * @method int getPaddingLength()
 * @method string wordwrap(string $text)
 * @method string padText(string $text, bool $front = true, bool $back = true)
 */
trait WritableIOStreamTrait
{
    public function write(string|\Stringable $text): int
    {
        $bytes = @\fwrite(
            $this->getStream(),
            (string) $text,
        );

        return $bytes ?: 0;
    }

    public function writeF(string $format, mixed ...$args): int
    {
        return $this->write(
            \sprintf(...\func_get_args()),
        );
    }

    public function writeLine(string|\Stringable $text): int
    {
        return $this->write($text) + $this->writeEol();
    }

    public function writeLineF(string $format, mixed ...$args): int
    {
        return $this->writeLine(
            \sprintf(...\func_get_args()),
        );
    }

    public function writeLines(array $lines): int
    {
        $bytes = 0;

        foreach ($lines as $line) {
            $bytes += $this->writeLine($line);
        }

        return $bytes;
    }

    public function writeBox(string|array $text, Color $color, Color $backgroundColor): int
    {
        if (!\is_array($text)) {
            $text = [
                $text,
            ];
        }

        $emptyLine = $backgroundColor->background(
            \str_repeat(' ', $this->getLineLength()),
        );

        $lines = [
            $emptyLine,
        ];

        foreach ($text as $line) {
            $line = $this->padText($line);

            foreach (\explode($this->getEol(), $this->wordwrap($line)) as $wrappedLine) {
                if (\strlen($wrappedLine) < $this->getLineLength()) {
                    $wrappedLine .= \str_repeat(' ', $this->getLineLength() - \strlen($wrappedLine));
                }

                $lines[] = $backgroundColor->background(
                    $color->text($wrappedLine),
                );
            }
        }

        $lines[] = $emptyLine;

        return $this->writeLines($lines);
    }

    public function writeSuccessBox(string|array $text): int
    {
        return $this->writeBox(
            text: $text,
            color: Color::BLACK,
            backgroundColor: Color::GREEN,
        );
    }

    public function writeErrorBox(string|array $text): int
    {
        return $this->writeBox(
            text: $text,
            color: Color::BLACK,
            backgroundColor: Color::RED,
        );
    }

    public function writeEol(int $times = 1): int
    {
        return $this->write(
            \str_repeat($this->getEol(), $times),
        );
    }

    public function cursor(Cursor ...$cursors): void
    {
        foreach ($cursors as $cursor) {
            $this->write($cursor->sequence());
        }
    }

    public function scrollUp(int $lines): void
    {
        $this->write("\033[{$lines}S");
    }

    public function scrollDown(int $lines): void
    {
        $this->write("\033[{$lines}T");
    }
}