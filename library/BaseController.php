<?php

declare(strict_types=1);

namespace Cli;

use Cli\Services\Stderr;
use Cli\Services\Stdin;
use Cli\Services\Stdout;

abstract class BaseController
{
    private Di $di;

    protected Stdin $stdin;
    protected Stdout $stdout;
    protected Stderr $stderr;

    final public function __construct(Di $di)
    {
        $this->di = $di;

        $this->stdin = $di->get(Stdin::class);
        $this->stdout = $di->get(Stdout::class);
        $this->stderr = $di->get(Stderr::class);
    }

    protected function getDi(): Di
    {
        return $this->di;
    }
}