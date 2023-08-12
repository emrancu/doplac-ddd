<?php

namespace Doplac\Override;

use Doplac\Domain\Traits\GeneratorOverride;
use Illuminate\Console\GeneratorCommand as GeneratorCommandOld;

abstract class GeneratorCommand extends GeneratorCommandOld
{
    use GeneratorOverride;
    
}
