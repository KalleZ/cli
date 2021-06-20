<?php

declare(strict_types=1);

namespace Cli;

use Cli\Attributes\Command;
use Cli\Attributes\Controller;

class Router
{
    protected array $routes = [];

    public function mount(string $controller): void
    {
        $class = new \ReflectionClass($controller);
        $attrs = $class->getAttributes(Controller::class);

        if (\sizeof($attrs) !== 1) {
            throw new RouterException(
                'Cannot mount controller [%s]: Missing or too many controller attributes; there must only be one',
                $controller,
            );
        }

        $group = \strtolower($attrs[0]->newInstance()->getGroup());

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!$method->isUserDefined() || $method->isConstructor() || $method->isDestructor()) {
                continue;
            }

            $attrs = $method->getAttributes(Command::class);

            if (\sizeof($attrs) !== 1 && $method->getName() !== '__invoke') {
                throw new RouterException(
                    'Cannot mount controller action [%s::%s]: Missing or too many command attributes; there must only be one',
                    $controller,
                    $method->getName(),
                );
            }

            $returnType = $method->getReturnType()?->getName();

            if ($returnType === null || $returnType !== 'void' || $method->returnsReference()) {
                throw new RouterException(
                    'Cannot mount controller action [%s::%s]: Method must have a "void" return type',
                    $controller,
                    $method->getName(),
                );
            }

            if ($method->isVariadic()) {
                throw new RouterException(
                    'Cannot mount controller action [%s::%s]: Method has one or more variadic parameters; none allowed',
                    $controller,
                    $method->getName(),
                );
            } elseif ($method->isStatic()) {
                throw new RouterException(
                    'Cannot mount controller action [%s::%s]: Method must not be static',
                    $controller,
                    $method->getName(),
                );
            }

            foreach ($method->getParameters() as $parameter) {
                if ($parameter->isPassedByReference()) {
                    throw new RouterException(
                        'Cannot mount controller action [%s::%s]: Parameter "$%s" must not be a reference',
                        $controller,
                        $method->getName(),
                        $parameter->getName(),
                    );
                }

                if ($parameter->getType() === null) {
                    throw new RouterException(
                        'Cannot mount controller action [%s::%s]: Parameter "$%s" must have a type',
                        $controller,
                        $method->getName(),
                        $parameter->getName(),
                    );
                }

                $type = $parameter->getType();

                if ($type instanceof \ReflectionUnionType) {
                    $types = $type->getTypes();

                    if (\sizeof($types) !== 2 || !$type->allowsNull()) {
                        throw new RouterException(
                            'Cannot mount controller action [%s::%s]: Parameter "$%s" must be a simple nullable union',
                            $controller,
                            $method->getName(),
                            $parameter->getName(),
                        );
                    }

                    foreach ($types as $type) {
                        if (!$type->allowsNull()) {
                            break;
                        }
                    }
                } elseif ($type instanceof \ReflectionIntersectionType) {
                    throw new RouterException(
                        'Cannot mount controller action [%s::%s]: Parameter "$%s" must not be an intersection type',
                        $controller,
                        $method->getName(),
                        $parameter->getName(),
                    );
                }

                if (!$type->isBuiltin() || $type->getName() === 'object' || $type->getName() === 'array') {
                    throw new RouterException(
                        'Cannot mount controller action [%s::%s]: Parameter "$%s" must be a builtin type and must be a scalar',
                        $controller,
                        $method->getName(),
                        $parameter->getName(),
                    );
                }

                if ($type->getName() === 'bool' && $parameter->getDefaultValue() === null) {
                    throw new RouterException(
                        'Cannot mount controller action [%s::%s]: Parameter "$%s" must have a boolean default value',
                        $controller,
                        $method->getName(),
                        $parameter->getName(),
                    );
                }
            }

            if ($method->getName() === '__invoke') {
                $this->registerRootAction($group, $controller);

                continue;
            }

            // @todo Support camelCase expanding commands: $myName => --my-name
            // @todo Support --with and --without for boolean commands

            $this->registerAction(
                $group,
                \strtolower($attrs[0]->newInstance()->getName()),
                $controller,
                $method->getName(),
            );
        }
    }

    final protected function reindex(): void
    {
        \krsort($this->routes);
    }

    protected function registerAction(string $group, string $command, string $controller, string $action): void
    {
        $this->routes[$group . ':' . $command] = new Route(
            controller: $controller,
            action: $action,
        );

        $this->reindex();
    }

    protected function registerRootAction(string $group, string $controller): void
    {
        $this->routes[$group] = new Route(
            controller: $controller,
            action: '__invoke',
        );

        $this->reindex();
    }

    public function getRoute(string $command): ?Route
    {
        $command = \strtolower($command);

        if (\array_key_exists($command, $this->routes)) {
            return $this->routes[$command];
        }

        return null;
    }
}