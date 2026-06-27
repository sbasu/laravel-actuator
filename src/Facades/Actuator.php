<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array checkHealth()
 * @method static array collectMetrics()
 * @method static array|null getMetric(string $name)
 * @method static array getAllMetrics()
 * @method static array getInfo()
 * @method static array getAvailableMetricNames()
 * @method static void registerHealthIndicator(string $class)
 * @method static void registerMetric(string $class)
 *
 * @see \Sbasu\LaravelActuator\Actuator
 */
class Actuator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sbasu\LaravelActuator\Actuator::class;
    }
}
