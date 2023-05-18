<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class HorizonWorkerCheck extends CheckDefinition
{
    public $command = 'ps aux | grep -E "php[0-9]\.[0-9] artisan horizon:work|php artisan horizon:work" | grep -v grep';

    public function resolve(Process $process)
    {
        return Str::of($process->getOutput())->isNotEmpty() ?
            $this->check->succeed('Horizon worker is running.') : $this->check->fail('Horizon worker is not running.');
    }
}
