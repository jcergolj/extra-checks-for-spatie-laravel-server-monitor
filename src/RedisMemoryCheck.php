<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class RedisMemoryCheck extends CheckDefinition
{
    public $command = 'redis-cli info memory';

    public function resolve(Process $process)
    {
        $failWhenAbove = config('server-monitor.redis.memory_threshold', 5000000);

        $currentRedisMemoryUsage = Str::of($process->getOutput())->match('/used_memory:(?<memoryUsage>\d+)/')->toInteger();

        if ($currentRedisMemoryUsage > $failWhenAbove) {
            $this->check->fail("Redis memory usage is above {$failWhenAbove}. It is {$currentRedisMemoryUsage} bytes.");

            return;
        }

        $this->check->succeed('Redis memory usage is normal.');

    }
}
