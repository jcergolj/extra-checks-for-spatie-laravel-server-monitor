<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class RedisMemoryCheck extends CheckDefinition
{
    public $command = 'redis-cli info memory';

    protected float $failWhenAbove = 5000000;

    public function resolve(Process $process)
    {
        $currentRedisMemoryUsage = Str::of($process->getOutput())->match('/used_memory:(?<memoryUsage>\d+)/')->toInteger();

        if ($currentRedisMemoryUsage > $this->failWhenAbove) {
            $this->check->fail("Redis memory usage is above {$this->failWhenAbove}. It is {$currentRedisMemoryUsage} bytes.");

            return;
        }

        $this->check->succeed('Redis memory usage is normal.');

    }
}
