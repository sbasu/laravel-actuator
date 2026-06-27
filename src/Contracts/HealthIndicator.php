<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Contracts;

use Sbasu\LaravelActuator\HealthStatus;

interface HealthIndicator
{
    public function check(): HealthStatus;

    public function name(): string;
}
