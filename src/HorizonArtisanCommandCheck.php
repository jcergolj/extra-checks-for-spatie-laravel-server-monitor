<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;
use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class HorizonArtisanCommandCheck extends CheckDefinition
{
    use Configurable;

    public $command = 'ps aux | grep -E ".*php([0-9]\.[0-9])? .*artisan horizon$" | grep -v grep';

    public function resolve(Process $process)
    {
        if (Str::of($process->getOutput())->isEmpty()) {
            return $this->check->fail('Horizon command is not running.');
        }

        $currentProcesses = Str::of($process->getOutput())->substrCount('artisan horizon');

        if ($currentProcesses !== $this->getFromConfig('horizon.artisan_command_processes', 1)) {
            return $this->check
                ->fail("Horizon command(s) is/are not running. {$currentProcesses}/".$this->getFromConfig('horizon.artisan_command_processes', 1));
        }

        $this->check->succeed('Horizon command(s) is/are running.');
    }
}
