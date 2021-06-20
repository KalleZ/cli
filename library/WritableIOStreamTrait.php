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
    public function write(string $text, Color $color = Color::DEFAULT, Color $backgroundColor = Color::DEFAULT): int
    {
        $bytes = @\fwrite(
            $this->getStream(),
            $backgroundColor->background(
                $color->text($text),
            ),
        );

        return $bytes ?: 0;
    }

    public function writeF(string $format, mixed ...$args): int
    {
        return $this->write(
            \sprintf(...\func_get_args()),
        );
    }

    public function writeLine(string $text, Color $color = Color::DEFAULT, Color $backgroundColor = Color::DEFAULT): int
    {
        return $this->write($text, $color, $backgroundColor) + $this->writeEol();
    }

    public function writeLineF(string $format, mixed ...$args): int
    {
        return $this->writeLine(
            \sprintf(...\func_get_args()),
        );
    }

    public function writeLines(array $lines, Color $color = Color::DEFAULT, Color $backgroundColor = Color::DEFAULT): int
    {
        $bytes = 0;

        foreach ($lines as $line) {
            $bytes += $this->writeLine($line, $color, $backgroundColor);
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

            foreach (\explode(\PHP_EOL, $this->wordwrap($line)) as $wrCliedLine) {
                if (\strlen($wrCliedLine) < $this->getLineLength()) {
                    $wrCliedLine .= \str_repeat(' ', $this->getLineLength() - \strlen($wrCliedLine));
                }

                $lines[] = $backgroundColor->background(
                    $color->text($wrCliedLine),
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
            \str_repeat(\PHP_EOL, $times),
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