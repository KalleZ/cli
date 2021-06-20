<?php

declare(strict_types=1);

namespace Cli;

enum Color
{
    case BLACK;
    case RED;
    case GREEN;
    case YELLOW;
    case BLUE;
    case MAGENTA;
    case CYAN;
    case WHITE;
    case DEFAULT;

    // @todo Bright colors?
    // @todo Bright backgrounds?

    public function text(string $text): string
    {
        return \sprintf(
            "%s%s\033[0m",
            $this->textColorSequence(),
            $text,
        );
    }

    public function background(string $text): string
    {
        return \sprintf(
            "%s%s\033[0m",
            $this->backgroundColorSequence(),
            $text,
        );
    }

    public function textColorSequence(): string
    {
        return match ($this) {
            self::BLACK => "\033[30m",
            self::RED => "\033[31m",
            self::GREEN => "\033[32m",
            self::YELLOW => "\033[33m",
            self::BLUE => "\033[34m",
            self::MAGENTA => "\033[35m",
            self::CYAN => "\033[36m",
            self::WHITE => "\033[37m",
            self::DEFAULT => "\033[39m",
        };
    }

    public function backgroundColorSequence(): string
    {
        return match ($this) {
            self::BLACK => "\033[40m",
            self::RED => "\033[41m",
            self::GREEN => "\033[42m",
            self::YELLOW => "\033[43m",
            self::BLUE => "\033[44m",
            self::MAGENTA => "\033[45m",
            self::CYAN => "\033[46m",
            self::WHITE => "\033[47m",
            self::DEFAULT => "\033[49m",
        };
    }
}