<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator;

use Sbasu\LaravelActuator\Indicators\CacheHealthIndicator;
use Sbasu\LaravelActuator\Indicators\DatabaseHealthIndicator;
use Sbasu\LaravelActuator\Indicators\DiskSpaceHealthIndicator;
use Sbasu\LaravelActuator\Indicators\QueueHealthIndicator;
use Sbasu\LaravelActuator\Metrics\DatabaseMetric;
use Sbasu\LaravelActuator\Metrics\MemoryMetric;
use Sbasu\LaravelActuator\Metrics\RequestMetric;
use Illuminate\Support\ServiceProvider;

class ActuatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/actuator.php',
            'actuator'
        );

        $this->app->singleton(Actuator::class, function ($app) {
            return new Actuator($app);
        });

        $this->app->alias(Actuator::class, 'actuator');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/actuator.php' => config_path('actuator.php'),
            ], 'actuator-config');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/actuator.php');

        $this->registerDefaultIndicators();
        $this->registerDefaultMetrics();
    }

    private function registerDefaultIndicators(): void
    {
        /** @var Actuator $actuator */
        $actuator = $this->app->make(Actuator::class);

        if (config('actuator.indicators.database', true)) {
            $actuator->registerHealthIndicator(DatabaseHealthIndicator::class);
        }

        if (config('actuator.indicators.disk_space', true)) {
            $actuator->registerHealthIndicator(DiskSpaceHealthIndicator::class);
        }

        if (config('actuator.indicators.cache', true)) {
            $actuator->registerHealthIndicator(CacheHealthIndicator::class);
        }

        if (config('actuator.indicators.queue', true)) {
            $actuator->registerHealthIndicator(QueueHealthIndicator::class);
        }
    }

    private function registerDefaultMetrics(): void
    {
        if (!config('actuator.metrics.enabled', true)) {
            return;
        }

        /** @var Actuator $actuator */
        $actuator = $this->app->make(Actuator::class);

        $actuator->registerMetric(MemoryMetric::class);
        $actuator->registerMetric(RequestMetric::class);
        $actuator->registerMetric(DatabaseMetric::class);
    }
}
