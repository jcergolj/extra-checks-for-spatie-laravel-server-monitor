<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class QueueWorkerCheck extends CheckDefinition
{
    public $command = 'ps aux | grep -E "php[0-9]\.[0-9] artisan queue:work|php artisan queue:work" | grep -v grep';

    public function resolve(Process $process)
    {
        return Str::of($process->getOutput())->isNotEmpty() ?
            $this->check->succeed('Queue worker is running.') : $this->check->fail('Queue worker is not running.');
    }
}
