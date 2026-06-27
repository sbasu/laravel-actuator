<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Metrics;

use Sbasu\LaravelActuator\Contracts\Metric;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseMetric implements Metric
{
    public function name(): string
    {
        return 'database';
    }

    public function collect(): array
    {
        try {
            $connection = DB::connection();
            $config     = $connection->getConfig();
            $queryLog   = DB::getQueryLog();

            return [
                'name'         => $this->name(),
                'measurements' => [
                    [
                        'statistic' => 'driver',
                        'value'     => $config['driver'] ?? 'unknown',
                    ],
                    [
                        'statistic' => 'host',
                        'value'     => $config['host'] ?? 'localhost',
                    ],
                    [
                        'statistic' => 'database',
                        'value'     => $config['database'] ?? 'unknown',
                    ],
                    [
                        'statistic' => 'query_count',
                        'value'     => count($queryLog),
                    ],
                ],
                'base_unit' => 'queries',
            ];
        } catch (Throwable $e) {
            return [
                'name'         => $this->name(),
                'measurements' => [
                    [
                        'statistic' => 'error',
                        'value'     => $e->getMessage(),
                    ],
                ],
                'base_unit' => 'queries',
            ];
        }
    }
}
