<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class QueueWorkerCheck extends CheckDefinition
{
    use Configurable;

    public $command = 'ps aux | grep -E ".*php([0-9]\.[0-9])? .*artisan queue:work" | grep -v grep';

    public function resolve(Process $process)
    {
        if (Str::of($process->getOutput())->isEmpty()) {
            return $this->check->fail('Queue worker is not running.');
        }

        $currentProcesses = Str::of($process->getOutput())->substrCount('queue:work');
        if ($currentProcesses !== $this->getFromConfig('queue.worker_processes', 1)) {
            return $this->check
                ->fail("Queue worker(s) is/are not running. {$currentProcesses}/".$this->getFromConfig('queue.worker_processes', 1));
        }

        $this->check->succeed('Queue worker(s) is/are running.');
    }
}
