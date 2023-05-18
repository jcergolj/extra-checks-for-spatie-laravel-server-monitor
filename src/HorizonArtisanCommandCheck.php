<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class HorizonArtisanCommandCheck extends CheckDefinition
{
    public $command = 'ps aux | grep -E "php[0-9]\.[0-9] artisan horizon$|php artisan horizon$" | grep -v grep';

    public function resolve(Process $process)
    {
        return Str::of($process->getOutput())->isNotEmpty() ?
            $this->check->succeed('Horizon command is running.') : $this->check->fail('Horizon command is not running.');
    }
}
