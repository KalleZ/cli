<?php

declare(strict_types=1);

namespace Cli;

class Config
{
    private const DEFAULT_CONFIG_FILE = '.config/cli.json';
    private const TYPE_JSON = 'json';

    protected object $config;
    protected string $type;
    protected string $file;

    public function __construct(object $config)
    {
        $this->config = $config;
    }

    final public static function createFromJsonFile(string $file): static
    {
        $json = @\file_get_contents($file);

        if ($json === false) {
            throw new ConfigException(
                'Unable to load JSON config file: %s',
                $file,
            );
        }

        try {
            $json = \json_decode(
                json: $json,
                flags: \JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new ConfigException(
                'Unable to parse JSON config file [%s]: %s',
                $file,
                $e->getMessage(),
            );
        }

        $config = new static($json);

        $config->type = self::TYPE_JSON;
        $config->file = $file;

        return $config;
    }

    final public static function createFromDefault(): static
    {
        return self::createFromJsonFile(
            \str_replace(
                [
                    '/',
                    '\\',
                ],
                \DIRECTORY_SEPARATOR,
                \getcwd() . '/' . self::DEFAULT_CONFIG_FILE,
            ),
        );
    }

    protected function getTyped(string $name, string $type): mixed
    {
        $value = $this->get($name);

        if (\gettype($value) !== $type) {
            throw new ConfigException(
                'Cannot fetch configuration directive "%s"; value is not of type "%s"',
                $name,
                $type,
            );
        }

        return $value;
    }

    public function get(string $name): mixed
    {
        if (!\property_exists($this->config, $name)) {
            throw new ConfigException(
                'Invalid configuration directive: %s',
                $name,
            );
        }

        return $this->config->{$name};
    }

    public function getString(string $name): string
    {
        return $this->getTyped($name, 'string');
    }

    public function getInteger(string $name): int
    {
        return $this->getTyped($name, 'integer');
    }

    public function getFloat(string $name): float
    {
        return $this->getTyped($name, 'double');
    }

    public function getArray(string $name): array
    {
        return $this->getTyped($name, 'array');
    }

    public function getObject(string $name): object
    {
        return $this->getTyped($name, 'object');
    }
}