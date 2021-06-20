<?php

declare(strict_types=1);

namespace Cli\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Controller
{
    public function __construct(
        private string $group,
    ) {
    }

    public function getGroup(): string
    {
        return $this->group;
    }
}