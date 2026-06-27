<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Tests\Unit;

use Sbasu\LaravelActuator\HealthStatus;
use Sbasu\LaravelActuator\Tests\TestCase;

class HealthStatusTest extends TestCase
{
    public function test_up_creates_status_with_up_value(): void
    {
        $status = HealthStatus::up();

        $this->assertEquals('UP', $status->getStatus());
        $this->assertTrue($status->isUp());
        $this->assertFalse($status->isDown());
        $this->assertFalse($status->isDegraded());
    }

    public function test_down_creates_status_with_down_value(): void
    {
        $status = HealthStatus::down();

        $this->assertEquals('DOWN', $status->getStatus());
        $this->assertFalse($status->isUp());
        $this->assertTrue($status->isDown());
        $this->assertFalse($status->isDegraded());
    }

    public function test_degraded_creates_status_with_degraded_value(): void
    {
        $status = HealthStatus::degraded();

        $this->assertEquals('DEGRADED', $status->getStatus());
        $this->assertFalse($status->isUp());
        $this->assertFalse($status->isDown());
        $this->assertTrue($status->isDegraded());
    }

    public function test_with_detail_adds_detail_and_returns_self(): void
    {
        $status = HealthStatus::up()
            ->withDetail('database', 'connected')
            ->withDetail('version', '8.0');

        $details = $status->getDetails();

        $this->assertArrayHasKey('database', $details);
        $this->assertArrayHasKey('version', $details);
        $this->assertEquals('connected', $details['database']);
        $this->assertEquals('8.0', $details['version']);
    }

    public function test_to_array_includes_status_and_timestamp(): void
    {
        $status = HealthStatus::up();
        $array  = $status->toArray();

        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('timestamp', $array);
        $this->assertEquals('UP', $array['status']);
    }

    public function test_to_array_includes_details_when_present(): void
    {
        $status = HealthStatus::up()
            ->withDetail('key', 'value');

        $array = $status->toArray();

        $this->assertArrayHasKey('details', $array);
        $this->assertEquals('value', $array['details']['key']);
    }

    public function test_to_array_excludes_details_when_empty(): void
    {
        $status = HealthStatus::up();
        $array  = $status->toArray();

        $this->assertArrayNotHasKey('details', $array);
    }

    public function test_timestamp_is_iso8601_format(): void
    {
        $status    = HealthStatus::up();
        $timestamp = $status->getTimestamp();

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/',
            $timestamp
        );
    }

    public function test_details_can_hold_mixed_values(): void
    {
        $status = HealthStatus::down()
            ->withDetail('string', 'value')
            ->withDetail('int', 42)
            ->withDetail('float', 3.14)
            ->withDetail('bool', true)
            ->withDetail('array', ['nested' => 'data'])
            ->withDetail('null', null);

        $details = $status->getDetails();

        $this->assertEquals('value', $details['string']);
        $this->assertEquals(42, $details['int']);
        $this->assertEquals(3.14, $details['float']);
        $this->assertTrue($details['bool']);
        $this->assertEquals(['nested' => 'data'], $details['array']);
        $this->assertNull($details['null']);
    }
}
