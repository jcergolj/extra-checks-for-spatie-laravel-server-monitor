<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\Configurable;
use Jcergolj\CustomChecks\HorizonSupervisorCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;

/** @see \Jcergolj\CustomChecks\HorizonSupervisorCheck */
class HorizonSupervisorCheckTest extends TestCase
{
    /** @var Check */
    public $check;

    /** @var HorizonSupervisorCheck */
    public $horizonSupervisorCheck;

    public function setUp(): void
    {
        parent::setUp();

        $this->createHost('localhost', 65000, ['horizon-supervisor']);

        $this->check = Check::first();

        $this->horizonSupervisorCheck = (new HorizonSupervisorCheck())->setCheck($this->check);
    }

    /** @test */
    public function success()
    {
        $process = $this->getProcessWithOutput('php8.2 artisan horizon:supervisor');

        $this->horizonSupervisorCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Horizon supervisor(s) is/are running.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function success_two_processes()
    {
        config()->set('server-monitor.horizon.supervisor_processes', 2);

        $process = $this->getProcessWithOutput("php artisan horizon:supervisor \n php artisan horizon:supervisor");

        $this->horizonSupervisorCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Horizon supervisor(s) is/are running.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function failure_two_processes()
    {
        config()->set('server-monitor.horizon.supervisor_processes', 3);

        $process = $this->getProcessWithOutput("php artisan horizon:supervisor \n php artisan horizon:supervisor");

        $this->horizonSupervisorCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Horizon supervisor(s) is/are not running. 2/3', $this->check->last_run_message);
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }

    /** @test */
    public function failure()
    {
        $process = $this->getFailedProcess('');

        $this->horizonSupervisorCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame(
            'Horizon supervisor is not running.',
            $this->check->last_run_message
        );
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }

    /** @test */
    public function assert_configurable_trait_is_used()
    {
        $this->assertContains(Configurable::class, class_uses(HorizonSupervisorCheck::class));
    }
}
