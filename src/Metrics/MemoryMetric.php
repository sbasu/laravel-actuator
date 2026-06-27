<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Metrics;

use Sbasu\LaravelActuator\Contracts\Metric;

class MemoryMetric implements Metric
{
    public function name(): string
    {
        return 'memory';
    }

    public function collect(): array
    {
        $usageBytes    = memory_get_usage(true);
        $peakBytes     = memory_get_peak_usage(true);
        $limitString   = ini_get('memory_limit');
        $limitBytes    = $this->parseMemoryLimit($limitString);

        $usagePercent = $limitBytes > 0
            ? round(($usageBytes / $limitBytes) * 100, 2)
            : null;

        return [
            'name'         => $this->name(),
            'measurements' => [
                [
                    'statistic' => 'usage_bytes',
                    'value'     => $usageBytes,
                    'human'     => format_bytes($usageBytes),
                ],
                [
                    'statistic' => 'peak_bytes',
                    'value'     => $peakBytes,
                    'human'     => format_bytes($peakBytes),
                ],
                [
                    'statistic' => 'limit_bytes',
                    'value'     => $limitBytes,
                    'human'     => $limitBytes > 0 ? format_bytes($limitBytes) : 'unlimited',
                ],
                [
                    'statistic' => 'usage_percent',
                    'value'     => $usagePercent,
                ],
            ],
            'base_unit' => 'bytes',
        ];
    }

    private function parseMemoryLimit(string|false $limit): int
    {
        if ($limit === false || $limit === '-1' || $limit === '') {
            return 0;
        }

        $limit = trim($limit);
        $last  = strtolower($limit[-1]);
        $value = (int) $limit;

        return match ($last) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
