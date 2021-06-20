<?php

declare(strict_types=1);

namespace Cli;

class Di
{
    private array $services = [];
    private array $initializers = [];

    public function service(string $name, object $object): void
    {
        $this->services[$name] = $object;
    }

    public function lazyService(string $name, \Closure $initializer): void
    {
        $this->initializers[$name] = $initializer;
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->services) || \array_key_exists($name, $this->initializers);
    }

    public function get(string $name): object
    {
        if (\array_key_exists($name, $this->initializers)) {
            $this->services[$name] = ($this->initializers[$name])($this);

            unset($this->initializers[$name]);
        }

        if (!\array_key_exists($name, $this->services)) {
            throw new ServiceNotFoundException($name);
        }

        return $this->services[$name];
    }
}