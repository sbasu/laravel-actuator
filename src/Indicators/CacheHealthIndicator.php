<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Indicators;

use Sbasu\LaravelActuator\Contracts\HealthIndicator;
use Sbasu\LaravelActuator\HealthStatus;
use Illuminate\Support\Facades\Cache;
use Throwable;

class CacheHealthIndicator implements HealthIndicator
{
    public function name(): string
    {
        return 'cache';
    }

    public function check(): HealthStatus
    {
        try {
            $driver = config('cache.default', 'file');

            Cache::put('actuator_test', 'test', 1);
            $value = Cache::get('actuator_test');

            $accessible = $value === 'test';

            $status = $accessible ? HealthStatus::up() : HealthStatus::down();

            return $status
                ->withDetail('driver', $driver)
                ->withDetail('accessible', $accessible);
        } catch (Throwable $e) {
            return HealthStatus::down()
                ->withDetail('driver', config('cache.default', 'unknown'))
                ->withDetail('accessible', false)
                ->withDetail('error', $e->getMessage());
        }
    }
}
