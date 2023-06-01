<?php

namespace Jcergolj\CustomChecks\Tests;

use CreateChecksTable;
use CreateHostsTable;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;
use Spatie\ServerMonitor\Models\Host;
use Symfony\Component\Process\Process;

class TestCase extends OrchestraTestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('mail.driver', 'log');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'prefix' => '',
            'database' => ':memory:',
        ]);

        $this->setUpDatabase();
    }

    protected function setUpDatabase()
    {
        include_once __DIR__.'/../vendor/spatie/laravel-server-monitor/database/migrations/create_hosts_table.php.stub';
        (new CreateHostsTable())->up();

        include_once __DIR__.'/../vendor/spatie/laravel-server-monitor/database/migrations/create_checks_table.php.stub';
        (new CreateChecksTable())->up();
    }

    protected function createHost(string $hostName = 'localhost', ?int $port = 65000, $checks = null): Host
    {
        if (is_null($checks)) {
            $checks = ['diskspace'];
        }

        $host = Host::create([
            'name' => $hostName,
            'port' => $port,
        ]);

        $host->checks()->saveMany(collect($checks)->map(function (string $checkName) {
            return new Check([
                'type' => $checkName,
                'status' => CheckStatus::NOT_YET_CHECKED,
            ]);
        }));

        return $host;
    }

    protected function getProcessWithOutput(string $output = 'my output'): Process
    {
        $process = Process::fromShellCommandline('echo "'.$output.'"');

        $process->start();

        while ($process->isRunning()) {
        }

        return $process;
    }

    protected function getFailedProcess(): Process
    {
        $process = Process::fromShellCommandline('blabla');

        $process->start();

        while ($process->isRunning()) {
        }

        return $process;
    }
}
