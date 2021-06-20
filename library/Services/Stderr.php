<?php

declare(strict_types=1);

namespace Cli\Services;

use Cli\Contracts\IOStream;
use Cli\Contracts\WritableIOStream;
use Cli\IOStreamTrait;
use Cli\WritableIOStreamTrait;

class Stderr implements IOStream, WritableIOStream
{
    use IOStreamTrait;
    use WritableIOStreamTrait;

    public function getStream()
    {
        return \STDERR;
    }
}