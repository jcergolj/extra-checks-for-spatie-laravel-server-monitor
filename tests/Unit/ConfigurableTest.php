<?php

namespace Jcergolj\CustomChecks\Tests\Unit;

use Jcergolj\CustomChecks\CpuLoadCheck;
use Jcergolj\CustomChecks\Tests\TestCase;
use Spatie\ServerMonitor\Models\Check;

/** @see \Jcergolj\CustomChecks\Configurable */
class ConfigurableTest extends TestCase
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
    public function host_config_value()
    {
        $configValue = 0.1;
        config()->set("server-monitor.{$this->check->host['name']}.cpu_load.one_minute_threshold", $configValue);
        config()->set('server-monitor.cpu_load.one_minute_threshold', 0.4);

        $this->assertSame($configValue, $this->cpuLoadCheck->getFromConfig('cpu_load.one_minute_threshold', 0.5));
    }

    /** @test */
    public function general_config_value()
    {
        $configValue = 0.4;
        config()->set('server-monitor.cpu_load.one_minute_threshold', $configValue);

        $this->assertSame($configValue, $this->cpuLoadCheck->getFromConfig('cpu_load.one_minute_threshold', 0.5));
    }

    /** @test */
    public function no_config_value()
    {
        $this->assertSame(0.5, $this->cpuLoadCheck->getFromConfig('cpu_load.one_minute_threshold', 0.5));
    }
}
