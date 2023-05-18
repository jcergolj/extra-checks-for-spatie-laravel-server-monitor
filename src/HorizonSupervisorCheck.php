<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class HorizonSupervisorCheck extends CheckDefinition
{
    public $command = 'ps aux | grep -E "php[0-9]\.[0-9] artisan horizon:supervisor|php artisan horizon:supervisor" | grep -v grep';

    public function resolve(Process $process)
    {
        return Str::of($process->getOutput())->isNotEmpty() ?
            $this->check->succeed('Horizon supervisor is running.') : $this->check->fail('Horizon supervisor is not running.');
    }
}
