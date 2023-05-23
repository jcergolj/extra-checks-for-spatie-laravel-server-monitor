<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class HorizonSupervisorCheck extends CheckDefinition
{
    public $command = 'ps aux | grep -E ".*php([0-9]\.[0-9])? .*artisan horizon:supervisor" | grep -v grep';

    public function resolve(Process $process)
    {
        if (Str::of($process->getOutput())->isEmpty()) {
            return $this->check->fail('Horizon supervisor is not running.');
        }

        if (Str::of($process->getOutput())->substrCount('horizon:supervisor') !== config('server-monitor.horizon.supervisor_processes', 1)) {
            return $this->check->fail('Horizon supervisor(s) is/are not running.');
        }

        $this->check->succeed('Horizon supervisor(s) is/are running.');
    }
}
