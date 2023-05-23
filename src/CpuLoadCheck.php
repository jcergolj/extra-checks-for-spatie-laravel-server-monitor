<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class CpuLoadCheck extends CheckDefinition
{
    use Configurable;

    public $command = 'uptime';

    public function resolve(Process $process)
    {
        [$oneMinLoad, $fiveMinLoad, $fifteenMinLoad] = Str::of($process->getOutput())
            ->after('load average:')
            ->matchAll('/([\d.]+)/')
            ->toArray();

        if ($oneMinLoad > $this->getFromConfig('cpu_load.one_minute_threshold', 1.3)) {
            return $this->check->fail("One minute load is high. It is {$oneMinLoad}.");
        }

        if ($fiveMinLoad > $this->getFromConfig('cpu_load.five_minute_threshold', 1.3)) {
            return $this->check->fail("Five minute load is high. It is {$fiveMinLoad}.");
        }

        if ($fifteenMinLoad > $this->getFromConfig('cpu_load.fifteen_minute_threshold', 1.3)) {
            return $this->check->fail("Fifteen minute load is high. It is {$fifteenMinLoad}.");
        }

        $this->check->succeed("CPU loads are {$oneMinLoad} for one minute, {$fiveMinLoad} for five and {$fifteenMinLoad} for fifteen.");
    }
}
