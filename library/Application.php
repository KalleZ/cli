<?php

declare(strict_types=1);

namespace Cli;

use Cli\Services;

class Application
{
    private Di $di;

    public function __construct(Di $di = null)
    {
        $this->di = $di ?? new Di();

        $this->di->lazyService(Services\Stdin::class, fn(Di $di): Services\Stdin => new Services\Stdin($di));
        $this->di->lazyService(Services\Stdout::class, fn(Di $di): Services\Stdout => new Services\Stdout($di));
        $this->di->lazyService(Services\Stderr::class, fn(Di $di): Services\Stderr => new Services\Stderr($di));

        if (!$this->di->has(Router::class)) {
            $this->di->service(Router::class, new Router());
        }

        if (!$this->di->has(Config::class)) {
            $this->di->service(Config::class, Config::createFromDefault());

            foreach ($this->di->get(Config::class)->getArray('controllers') as $controller) {
                $this->di->get(Router::class)->mount($controller);
            }
        }

        if (!$this->di->has(Dispatcher::class)) {
            $this->di->service(Dispatcher::class, new Dispatcher());
        }
    }

    public function run(): void
    {
        $argv = $_SERVER['argv'];

        \array_shift($argv);

        if ($_SERVER['argc'] - 1 === 0) {
            throw new \Exception('Nothing to do');
        }

        $route = $this->di->get(Router::class)->getRoute($argv[0]);

        if ($route === null) {
            throw new RouterException(
                'Invalid command "%s"',
                $argv[0],
            );
        }

        \array_shift($argv);

        $this->di->get(Dispatcher::class)->dispatch(
            $this->di,
            $route,
            $argv,
        );
    }
}