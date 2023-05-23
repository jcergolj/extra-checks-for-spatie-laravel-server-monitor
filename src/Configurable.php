<?php

namespace Jcergolj\CustomChecks;

trait Configurable
{
    public function getFromConfig($key, $defaultValue)
    {
        if (config()->has("server-monitor.{$this->check->host['name']}.{$key}")) {
            return config("server-monitor.{$this->check->host['name']}.{$key}");
        }

        return config("server-monitor.{$key}", $defaultValue);
    }
}
