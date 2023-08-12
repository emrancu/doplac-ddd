<?php

namespace Illuminate\Console;

use Doplac\Domain\Traits\GeneratorOverride;
use Illuminate\Console\GeneratorCommand as GeneratorCommandOld;

abstract class Generator extends GeneratorCommandOld
{
    use GeneratorOverride;
    
}
