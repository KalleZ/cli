<?php

declare(strict_types=1);

namespace Cli;

class Text implements \Stringable
{
    public function __construct(
        private string $text,
        private Color $color = Color::DEFAULT,
        private Color $backgroundColor = Color::DEFAULT,
    ) {
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function setColor(Color $color): void
    {
        $this->color = $color;
    }

    public function getBackgroundColor(): Color
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(Color $color)
    {
        $this->backgroundColor = $color;
    }

    public function __toString(): string
    {
        return $this->backgroundColor->background(
            $this->color->text(
                $this->text,
            ),
        );
    }
}