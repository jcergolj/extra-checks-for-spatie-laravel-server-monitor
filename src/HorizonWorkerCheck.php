<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class HorizonWorkerCheck extends CheckDefinition
{
    use Configurable;

    public $command = 'ps aux | grep -E ".*php([0-9]\.[0-9])? .*artisan horizon:work" | grep -v grep';

    public function resolve(Process $process)
    {
        if (Str::of($process->getOutput())->isEmpty()) {
            return $this->check->fail('Horizon worker is not running.');
        }

        if (Str::of($process->getOutput())->substrCount('horizon:work') !== $this->getFromConfig('horizon.worker_processes', 1)) {
            return $this->check->fail('Horizon worker(s) is/are not running.');
        }

        $this->check->succeed('Horizon worker(s) is/are running.');
    }
}
