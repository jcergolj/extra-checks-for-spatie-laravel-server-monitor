<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\RedisCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;

/** @see \Jcergolj\CustomChecks\RedisCheck */
class RedisCheckTest extends TestCase
{
    /** @var Check */
    public $check;

    /** @var RedisCheck */
    public $redisCheck;

    public function setUp(): void
    {
        parent::setUp();

        $this->createHost('localhost', 65000, ['redis']);

        $this->check = Check::first();

        $this->redisCheck = (new RedisCheck())->setCheck($this->check);
    }

    /** @test */
    public function success()
    {
        $process = $this->getProcessWithOutput('PONG');

        $this->redisCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Redis is running.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function failure()
    {
        $process = $this->getFailedProcess('');

        $this->redisCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame(
            'Redis is not running.',
            $this->check->last_run_message
        );
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }
}
