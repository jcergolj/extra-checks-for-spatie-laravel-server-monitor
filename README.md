## Extra Checks For Spatie Laravel Server Monitor package
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

### Custom check overview

#### CpuLoadCheck
It checks server loads in last 1, 5 and 15 minutes

It executes this command on the server: `uptime`.

You can specify cpu load threshold in `server-monitor.php` config file. If it isn't provided the
default values are 1.3.

```php
// config/server-monitor.php
'cpu_load' => [
    'one_minute_threshold' => 1.6,
    'five_minute_threshold' => 1.2,
    'fifteen_minute_threshold' => 1.1,
]
```

#### RedisCheck
It checks if Redis is running.

It executes this command on the server: `redis-cli ping`.

#### RedisMemoryCheck
It checks the current redis memory consumption.

It executes this command on the server: `redis-cli info memory`.

You can specify redis memory threshold in `server-monitor.php` config file. Default value is 5MB.

```php
// config/server-monitor.php
'redis' => [
    'memory_threshold' => 6000000,
]
```

#### HorizonArtisanCommandCheck
It checks if horizon artisan process is running.

It executes this command on the server: `ps aux | grep -E ".*php([0-9]\.[0-9])? .*artisan horizon$" | grep -v grep`.

You can specify the number of horizon processes that should run on the server in `server-monitor.php` config file. The default value is 1.

```php
// config/server-monitor.php
'horizon' => [
    'artisan_command_processes' => 2,
]
```

#### HorizonSupervisorCheck
It checks if horizon supervisor process is running.

It executes this command on the server: `ps aux | grep -E ".*php([0-9]\.[0-9])? .*artisan horizon:supervisor" | grep -v grep`.

You can specify the number of horizon supervisor processes that should run on the server in `server-monitor.php` config file. Default value is 1.

```php
// config/server-monitor.php
'horizon' => [
    'supervisor_processes' => 2,
]
```

#### HorizonWorkerCheck
It checks if horizon worker process is running.

It executes this command on the server: `ps aux | grep -E ".*php([0-9]\.[0-9])? .*artisan horizon:work" | grep -v grep`.

You can specify the number of min and max horizon worker processes that should run on the server in `server-monitor.php` config file. The default value is 1 for both min and max.

```php
// config/server-monitor.php
'horizon' => [
    'min_worker_processes' => 2,
    'max_worker_processes' => 2,
]
```

#### QueueWorkerCheck
It checks if queue worker process is running.

It executes this command on the server: `ps aux | grep -E ".*php([0-9]\.[0-9])? .*artisan queue:work" | grep -v grep`.

You can specify the number of queue worker processes that should run on the server in `server-monitor.php` config file. The default value is 1.

```php
// config/server-monitor.php
'queue' => [
    'worker_processes' => 2,
]
```

#### DbConnectionCountCheck
It checks if the number of mysql connections.

It executes this command on the server: `netstat -an | grep 3306 | grep ESTABLISHED`.

You can specify the db connection threshold in `server-monitor.php` config file. The default value is 40.

```php
// config/server-monitor.php
'mysql' => [
    'connections' => 50,
]
```

#### Extended server-monitor.php config file
```php
<?php

return [

    /*
     * These are the checks that can be performed on your servers. You can add your own
     * checks. The only requirement is that they should extend the
     * `Spatie\ServerMonitor\Checks\CheckDefinitions\CheckDefinition` class.
     */
    'checks' => [
        'diskspace' => Spatie\ServerMonitor\CheckDefinitions\Diskspace::class,
        'elasticsearch' => Spatie\ServerMonitor\CheckDefinitions\Elasticsearch::class,
        'memcached' => Spatie\ServerMonitor\CheckDefinitions\Memcached::class,
        'mysql' => Spatie\ServerMonitor\CheckDefinitions\MySql::class,
    ],

    /*
     * The default value for how often the checks will run,
     * after the last successful one.
     */
    'next_run_in_minutes' => env('SERVER_MONITOR_NEXT_RUN_IN_MINUTES', 10),

    /*
     * The performance of the package can be increased by allowing a high number
     * of concurrent ssh connections. Set this to a lower value if you're
     * getting weird errors running the check.
     */
    'concurrent_ssh_connections' => 5,

    /*
     * This string will be prepended to the ssh command generated by the package.
     */
    'ssh_command_prefix' => '',

    /*
     * This string will be appended to the ssh command generated by the package.
     */
    'ssh_command_suffix' => '',

    'notifications' => [

        'notifications' => [
            Spatie\ServerMonitor\Notifications\Notifications\CheckSucceeded::class => [],
            Spatie\ServerMonitor\Notifications\Notifications\CheckRestored::class => ['slack'],
            Spatie\ServerMonitor\Notifications\Notifications\CheckWarning::class => ['slack'],
            Spatie\ServerMonitor\Notifications\Notifications\CheckFailed::class => ['slack'],
        ],

        /*
         * To avoid burying you in notifications, we'll only send one every given amount
         * of minutes when a check keeps emitting warning or keeps failing.
         */
        'throttle_failing_notifications_for_minutes' => 60,

        // Separate the email by , to add many recipients
        'mail' => [
            'to' => 'your@email.com',
        ],

        'slack' => [
            'webhook_url' => env('SERVER_MONITOR_SLACK_WEBHOOK_URL'),
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent. The default
         * notifiable will use the variables specified in this config file.
         */
        'notifiable' => \Spatie\ServerMonitor\Notifications\Notifiable::class,

        /*
         * The date format used in notifications.
         */
        'date_format' => 'd/m/Y',
    ],

    /*
     * To add or modify behaviour to the `Host` model you can specify your
     * own model here. The only requirement is that they should
     * extend the `Host` model provided by this package.
     */
    'host_model' => Spatie\ServerMonitor\Models\Host::class,

    /*
     * To add or modify behaviour to the `Check` model you can specify your
     * own model here. The only requirement is that they should
     * extend the `Check` model provided by this package.
     */
    'check_model' => Spatie\ServerMonitor\Models\Check::class,

    /*
     * Right before running a check it's process will be given to this class. Here you
     * can perform some last minute manipulations on it before it will
     * actually be run.
     *
     * This class should implement Spatie\ServerMonitor\Manipulators\Manipulator
     */
    'process_manipulator' => Spatie\ServerMonitor\Manipulators\Passthrough::class,

    /*
     * Thresholds for disk space's alert.
     */
    'diskspace_percentage_threshold' => [
        'warning' => 80,
        'fail' => 90,
    ],

    'cpu_load' => [
        'one_minute_threshold' => 1.4,
        'five_minute_threshold' => 1.4,
        'fifteen_minute_threshold' => 1.4,
    ],

    'redis' => [
        'memory_threshold' => 5000000,
    ],

    'horizon' => [
        'artisan_command_processes' => 1,
        'supervisor_processes' => 1,
        'min_worker_processes' => 1,
        'max_worker_processes' => 1,
    ],

    'queue' => [
        'worker_processes' => 1,
    ],

    'mysql' => [
        'connections' => 40,
    ],

    // host specific config values
    // you can override all or only some values on a host to host basis
    // replace dots in host name string with underscores
    // host.name.com => host_name_com
    'host_name_one_com' => [
        'cpu_load' => [
            'one_minute_threshold' => 0.9,
        ],
    ],

    'host_name_two_com' => [
        'cpu_load' => [
            'one_minute_threshold' => 1.2,
            'five_minute_threshold' => 1.2,
            'fifteen_minute_threshold' => 1.2,
        ],

        'redis' => [
            'memory_threshold' => 40000,
        ],

        'horizon' => [
            'artisan_command_processes' => 2,
            'supervisor_processes' => 2,
            'min_worker_processes' => 1,
            'max_worker_processes' => 2,
        ],

        'queue' => [
            'worker_processes' => 2,
        ],

        'mysql' => [
            'connections' => 10,
        ]
    ]
];
```

#### Extra custom options each host with its own values
How are custom values obtained? First we check if host specific value exists, then the general one in the config lastly if there is none the default value is used.
