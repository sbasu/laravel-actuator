<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator;

class HealthStatus
{
    private string $status;
    private string $timestamp;
    private array $details = [];

    private function __construct(string $status)
    {
        $this->status = $status;
        $this->timestamp = gmdate('Y-m-d\TH:i:s\Z');
    }

    public static function up(): static
    {
        return new static('UP');
    }

    public static function down(): static
    {
        return new static('DOWN');
    }

    public static function degraded(): static
    {
        return new static('DEGRADED');
    }

    public function withDetail(string $key, mixed $value): static
    {
        $this->details[$key] = $value;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function isUp(): bool
    {
        return $this->status === 'UP';
    }

    public function isDown(): bool
    {
        return $this->status === 'DOWN';
    }

    public function isDegraded(): bool
    {
        return $this->status === 'DEGRADED';
    }

    public function toArray(): array
    {
        $result = [
            'status'    => $this->status,
            'timestamp' => $this->timestamp,
        ];

        if (!empty($this->details)) {
            $result['details'] = $this->details;
        }

        return $result;
    }
}
