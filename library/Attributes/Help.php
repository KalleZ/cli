<?php

declare(strict_types=1);

namespace Cli\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Help
{
    public function __construct(
        private string $text,
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }
}