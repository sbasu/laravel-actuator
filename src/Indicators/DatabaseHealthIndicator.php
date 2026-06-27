<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Indicators;

use Sbasu\LaravelActuator\Contracts\HealthIndicator;
use Sbasu\LaravelActuator\HealthStatus;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseHealthIndicator implements HealthIndicator
{
    public function name(): string
    {
        return 'database';
    }

    public function check(): HealthStatus
    {
        try {
            $connection = DB::connection();
            $connection->getPdo();

            $config = $connection->getConfig();

            return HealthStatus::up()
                ->withDetail('database', $config['database'] ?? 'unknown')
                ->withDetail('driver', $config['driver'] ?? 'unknown')
                ->withDetail('host', $config['host'] ?? 'localhost')
                ->withDetail('connection', 'connected');
        } catch (Throwable $e) {
            return HealthStatus::down()
                ->withDetail('error', $e->getMessage())
                ->withDetail('connection', 'failed');
        }
    }
}
