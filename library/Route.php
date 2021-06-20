<?php

declare(strict_types=1);

namespace Cli;

class Route
{
    public function __construct(
        private string $controller,
        private string $action,
    ) {
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}