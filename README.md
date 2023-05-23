# Extra Checks For Spatie Laravel Server Monitor package
First you need to install [Spatie Server Monitor Package](https://github.com/spatie/laravel-server-monitor).

Then you need to install this package
```bash
composer require jcergolj/extra-checks-for-spatie-laravel-server-monitor
```

Finally add the following checks to `config\server-monitor.php` file
```php
'checks' => [
    'diskspace' => Spatie\ServerMonitor\CheckDefinitions\Diskspace::class,
    'elasticsearch' => Spatie\ServerMonitor\CheckDefinitions\Elasticsearch::class,
    'memcached' => Spatie\ServerMonitor\CheckDefinitions\Memcached::class,
    'mysql' => Spatie\ServerMonitor\CheckDefinitions\MySql::class,
    'cpu-load' => Jcergolj\CustomChecks\CpuLoadCheck::class,
    'redis' => Jcergolj\CustomChecks\RedisCheck::class,
    'redis-memory' => Jcergolj\CustomChecks\RedisMemoryCheck::class,
    'horizon-artisan-command' => Jcergolj\CustomChecks\HorizonArtisanCommandCheck::class,
    'horizon-supervisor' => Jcergolj\CustomChecks\HorizonSupervisorCheck::class,
    'horizon-worker' => Jcergolj\CustomChecks\HorizonWorkerCheck::class,
    'queue-worker' => Jcergolj\CustomChecks\QueueWorkerCheck::class,
    'db-connection-count' => Jcergolj\CustomChecks\DbConnectionCountCheck::class,
],
```

## Custom check overview

### CpuLoadCheck
It checks server loads in last 1, 5 and 15 minutes

It executes this command on the server: `uptime`.

You can specify cpu load threshold in `server-monitor.php` config file. If it isn't provided the
default values are 1.4.

```php
// config/server-monitor.php
'cpu_load' => [
    'one_minute_threshold' => 1.6,
    'five_minute_threshold' => 1.2,
    'fifteen_minute_threshold' => 1.1,
]
```

If loads are below threshold, everything is fine.

### RedisCheck
It checks if Redis is running.

It executes this command on the server: `redis-cli ping`.

If the response of the command is `PONG`, everything is fine.

### RedisMemoryCheck
It checks the current redis memory consumption.

It executes this command on the server: `redis-cli info memory`.

You can specify redis memory threshold in `server-monitor.php` config file. If it isn't provided the
default value is 5MB.

```php
// config/server-monitor.php
'redis' => [
    'memory_threshold' => 6000000,
]
```

If it is bellow threshold, everything is fine.

### HorizonArtisanCommandCheck
It checks if horizon artisan process is running.

It executes this command on the server: `ps aux | grep -E "php[0-9]\.[0-9] artisan horizon$|php artisan horizon$" | grep -v grep`.

If output contains `artisan horizon`, process is running.

### HorizonSupervisorCheck
It checks if horizon supervisor process is running.

It executes this command on the server: `ps aux | grep -E "php[0-9]\.[0-9] artisan horizon:supervisor|php artisan horizon:supervisor" | grep -v grep`.

If output contains `artisan horizon:supervisor`, process is running.

### HorizonWorkerCheck
It checks if horizon worker process is running.

It executes this command on the server: `ps aux | grep -E "php[0-9]\.[0-9] artisan horizon:work|php artisan horizon:work" | grep -v grep`.

If output contains `artisan horizon:work`, process is running.

### QueueWorkerCheck
It checks if queue worker process is running.

It executes this command on the server: `ps aux | grep -E "php[0-9]\.[0-9] artisan queue:work|php artisan queue:work" | grep -v grep`.

If output contains `artisan queue:work`, process is running.

### DbConnectionCountCheck
It checks if the number of mysql connections.

It executes this command on the server: `netstat -an | grep 3306 | grep ESTABLISHED`.

You can specify the db connection threshold in `server-monitor.php` config file. If it isn't provided the
default value is 40.

```php
// config/server-monitor.php
'mysql' => [
    'connections' => 50,
]
```

It counts the number of mysql db connections. If it is bellow threshold, everything is fine.
