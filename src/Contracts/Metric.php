<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Contracts;

interface Metric
{
    public function collect(): array;

    public function name(): string;
}
