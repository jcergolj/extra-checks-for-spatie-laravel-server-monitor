<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\Configurable;
use Jcergolj\CustomChecks\DbConnectionCountCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;

/** @see \Jcergolj\CustomChecks\DbConnectionCountCheck */
class DbConnectionCountCheckTest extends TestCase
{
    /** @var Check */
    public $check;

    /** @var DbConnectionCountCheck */
    public $dbConnectionCountCheck;

    public function setUp(): void
    {
        parent::setUp();

        $this->createHost('localhost', 65000, ['db-connection-count']);

        $this->check = Check::first();

        $this->dbConnectionCountCheck = (new DbConnectionCountCheck())->setCheck($this->check);
    }

    /** @test */
    public function success()
    {
        $process = $this->getProcessWithOutput("conn 1\n conn 2\n conn 3");

        $this->dbConnectionCountCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('There are 3 database connections.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function failure()
    {
        $process = $this->getProcessWithOutput(str_repeat(" \n", 40));

        $this->dbConnectionCountCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame(
            'There are too many database connections. Max. allowed is 40 but there is 41 connections.',
            $this->check->last_run_message
        );
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }

    /** @test */
    public function assert_configurable_trait_is_used()
    {
        $this->assertContains(Configurable::class, class_uses(DbConnectionCountCheck::class));
    }
}
