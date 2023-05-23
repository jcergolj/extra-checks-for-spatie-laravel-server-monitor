<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\HorizonWorkerCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;

/** @see \Jcergolj\CustomChecks\HorizonWorkerCheck */
class HorizonWorkerCheckTest extends TestCase
{
    /** @var Check */
    public $check;

    /** @var HorizonWorkerCheck */
    public $horizonWorkerCheck;

    public function setUp(): void
    {
        parent::setUp();

        $this->createHost('localhost', 65000, ['horizon-worker']);

        $this->check = Check::first();

        $this->horizonWorkerCheck = (new HorizonWorkerCheck())->setCheck($this->check);
    }

    /** @test */
    public function success()
    {
        $process = $this->getProcessWithOutput('php8.2 artisan horizon:worker');

        $this->horizonWorkerCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Horizon worker(s) is/are running.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function success_two_processes()
    {
        config()->set('server-monitor.horizon.worker_processes', 2);

        $process = $this->getProcessWithOutput("/usr/bin/php7.3 artisan horizon:work redis --delay=0 \n
        /usr/bin/php7.2 artisan horizon:work redis --delay=0");

        $this->horizonWorkerCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Horizon worker(s) is/are running.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function failure()
    {
        $process = $this->getFailedProcess('');

        $this->horizonWorkerCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame(
            'Horizon worker is not running.',
            $this->check->last_run_message
        );
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }
}
