<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\HorizonArtisanCommandCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;

/** @see \Jcergolj\CustomChecks\HorizonArtisanCommandCheck */
class HorizonArtisanCommandCheckTest extends TestCase
{
    /** @var Check */
    public $check;

    /** @var HorizonArtisanCommandCheck */
    public $horizonArtisanCommandCheck;

    public function setUp(): void
    {
        parent::setUp();

        $this->createHost('localhost', 65000, ['horizon-artisan-command']);

        $this->check = Check::first();

        $this->horizonArtisanCommandCheck = (new HorizonArtisanCommandCheck())->setCheck($this->check);
    }

    /** @test */
    public function success()
    {
        $process = $this->getProcessWithOutput('php8.2 artisan horizon');

        $this->horizonArtisanCommandCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame('Horizon command is running.', $this->check->last_run_message);
        $this->assertSame(CheckStatus::SUCCESS, $this->check->status);
    }

    /** @test */
    public function failure()
    {
        $process = $this->getFailedProcess('');

        $this->horizonArtisanCommandCheck->resolve($process);

        $this->check->fresh();

        $this->assertSame(
            'Horizon command is not running.',
            $this->check->last_run_message
        );
        $this->assertSame(CheckStatus::FAILED, $this->check->status);
    }
}
