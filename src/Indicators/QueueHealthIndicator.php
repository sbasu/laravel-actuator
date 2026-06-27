<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Indicators;

use Sbasu\LaravelActuator\Contracts\HealthIndicator;
use Sbasu\LaravelActuator\HealthStatus;
use Illuminate\Support\Facades\Queue;
use Throwable;

class QueueHealthIndicator implements HealthIndicator
{
    public function name(): string
    {
        return 'queue';
    }

    public function check(): HealthStatus
    {
        try {
            $driver = config('queue.default', 'sync');
            Queue::size();

            return HealthStatus::up()
                ->withDetail('driver', $driver)
                ->withDetail('connected', true);
        } catch (Throwable $e) {
            return HealthStatus::down()
                ->withDetail('driver', config('queue.default', 'unknown'))
                ->withDetail('connected', false)
                ->withDetail('error', $e->getMessage());
        }
    }
}
