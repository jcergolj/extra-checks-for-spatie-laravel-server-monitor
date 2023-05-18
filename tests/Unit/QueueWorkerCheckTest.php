<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\QueueWorkerCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;

/** @see \Jcergolj\CustomChecks\QueueWorkerCheck */
class QueueWorkerCheckTest extends TestCase
{
    /** @var Check */
    public $check;

    /** @var QueueWorkerCheck */
    public $queueWorkerCheck;

    public function setUp(): void
    {
        parent::setUp();

        $this->createHost('localhost', 65000, ['queue-worker']);

        $this->check = Check::first();

        $this->queueWorkerCheck = (new QueueWorkerCheck())->setCheck($this->check);
    }

    /** @test */
    public function success()
    {
        $process = $this->getProcessWithOutput('php8.2 artisan queue:worker');

        $this->queueWorkerCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Queue worker is running.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function failure()
    {
        $process = $this->getFailedProcess('');

        $this->queueWorkerCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame(
            'Queue worker is not running.',
            $this->check->last_run_message
        );
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }
}
