<?php

declare(strict_types=1);

namespace Cli;

class Dispatcher
{
    public function dispatch(Di $di, Route $route, array $arguments): void
    {
        $args = [];
        $numArguments = \sizeof($arguments);

        $method = new \ReflectionMethod(
            $route->getController(),
            $route->getAction(),
        );

        if ($numArguments < $method->getNumberOfRequiredParameters()) {
            throw new RouterException(
                'Too few arguments for command',
            );
        } elseif ($numArguments > $method->getNumberOfParameters()) {
            throw new RouterException(
                'Too many arguments for command',
            );
        }

        if ($numArguments > 0) {
            foreach ($method->getParameters() as $parameter) {
                $found = false;

                foreach ($arguments as $index => $argument) {
                    [$name, $value] = self::parseArgument($argument);

                    if (\strcasecmp($parameter->getName(), $name) === 0) {
                        $found = true;

                        break;
                    }
                }

                if (!$found) {
                    continue;
                }

                $type = $parameter->getType();

                if (!$type->allowsNull()) {
                    if ($type->getName() === 'bool') {
                        $value = $value === null ? true : (bool) $value;
                    } elseif ($value === null && $parameter->getDefaultValue()) {
                        $value = $parameter->getDefaultValue();
                    } elseif ($value === null) {
                        throw new RouterException(
                            'Command must have a value',
                        );
                    }
                }

                unset($arguments[$index]);

                if ($value !== null) {
                    $type = $type->getName();

                    if ($type === 'mixed') {
                        $type = 'string';
                    }

                    \settype($value, $type);
                }

                $args[$name] = $value;
            }

            if (\sizeof($arguments) > 0) {
                throw new RouterException(
                    'Invalid argument "%s"',
                    \explode('=', \end($arguments), 2)[0],
                );
            }
        }

        $controller = new ($route->getController())($di);
        $controller->{$route->getAction()}(...$args);
    }

    private static function parseArgument(string $argument): array
    {
        if (\strpos($argument, '--') !== 0) {
            throw new RouterException(
                'Command must begin with "--"',
            );
        }

        $parts = \explode('=', $argument, 2);
        $parts[0] = \substr($parts[0], 2);

        if (\sizeof($parts) === 1) {
            $parts[1] = null;
        }

        return $parts;
    }
}