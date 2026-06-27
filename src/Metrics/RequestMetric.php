<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Metrics;

use Sbasu\LaravelActuator\Contracts\Metric;

class RequestMetric implements Metric
{
    public function name(): string
    {
        return 'request';
    }

    public function collect(): array
    {
        $startTime   = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
        $currentTime = microtime(true);
        $durationMs  = round(($currentTime - $startTime) * 1000, 3);

        return [
            'name'         => $this->name(),
            'measurements' => [
                [
                    'statistic' => 'duration_ms',
                    'value'     => $durationMs,
                ],
                [
                    'statistic' => 'start_time',
                    'value'     => $startTime,
                    'human'     => gmdate('Y-m-d\TH:i:s\Z', (int) $startTime),
                ],
                [
                    'statistic' => 'current_time',
                    'value'     => $currentTime,
                    'human'     => gmdate('Y-m-d\TH:i:s\Z', (int) $currentTime),
                ],
            ],
            'base_unit' => 'milliseconds',
        ];
    }
}
