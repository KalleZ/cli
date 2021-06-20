<?php

declare(strict_types=1);

namespace Cli\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Command
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}