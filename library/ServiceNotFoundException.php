<?php

declare(strict_types=1);

namespace Cli;

class ServiceNotFoundException extends FormattableException
{
    public function __construct(string $name)
    {
        parent::__construct(
            'Unable to find service "%s"', $name,
        );
    }
}