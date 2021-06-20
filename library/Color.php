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
    case BRIGHT_BLACK;
    case BRIGHT_RED;
    case BRIGHT_GREEN;
    case BRIGHT_YELLOW;
    case BRIGHT_BLUE;
    case BRIGHT_MAGENTA;
    case BRIGHT_CYAN;
    case BRIGHT_WHITE;
    case DEFAULT;

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
            self::BRIGHT_BLACK => "\033[90m",
            self::BRIGHT_RED => "\033[91m",
            self::BRIGHT_GREEN => "\033[92m",
            self::BRIGHT_YELLOW => "\033[93m",
            self::BRIGHT_BLUE => "\033[94m",
            self::BRIGHT_MAGENTA => "\033[95m",
            self::BRIGHT_CYAN => "\033[96m",
            self::BRIGHT_WHITE => "\033[97m",
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
            self::BRIGHT_BLACK => "\033[100m",
            self::BRIGHT_RED => "\033[101m",
            self::BRIGHT_GREEN => "\033[102m",
            self::BRIGHT_YELLOW => "\033[103m",
            self::BRIGHT_BLUE => "\033[104m",
            self::BRIGHT_MAGENTA => "\033[105m",
            self::BRIGHT_CYAN => "\033[106m",
            self::BRIGHT_WHITE => "\033[107m",
            self::DEFAULT => "\033[49m",
        };
    }
}