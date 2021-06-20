<?php

declare(strict_types=1);

namespace Cli;

use Cli\Services\Stdout;

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
trait ReadableIOStreamTrait
{
    public function read(int $maxLength = -1, int $offset = -1, bool $trim = true): ?string
    {
        $input = @\stream_get_contents(
            $this->getStream(),
            $maxLength,
            $offset,
        );

        if ($input === false) {
            return null;
        }

        if ($trim) {
            $input = \trim($input);
        }

        return $input;
    }

    public function getBooleanInput(): bool
    {
        $input = \strtolower(
            $this->read(3) ?? '',
        );

        return $input === 'y' || $input === '1';
    }

    public function askYesNo(string $question, bool $includeHelp = true): bool
    {
        if ($includeHelp) {
            $question .= \sprintf(
                ' [Y/N]',
            );
        }

        $this->getDi()->get(Stdout::class)->writeLine(
            $question,
        );

        return $this->getBooleanInput();
    }
}