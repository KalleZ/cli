<?php

declare(strict_types=1);

namespace Cli;

class Loader
{
    private static $imports = [];

    public static function autoload(string $symbol): void
    {
        $symbol = '\\' . $symbol;
        $namespace = \substr($symbol, 0, \strrpos($symbol, '\\'));

        do {
            if(\array_key_exists($namespace, self::$imports)) {
                $cwd = self::$imports[$namespace];

                break;
            }
        } while ($namespace = \substr($namespace, 0, \strrpos($namespace, '\\')));

        if (!isset($cwd)) {
            $cwd = \getcwd();
        }

        if (\PHP_OS_FAMILY !== 'Windows') {
            $filename = \str_replace(
                [
                    '\\',
                    $namespace,
                ],
                [
                    '/',
                    '',
                ],
                $symbol,
            );
        } else {
            $filename = \str_replace(
                $namespace,
                '',
                $symbol,
            );
        }

        $path = $cwd . $filename . '.php';

        if (!\is_file($path)) {
            throw new AutoloadNotFoundException($symbol);
        }

        require $path;
    }

    public static function register(array $imports = []): void
    {
        \krsort($imports);

        self::$imports = $imports;

        \spl_autoload_register('\Cli\Loader::autoload');
    }

    public static function unregister(): void
    {
        self::$imports = [];

        \spl_autoload_unregister('\Cli\Loader::autoload');
    }
}