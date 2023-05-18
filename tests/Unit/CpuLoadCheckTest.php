<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\CpuLoadCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;

/** @see \Jcergolj\CustomChecks\CpuLoadCheck */
class CpuLoadCheckTest extends TestCase
{
    /** @var Check */
    public $check;

    /** @var CpuLoadCheck */
    public $cpuLoadCheck;

    public function setUp(): void
    {
        parent::setUp();

        $this->createHost('localhost', 65000, ['cpu']);

        $this->check = Check::first();

        $this->cpuLoadCheck = (new CpuLoadCheck())->setCheck($this->check);
    }

    /** @test */
    public function success()
    {
        $process = $this->getProcessWithOutput('load average: 0.5 0.5 0.5');

        $this->cpuLoadCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('CPU loads are 0.5 for one minute, 0.5 for five and 0.5 for fifteen.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function one_min_cpu_load_failure()
    {
        $process = $this->getProcessWithOutput('load average: 1.6 0.5 0.5');

        $this->cpuLoadCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('One minute load is high. It is 1.6.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }

    /** @test */
    public function five_min_cpu_load_failure()
    {
        $process = $this->getProcessWithOutput('load average: 0.5 1.6 0.5');

        $this->cpuLoadCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Five minute load is high. It is 1.6.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }

    /** @test */
    public function fifteen_min_cpu_load_failure()
    {
        $process = $this->getProcessWithOutput('load average: 0.5 0.5 1.6');

        $this->cpuLoadCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Fifteen minute load is high. It is 1.6.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }
}
