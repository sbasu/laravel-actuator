# Laravel Actuator

![GitHub stars](https://img.shields.io/github/stars/sbasu/laravel-actuator?style=flat-square)
![Packagist Downloads](https://img.shields.io/packagist/dt/sbasu/laravel-actuator?style=flat-square)
![License](https://img.shields.io/github/license/sbasu/laravel-actuator?style=flat-square)
![PHP Version](https://img.shields.io/packagist/php-v/sbasu/laravel-actuator?style=flat-square)

Spring Boot Actuator-like monitoring and management endpoints for Laravel applications. Expose health checks, metrics, application info, and environment details through a simple HTTP API — perfect for DevOps, container orchestration, and observability platforms.

## Features

- ✅ `/actuator/health` — Aggregated health check with per-component breakdown
- ✅ `/actuator/metrics` — Runtime metrics (memory, database, request timing)
- ✅ `/actuator/info` — Application name, version, and environment info
- ✅ `/actuator/env` — Filtered environment variables (sensitive values masked)
- ✅ Built-in health indicators: database, disk space, cache, queue
- ✅ Extensible: add custom health indicators and metrics in seconds
- ✅ Security-first: sensitive env vars masked, env endpoint disabled by default
- ✅ Laravel 10 & 11 compatible, PHP 8.1+
- ✅ Auto-discovery via Composer package extras

## Installation

```bash
composer require sbasu/laravel-actuator
```

The service provider is auto-discovered. No manual registration needed.

To publish the configuration file:

```bash
php artisan vendor:publish --tag=actuator-config
```
## Laravel & PHP Version Support

- **Laravel:** 10, 11, 12, 13+
- **PHP:** 8.1, 8.2, 8.3+

This package is tested against the latest Laravel versions and receives updates for new releases.

## Endpoints

### Health Check

```
GET /actuator/health
```

**Response (200 OK — all components healthy):**

```json
{
  "status": "UP",
  "components": {
    "database": {
      "status": "UP",
      "timestamp": "2026-06-27T14:30:45Z",
      "details": {
        "database": "myapp",
        "driver": "mysql",
        "host": "127.0.0.1",
        "connection": "connected"
      }
    },
    "disk_space": {
      "status": "UP",
      "timestamp": "2026-06-27T14:30:45Z",
      "details": {
        "free_bytes": 53687091200,
        "total_bytes": 107374182400,
        "percentage_used": 50.0,
        "free": "50 GB",
        "total": "100 GB"
      }
    },
    "cache": {
      "status": "UP",
      "timestamp": "2026-06-27T14:30:45Z",
      "details": {
        "driver": "redis",
        "accessible": true
      }
    },
    "queue": {
      "status": "UP",
      "timestamp": "2026-06-27T14:30:45Z",
      "details": {
        "driver": "redis",
        "connected": true
      }
    }
  },
  "timestamp": "2026-06-27T14:30:45Z"
}
```

Returns **HTTP 503** when any component reports `DOWN`.

### Metrics

```
GET /actuator/metrics
```

```json
{
  "names": [
    "actuator.memory",
    "actuator.request",
    "actuator.database"
  ]
}
```

```
GET /actuator/metrics/memory
```

```json
{
  "name": "memory",
  "measurements": [
    { "statistic": "usage_bytes", "value": 8388608, "human": "8 MB" },
    { "statistic": "peak_bytes",  "value": 10485760, "human": "10 MB" },
    { "statistic": "limit_bytes", "value": 134217728, "human": "128 MB" },
    { "statistic": "usage_percent", "value": 6.25 }
  ],
  "base_unit": "bytes"
}
```

### Application Info

```
GET /actuator/info
```

```json
{
  "app": {
    "name": "My Laravel App",
    "version": "2.1.0",
    "environment": "staging",
    "debug": false
  },
  "actuator": {
    "version": "1.0.0",
    "package": "sbasu/laravel-actuator"
  }
}
```

> The `environment` and `debug` fields are hidden in production.

### Environment Variables

```
GET /actuator/env
```

Disabled by default. Enable in config: `actuator.show_env = true`.

```json
{
  "activeProfiles": ["production"],
  "propertySources": [
    {
      "name": "systemEnvironment",
      "properties": {
        "APP_NAME": "My Laravel App",
        "APP_ENV": "production",
        "DB_PASSWORD": "******",
        "APP_KEY": "******"
      }
    }
  ]
}
```

Sensitive variables containing `PASSWORD`, `KEY`, `SECRET`, `TOKEN`, `PRIVATE`, `CREDENTIAL`, `AUTH`, `PASS`, or `PWD` are automatically masked.

## Health Indicators

| Indicator | Config Key | What It Checks |
|-----------|-----------|----------------|
| `DatabaseHealthIndicator` | `indicators.database` | PDO connection to the default DB |
| `DiskSpaceHealthIndicator` | `indicators.disk_space` | Free disk space (DOWN if >85% used) |
| `CacheHealthIndicator` | `indicators.cache` | Cache read/write test |
| `QueueHealthIndicator` | `indicators.queue` | Queue connection availability |

## Available Metrics

| Metric | Endpoint | Measurements |
|--------|----------|-------------|
| `memory` | `/actuator/metrics/memory` | usage, peak, limit, usage % |
| `request` | `/actuator/metrics/request` | duration ms, start time, current time |
| `database` | `/actuator/metrics/database` | driver, host, database, query count |

## Custom Health Indicator

Implement the `HealthIndicator` contract:

```php
use Sbasu\LaravelActuator\Contracts\HealthIndicator;
use Sbasu\LaravelActuator\HealthStatus;

class RedisHealthIndicator implements HealthIndicator
{
    public function name(): string
    {
        return 'redis';
    }

    public function check(): HealthStatus
    {
        try {
            \Illuminate\Support\Facades\Redis::ping();

            return HealthStatus::up()
                ->withDetail('connected', true);
        } catch (\Throwable $e) {
            return HealthStatus::down()
                ->withDetail('connected', false)
                ->withDetail('error', $e->getMessage());
        }
    }
}
```

Register it in a service provider:

```php
use Sbasu\LaravelActuator\Actuator;

public function boot(): void
{
    $this->app->make(Actuator::class)
        ->registerHealthIndicator(RedisHealthIndicator::class);
}
```

Or via the facade:

```php
use Sbasu\LaravelActuator\Facades\Actuator;

Actuator::registerHealthIndicator(RedisHealthIndicator::class);
```

## Configuration

```php
// config/actuator.php
return [
    'path'       => env('ACTUATOR_PATH', 'actuator'), // URI prefix
    'middleware' => ['api'],                           // Route middleware

    'indicators' => [
        'database'   => true,
        'disk_space' => true,
        'cache'      => true,
        'queue'      => true,
    ],

    'metrics' => [
        'enabled'     => true,
        'sample_rate' => 1.0,
    ],

    'show_details' => true,  // Include component details in /health response
    'show_env'     => false, // Enable /env endpoint (keep false in production)
    'log_access'   => false, // Log each actuator request

    'max_request_history' => 100,
];
```

## Comparison with Spring Boot Actuator

| Feature | Spring Boot Actuator | Laravel Actuator |
|---------|---------------------|-----------------|
| Health endpoint | `/actuator/health` | `/actuator/health` |
| Metrics endpoint | `/actuator/metrics` | `/actuator/metrics` |
| Info endpoint | `/actuator/info` | `/actuator/info` |
| Env endpoint | `/actuator/env` | `/actuator/env` |
| Custom indicators | `HealthIndicator` interface | `HealthIndicator` interface |
| Auto-registration | `@Component` | Service provider |
| Sensitive masking | Yes | Yes |
| HTTP status on DOWN | 503 | 503 |
| Component breakdown | Yes | Yes |

## Security Considerations

- The `/env` endpoint is **disabled by default**. Never enable it in production.
- Sensitive environment variables are always masked, even when the endpoint is enabled.
- Add authentication middleware to protect actuator endpoints in production:

```php
// config/actuator.php
'middleware' => ['api', 'auth:sanctum'],
```

- Or restrict by IP using middleware:

```php
'middleware' => ['api', 'restrict-to-internal-network'],
```

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Write tests for new functionality
4. Ensure all tests pass: `./vendor/bin/phpunit`
5. Submit a pull request

Please follow PSR-12 coding standards.

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.

## Author

**Sbasu** — [github.com/sbasu](https://github.com/sbasu) — shantanubasu123@gmail.com
