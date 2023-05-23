<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\Configurable;
use Jcergolj\CustomChecks\RedisMemoryCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;

/** @see \Jcergolj\CustomChecks\RedisMemoryCheck */
class RedisMemoryCheckTest extends TestCase
{
    /** @var Check */
    public $check;

    /** @var RedisMemoryCheck */
    public $redisMemoryCheck;

    public function setUp(): void
    {
        parent::setUp();

        $this->createHost('localhost', 65000, ['redis-memory']);

        $this->check = Check::first();

        $this->redisMemoryCheck = (new RedisMemoryCheck())->setCheck($this->check);
    }

    /** @test */
    public function success()
    {
        $process = $this->getProcessWithOutput('used_memory:1000');

        $this->redisMemoryCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Redis memory usage is normal.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function failure()
    {
        $process = $this->getProcessWithOutput('used_memory:5000001');

        $this->redisMemoryCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame(
            'Redis memory usage is above 5000000. It is 5000001 bytes.',
            $this->check->last_run_message
        );
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }

    /** @test */
    public function assert_configurable_trait_is_used()
    {
        $this->assertContains(Configurable::class, class_uses(RedisMemoryCheck::class));
    }
}
