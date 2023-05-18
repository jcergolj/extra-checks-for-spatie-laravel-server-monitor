<?php

namespace Jcergolj\CustomChecks;

use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class RedisCheck extends CheckDefinition
{
    public $command = 'redis-cli ping';

    public function resolve(Process $process)
    {
        if (trim($process->getOutput()) === 'PONG') {
            $this->check->succeed('Redis is running.');

            return;
        }

        $this->check->fail('Redis is not running.');

    }
}
