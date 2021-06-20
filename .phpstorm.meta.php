<?php

namespace PHPSTORM_META
{
    override(
        \Cli\Di::get(0),
        map(
            [
                '' => '@',
            ],
        ),
    );
}
