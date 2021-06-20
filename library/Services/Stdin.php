<?php

declare(strict_types=1);

namespace Cli\Services;

use Cli\Contracts\IOStream;
use Cli\Contracts\ReadableIOStream;
use Cli\IOStreamTrait;
use Cli\ReadableIOStreamTrait;

class Stdin implements IOStream, ReadableIOStream
{
    use IOStreamTrait;
    use ReadableIOStreamTrait;

    public function getStream()
    {
        return \STDIN;
    }
}