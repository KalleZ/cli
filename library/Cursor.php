<?php

declare(strict_types=1);

namespace Cli;

enum Cursor
{
    case STOP_BLINK;
    case START_BLINK;
    case HIDE;
    case SHOW;
    case UNDERLINE;
    case BEAM;
    case BLOCK;

    public function sequence(): string
    {
        return match ($this) {
            self::STOP_BLINK => "\033[?12l",
            self::START_BLINK => "\033[?12h",
            self::HIDE => "\033[?25l",
            self::SHOW => "\033[?25h",
            self::UNDERLINE => "\033[4 q",
            self::BEAM => "\033[6 q",
            self::BLOCK => "\033[0 q",
        };
    }
}