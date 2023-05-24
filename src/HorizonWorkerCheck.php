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

        $currentProcesses = Str::of($process->getOutput())->substrCount('horizon:work');
        if ($currentProcesses >= $this->getFromConfig('horizon.min_worker_processes', 1) &&
            $currentProcesses <= $this->getFromConfig('horizon.max_worker_processes', 1)
        ) {
            return $this->check->succeed('Horizon worker(s) is/are running.');
        }

        return $this->check
            ->fail("Horizon worker(s) is/are not running. {$currentProcesses}/".$this->getFromConfig('horizon.min_worker_processes', 1).','.$this->getFromConfig('horizon.max_worker_processes', 1));
    }
}
