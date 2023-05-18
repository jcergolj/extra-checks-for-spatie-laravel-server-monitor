<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class DbConnectionCountCheck extends CheckDefinition
{
    public $command = 'netstat -an | grep 3306 | grep ESTABLISHED';

    protected int $errorThreshold = 40;

    public function resolve(Process $process)
    {
        // -1 ignore last line
        $currentConnections = Str::of($process->getOutput())->split('/\n/')->count() - 1;

        if ($currentConnections > $this->errorThreshold) {
            return $this->check->fail("There are too many database connections. Max. allowed is {$this->errorThreshold} but there is {$currentConnections} connections.");
        }

        return $this->check->succeed("There are {$currentConnections} database connections.");
    }
}
