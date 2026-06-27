<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Collections;

use Countable;
use Iterator;
use Sbasu\LaravelActuator\HealthStatus;

class HealthCollection implements Countable, Iterator
{
    private array $items = [];
    private array $names = [];
    private int $position = 0;

    public function add(string $name, HealthStatus $status): void
    {
        $this->names[]  = $name;
        $this->items[]  = $status;
    }

    public function all(): array
    {
        return array_combine($this->names, $this->items);
    }

    public function find(string $name): ?HealthStatus
    {
        $index = array_search($name, $this->names, true);

        return $index !== false ? $this->items[$index] : null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function hasDown(): bool
    {
        foreach ($this->items as $status) {
            if ($status->isDown()) {
                return true;
            }
        }

        return false;
    }

    public function hasDegraded(): bool
    {
        foreach ($this->items as $status) {
            if ($status->isDegraded()) {
                return true;
            }
        }

        return false;
    }

    public function current(): HealthStatus
    {
        return $this->items[$this->position];
    }

    public function key(): string
    {
        return $this->names[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }
}
