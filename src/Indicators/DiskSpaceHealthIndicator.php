<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Indicators;

use Sbasu\LaravelActuator\Contracts\HealthIndicator;
use Sbasu\LaravelActuator\HealthStatus;
use Throwable;

class DiskSpaceHealthIndicator implements HealthIndicator
{
    private string $path;
    private float $threshold;

    public function __construct(string $path = '/', float $threshold = 85.0)
    {
        $this->path      = $path;
        $this->threshold = $threshold;
    }

    public function name(): string
    {
        return 'disk_space';
    }

    public function check(): HealthStatus
    {
        try {
            $free  = disk_free_space($this->path);
            $total = disk_total_space($this->path);

            if ($free === false || $total === false || $total === 0.0) {
                return HealthStatus::down()
                    ->withDetail('error', 'Unable to determine disk space');
            }

            $used            = $total - $free;
            $percentageUsed  = round(($used / $total) * 100, 2);

            $status = $percentageUsed >= $this->threshold
                ? HealthStatus::down()
                : HealthStatus::up();

            return $status
                ->withDetail('free_bytes', (int) $free)
                ->withDetail('total_bytes', (int) $total)
                ->withDetail('used_bytes', (int) $used)
                ->withDetail('percentage_used', $percentageUsed)
                ->withDetail('free', format_bytes((int) $free))
                ->withDetail('total', format_bytes((int) $total))
                ->withDetail('path', $this->path);
        } catch (Throwable $e) {
            return HealthStatus::down()
                ->withDetail('error', $e->getMessage());
        }
    }
}
