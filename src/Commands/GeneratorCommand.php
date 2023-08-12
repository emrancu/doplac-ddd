<?php

namespace Doplac\Domain\Commands;

use Doplac\Domain\Traits\GeneratorOverride;
use Illuminate\Console\GeneratorCommand;

abstract class Generator extends GeneratorCommand
{
    use GeneratorOverride;
    
}
