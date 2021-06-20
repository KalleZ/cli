<?php

declare(strict_types=1);

namespace Cli;

class AutoloadNotFoundException extends FormattableException
{
    public function __construct(string $symbol)
    {
        parent::__construct(
            'Unable to autoload symbol "%s"', $symbol,
        );
    }
}