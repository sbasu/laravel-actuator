<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator;

use Sbasu\LaravelActuator\Collections\HealthCollection;
use Sbasu\LaravelActuator\Contracts\HealthIndicator;
use Sbasu\LaravelActuator\Contracts\Metric;
use Illuminate\Contracts\Container\Container;
use Throwable;

class Actuator
{
    private array $healthIndicators = [];
    private array $metrics          = [];

    public function __construct(private readonly Container $container) {}

    public function registerHealthIndicator(string $class): void
    {
        $this->healthIndicators[] = $class;
    }

    public function registerMetric(string $class): void
    {
        $this->metrics[] = $class;
    }

    public function getHealthIndicators(): array
    {
        return $this->healthIndicators;
    }

    public function checkHealth(): array
    {
        $collection = new HealthCollection();

        foreach ($this->healthIndicators as $indicatorClass) {
            try {
                /** @var HealthIndicator $indicator */
                $indicator = $this->container->make($indicatorClass);
                $status    = $indicator->check();
                $collection->add($indicator->name(), $status);
            } catch (Throwable $e) {
                $status = HealthStatus::down()
                    ->withDetail('error', $e->getMessage())
                    ->withDetail('indicator', $indicatorClass);
                $collection->add(class_basename($indicatorClass), $status);
            }
        }

        $overallStatus = 'UP';

        if ($collection->hasDown()) {
            $overallStatus = 'DOWN';
        } elseif ($collection->hasDegraded()) {
            $overallStatus = 'DEGRADED';
        }

        $components = [];

        foreach ($collection as $name => $status) {
            $components[$name] = $status->toArray();
        }

        return [
            'status'     => $overallStatus,
            'components' => $components,
            'timestamp'  => human_timestamp(),
        ];
    }

    public function collectMetrics(): array
    {
        $results = [];

        foreach ($this->metrics as $metricClass) {
            try {
                /** @var Metric $metric */
                $metric           = $this->container->make($metricClass);
                $results[$metric->name()] = $metric->collect();
            } catch (Throwable $e) {
                $results[class_basename($metricClass)] = [
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function getMetric(string $name): ?array
    {
        foreach ($this->metrics as $metricClass) {
            try {
                /** @var Metric $metric */
                $metric = $this->container->make($metricClass);

                if ($metric->name() === $name) {
                    return $metric->collect();
                }
            } catch (Throwable) {
                continue;
            }
        }

        return null;
    }

    public function getAllMetrics(): array
    {
        return $this->collectMetrics();
    }

    public function getAvailableMetricNames(): array
    {
        $names = [];

        foreach ($this->metrics as $metricClass) {
            try {
                /** @var Metric $metric */
                $metric  = $this->container->make($metricClass);
                $names[] = $metric->name();
            } catch (Throwable) {
                continue;
            }
        }

        return $names;
    }

    public function getInfo(): array
    {
        $info = [
            'app' => [
                'name'        => config('app.name', 'Laravel'),
                'version'     => config('app.version', '1.0.0'),
                'description' => config('app.description', ''),
            ],
            'actuator' => [
                'version' => '1.0.0',
                'package' => 'sbasu/laravel-actuator',
            ],
        ];

        if (config('app.env') !== 'production') {
            $info['app']['environment'] = config('app.env', 'local');
            $info['app']['debug']       = config('app.debug', false);
        }

        return $info;
    }
}
