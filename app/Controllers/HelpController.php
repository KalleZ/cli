<?php

declare(strict_types=1);

namespace Cli\Controllers;

use Cli\Attributes\Command;
use Cli\Attributes\Controller;
use Cli\Attributes\Help;
use Cli\BaseController;
use Cli\Config;
use Cli\FormattableException;
use Cli\Router;

#[Controller(group: 'help')]
class HelpController extends BaseController
{
    #[Help(text: 'This help, use --explain=<command> for more information')]
    public function __invoke(string $explain = null): void
    {
        $list = [];
        $columnLength = 0;

        foreach ($this->getControllers() as $group => $controller) {
            if (self::hasRootCommand($controller)) {
                $list[\strtolower($group)] = self::getCommandHelpText(
                    $controller->getMethod('__invoke'),
                );

                $columnLength = \max($columnLength, \strlen($group));
            }

            foreach (self::getCommands($controller) as $command => $method) {
                $list[\strtolower($group . ':' . $command)] = self::getCommandHelpText(
                    $method,
                );

                $columnLength = \max($columnLength, \strlen($group . ':' . $command));
            }
        }

        if ($explain !== null) {
            $explain = \strtolower($explain);
            $route = $this->getDi()->get(Router::class)->getRoute($explain);

            if (!\array_key_exists($explain, $list) || $route === null) {
                throw new FormattableException(
                    'Cannot explain command "%s" as it does not exists"',
                    $explain,
                );
            }

            $this->stdout->writeLine($explain);

            if ($list[$explain] !== null) {
                $this->stdout->writeLine($list[$explain]);
            }

            $args = self::getCommandArguments(
                new \ReflectionMethod(
                    $route->getController(),
                    $route->getAction(),
                ),
            );
            
            if (\sizeof($args) > 0) {
                $this->stdout->writeEol();
                $this->stdout->writeLine('Arguments:');

                foreach ($args as $argument) {
                    $this->stdout->writeLine(
                        \str_repeat(' ', 8) . $argument,
                    );
                }
            }
        } else {
            foreach ($list as $command => $help) {
                $line = \str_pad(
                    $command,
                    $columnLength + $this->stdout->getPaddingLength(),
                );

                if ($help !== null) {
                    $line .= $help;
                }

                $this->stdout->writeLine($line);
            }
        }
    }

    /**
     * @return array<string, \ReflectionClass<BaseController>>
     */
    private function getControllers(): array
    {
        $controllers = [];

        foreach ($this->getDi()->get(Config::class)->getArray('controllers') as $controller) {
            $class = new \ReflectionClass($controller);
            $attrs = $class->getAttributes(Controller::class);

            if (\sizeof($attrs) !== 1) {
                throw new FormattableException(
                    'Controller "%s" does not have a group',
                    $class->getName(),
                );
            }

            $controllers[$attrs[0]->newInstance()->getGroup()] = $class;
        }

        return $controllers;
    }

    private static function hasRootCommand(\ReflectionClass $controller): bool
    {
        return $controller->hasMethod('__invoke');
    }

    /**
     * @return array<string, \ReflectionMethod>
     */
    private static function getCommands(\ReflectionClass $controller): array
    {
        $commands = [];

        foreach ($controller->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $attrs = $method->getAttributes(Command::class);

            if (\sizeof($attrs) !== 1) {
                continue;
            }

            $commands[$attrs[0]->newInstance()->getName()] = $method;
        }

        return $commands;
    }

    private static function getCommandHelpText(\ReflectionMethod $method): ?string
    {
        $attrs = $method->getAttributes(Help::class);

        if (\sizeof($attrs) > 0) {
            return $attrs[0]->newInstance()->getText();
        }

        return null;
    }

    private static function getCommandArguments(\ReflectionMethod $method): array
    {
        $args = [];

        foreach ($method->getParameters() as $parameter) {
            $arg = \sprintf(
                '--%s=<%s>',
                $parameter->getName(),
                $parameter->getType()->getName(),
            );

            if ($parameter->isOptional()) {
                $default = $parameter->getDefaultValue();

                if ($default !== null) {
                    $default = \sprintf(
                        ' [Default: %s]',
                        $default,
                    );
                }

                $arg .= \sprintf(
                    ' [OPTIONAL]%s',
                    $default,
                );
            } else {
                $arg .= ' [REQUIRED]';
            }

            $args[] = $arg;
        }

        return $args;
    }
}