# Laravel Actuator

Spring Boot Actuator-like monitoring endpoints for Laravel applications.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sbasu/laravel-actuator.svg?style=flat-square)](https://packagist.org/packages/sbasu/laravel-actuator)
[![License](https://img.shields.io/packagist/l/sbasu/laravel-actuator.svg?style=flat-square)](https://github.com/sbasu/laravel-actuator/blob/main/LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg?style=flat-square)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-%5E10.0-brightgreen.svg?style=flat-square)](https://laravel.com)

## Overview

Laravel Actuator provides production-ready HTTP endpoints to monitor and manage your Laravel application. Inspired by [Spring Boot Actuator](https://spring.io/guides/gs/actuator-service/), it brings enterprise-grade monitoring to Laravel.

Get instant visibility into your app's health, performance metrics, and configuration.

## Why Laravel Actuator?

- **Production Ready** — Know your app's health in real-time
- **Kubernetes Compatible** — Built for container orchestration (liveness/readiness probes)
- **Zero Configuration** — Works out of the box with sensible defaults
- **Comprehensive Health Checks** — Database, cache, queue, disk space
- **Live Metrics** — Memory usage, request timing, database statistics
- **DevOps Friendly** — Standard endpoints for monitoring systems and dashboards
- **Spring Boot Compatible** — Familiar if you know Spring Boot Actuator

## Requirements

- **PHP:** 8.1 or higher
- **Laravel:** 10, 11, 12, or 13+
- **Composer:** For package installation

## Installation

### Step 1: Install via Composer

```bash
composer require sbasu/laravel-actuator
```

### Step 2: Publish Configuration (Required)

```bash
php artisan vendor:publish --tag=actuator-config
```

This creates `config/actuator.php` in your application where you can customize behavior.

### Step 3: Verify Installation

Start your Laravel server:

```bash
php artisan serve
```

Test the health endpoint:

```bash
curl http://localhost:8000/actuator/health
```

You should see a JSON response with status `"UP"`.

## Usage

Once installed and configured, your Laravel app automatically has monitoring endpoints available.

### 1. Health Check Endpoint

Check if your application and all dependencies are healthy.

**Request:**
```bash
curl http://localhost:8000/actuator/health
```

**Response:**
```json
{
  "status": "UP",
  "components": {
    "database": {
      "status": "UP",
      "timestamp": "2026-06-27T10:34:14Z",
      "details": {
        "database": "mysql",
        "driver": "sqlite",
        "host": "localhost",
        "connection": "connected"
      }
    },
    "disk_space": {
      "status": "UP",
      "timestamp": "2026-06-27T10:34:14Z",
      "details": {
        "free_bytes": 84775305216,
        "total_bytes": 881433005216,
        "used_bytes": 2659151872,
        "percentage_used": 3.04,
        "free": "78.95 GB",
        "total": "881.43 GB",
        "path": "/"
      }
    },
    "cache": {
      "status": "UP",
      "timestamp": "2026-06-27T10:34:14Z",
      "details": {
        "driver": "database",
        "accessible": true
      }
    },
    "queue": {
      "status": "UP",
      "timestamp": "2026-06-27T10:34:14Z",
      "details": {
        "driver": "database",
        "connected": true
      }
    }
  },
  "timestamp": "2026-06-27T10:34:14Z"
}
```

**Use Cases:**
- Kubernetes liveness probes (restart unhealthy pods)
- Kubernetes readiness probes (route traffic only to ready pods)
- Load balancer health checks
- Monitoring dashboards
- CI/CD deployment verification

---

### 2. Metrics Endpoint

List available performance metrics and monitoring data.

**Request:**
```bash
curl http://localhost:8000/actuator/metrics
```

**Response:**
```json
{
  "names": [
    "actuator.memory",
    "actuator.request",
    "actuator.database"
  ]
}
```

**Use Cases:**
- Integration with Prometheus/Grafana
- Performance monitoring
- Identifying bottlenecks
- Tracking resource usage

---

### 3. Application Info Endpoint

Get metadata about your application.

**Request:**
```bash
curl http://localhost:8000/actuator/info
```

**Response:**
```json
{
  "app": {
    "name": "Laravel",
    "version": "1.0.0",
    "description": "",
    "environment": "local",
    "debug": true
  },
  "actuator": {
    "version": "1.0.0",
    "package": "sbasu/laravel-actuator"
  }
}
```

**Use Cases:**
- Verify deployment version
- Check environment configuration
- CI/CD pipeline information
- Application identification

---

### 4. Environment Variables Endpoint

View environment configuration. **Disabled by default for security.**

#### Enabling the Endpoint

Follow these steps to enable (development environments only):

**Step 1:** Open your `.env` file

```bash
# macOS/Linux
nano .env

# Windows
notepad .env
```

**Step 2:** Add this line

ACTUATOR_SHOW_ENV=true

**Step 3:** Restart your server

```bash
php artisan serve
```

**Step 4:** Test the endpoint

```bash
curl http://localhost:8000/actuator/env
```

#### Example Response

```json
{
  "APP_NAME": "Laravel",
  "APP_ENV": "local",
  "APP_DEBUG": "true",
  "APP_URL": "http://localhost:8000",
  "DB_CONNECTION": "sqlite",
  "DB_DATABASE": "/full/path/to/database.sqlite",
  ...
}
```

#### ⚠️ Security Warning

**NEVER enable this endpoint in production!**

Environment variables may contain:
- Database passwords
- API keys
- Encryption keys
- OAuth tokens
- Third-party service credentials

**Only enable in development environments.**

---

## Configuration

Edit `config/actuator.php` to customize the package behavior:

```php
return [
    // URI prefix for all endpoints
    'path' => env('ACTUATOR_PATH', 'actuator'),

    // Middleware applied to actuator endpoints
    'middleware' => ['api'],

    // Enable/disable individual health indicators
    'indicators' => [
        'database' => true,
        'disk_space' => true,
        'cache' => true,
        'queue' => true,
    ],

    // Metrics collection settings
    'metrics' => [
        'enabled' => true,
        'sample_rate' => 1.0,
    ],

    // Show detailed health information in responses
    'show_details' => true,

    // Show environment variables (disabled by default for security)
    'show_env' => env('ACTUATOR_SHOW_ENV', false),

    // Log all actuator requests
    'log_access' => false,
];
```

### Configuration Options

| Option | Default | Purpose |
|--------|---------|---------|
| `path` | `actuator` | URI prefix for endpoints |
| `middleware` | `['api']` | Middleware to apply |
| `indicators.database` | `true` | Enable database health check |
| `indicators.disk_space` | `true` | Enable disk space check |
| `indicators.cache` | `true` | Enable cache health check |
| `indicators.queue` | `true` | Enable queue health check |
| `show_details` | `true` | Show detailed health info |
| `show_env` | `false` | Enable environment endpoint |
| `log_access` | `false` | Log endpoint requests |

---

## Real-World Examples

### Kubernetes Deployment

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: laravel-app
spec:
  template:
    spec:
      containers:
      - name: app
        image: laravel-app:latest
        ports:
        - containerPort: 8000
        
        # Check if pod is alive
        livenessProbe:
          httpGet:
            path: /actuator/health
            port: 8000
          initialDelaySeconds: 30
          periodSeconds: 10
          timeoutSeconds: 5
          failureThreshold: 3
        
        # Check if pod is ready for traffic
        readinessProbe:
          httpGet:
            path: /actuator/health
            port: 8000
          initialDelaySeconds: 10
          periodSeconds: 5
          timeoutSeconds: 3
          failureThreshold: 3
```

### Docker Health Check

```dockerfile
FROM php:8.3-cli

COPY . /app
WORKDIR /app

RUN composer install

# Health check every 30s
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
  CMD curl -f http://localhost:8000/actuator/health || exit 1

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
```

### Prometheus Monitoring

```yaml
# prometheus.yml
scrape_configs:
  - job_name: 'laravel-app'
    metrics_path: '/actuator/metrics'
    static_configs:
      - targets: ['localhost:8000']
    scrape_interval: 30s
```

### Load Balancer Health Check (Nginx)

```nginx
upstream laravel {
    server laravel-app-1:8000;
    server laravel-app-2:8000;
    server laravel-app-3:8000;
    
    # Check health regularly
    check interval=3000 rise=2 fall=5 timeout=1000 type=http;
    check_http_send "GET /actuator/health HTTP/1.0\r\n\r\n";
    check_http_expect_alive http_2xx;
}

server {
    listen 80;
    server_name api.example.com;
    
    location / {
        proxy_pass http://laravel;
    }
}
```

---

## Endpoints Reference

| Endpoint | Method | Purpose | Returns | Status |
|----------|--------|---------|---------|--------|
| `/actuator/health` | GET | Application health | JSON with status | 200/503 |
| `/actuator/metrics` | GET | Available metrics | JSON array | 200 |
| `/actuator/info` | GET | App information | JSON metadata | 200 |
| `/actuator/env` | GET | Environment variables | JSON config | 200/403 |

---

## Security Best Practices

### Health Endpoints

- ✅ Public by default (no authentication required)
- ✅ No sensitive data exposed
- ✅ Safe to expose to monitoring systems
- ✅ Include in Kubernetes probes

### Environment Endpoint

- ⚠️ **Disabled by default** for security
- ⚠️ Only enable in development environments
- ⚠️ Never enable in production
- ⚠️ Can expose sensitive configuration

### Protecting Endpoints

If you want authentication on actuator endpoints, edit `config/actuator.php`:

**Option 1: Require API Authentication**
```php
'middleware' => ['api', 'auth:api'],
```

**Option 2: Rate Limiting**
```php
'middleware' => ['api', 'throttle:60,1'],
```

**Option 3: Custom Middleware**
```php
'middleware' => ['api', App\Http\Middleware\ActuatorAuth::class],
```

---

## Troubleshooting

### Endpoints Return 404

**Problem:** Getting "Route not found" when accessing endpoints

**Solution:** You must publish the configuration first

```bash
php artisan vendor:publish --tag=actuator-config
```

Then restart your server.

---

### Config File Not Found

**Problem:** `config/actuator.php` doesn't exist

**Solution:** Run the publish command

```bash
php artisan vendor:publish --tag=actuator-config
```

---

### Database Shows DOWN

**Problem:** Health check returns database status as DOWN

**Solution:** Check your database connection

```bash
# Check .env file
cat .env | grep DB_

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

### Cannot Enable /env Endpoint

**Problem:** Still get 403 when `ACTUATOR_SHOW_ENV=true`

**Solution:** Restart your server after editing `.env`

```bash
# Stop and restart
php artisan serve
```


```
## Reporting Issues

Found a bug or have a suggestion?

- **🐛 Report Bugs:** [GitHub Issues](https://github.com/sbasu/laravel-actuator/issues/new?template=bug_report.md)
- **✨ Request Features:** [GitHub Issues](https://github.com/sbasu/laravel-actuator/issues/new?template=feature_request.md)
- **💬 Ask Questions:** [GitHub Discussions](https://github.com/sbasu/laravel-actuator/discussions)

Please include details like:
- Laravel and PHP version
- Steps to reproduce
- Expected vs actual behavior
- Error messages or logs

The more information you provide, the faster we can help!
```


---

## Roadmap

This is the first in a series of Spring Boot → Laravel packages bringing enterprise patterns to Laravel:

- ✅ **Laravel Actuator** v1.0 — Health checks & monitoring
- 🔄 **Laravel Profiles** — Environment-specific configuration
- 🔄 **Laravel Repository** — Data access abstraction layer
- 🔄 **Laravel Events** — Advanced event bus
- 🔄 **Laravel Scheduler** — Improved task scheduling

Follow [@sbasu](https://github.com/sbasu) on GitHub for updates.

---

## Support

- **Issues:** [github.com/sbasu/laravel-actuator/issues](https://github.com/sbasu/laravel-actuator/issues)
- **Discussions:** [github.com/sbasu/laravel-actuator/discussions](https://github.com/sbasu/laravel-actuator/discussions)
- **GitHub:** [github.com/sbasu/laravel-actuator](https://github.com/sbasu/laravel-actuator)

---

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) file for more information.

---

## About

Built by [Shantanu Basu](https://github.com/sbasu).

Inspired by [Spring Boot Actuator](https://spring.io/guides/gs/actuator-service/).

Made with ❤️ for the Laravel community.