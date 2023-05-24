<?php

namespace Jcergolj\CustomChecks;

use Illuminate\Support\Str;

trait Configurable
{
    public function getFromConfig($key, $defaultValue)
    {
        $modifiedHostName = Str::of($this->check->host['name'])->replace('.', '_')->__toString();

        if (config()->has("server-monitor.{$modifiedHostName}.{$key}")) {
            return config("server-monitor.{$modifiedHostName}.{$key}");
        }

        return config("server-monitor.{$key}", $defaultValue);
    }
}
