# Changelog

All notable changes to `sbasu/laravel-actuator` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-06-27

### Added
- Initial release
- `/actuator/health` endpoint with component breakdown
- `/actuator/metrics` endpoint listing all registered metrics
- `/actuator/metrics/{metric}` endpoint for individual metric details
- `/actuator/info` endpoint with application information
- `/actuator/env` endpoint with sensitive variable filtering
- Built-in health indicators: database, disk space, cache, queue
- Built-in metrics: memory, request, database
- `HealthStatus` value object with `UP`, `DOWN`, `DEGRADED` states
- `HealthCollection` for aggregating multiple health checks
- `ActuatorMiddleware` with optional access logging
- `ActuatorServiceProvider` with auto-discovery support
- `Actuator` facade
- Comprehensive configuration file
- Helper functions: `format_bytes()`, `format_uptime()`, `human_timestamp()`
- Full test suite with feature and unit tests
- Laravel 10 and 11 support
- PHP 8.1+ support
