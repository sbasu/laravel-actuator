<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Tests\Unit;

use Sbasu\LaravelActuator\Indicators\DatabaseHealthIndicator;
use Sbasu\LaravelActuator\Tests\TestCase;

class DatabaseIndicatorTest extends TestCase
{
    public function test_indicator_name_is_database(): void
    {
        $indicator = new DatabaseHealthIndicator();

        $this->assertEquals('database', $indicator->name());
    }

    public function test_indicator_returns_up_status_with_valid_connection(): void
    {
        $indicator = new DatabaseHealthIndicator();
        $status    = $indicator->check();

        $this->assertTrue($status->isUp());
    }

    public function test_indicator_includes_connection_details(): void
    {
        $indicator = new DatabaseHealthIndicator();
        $status    = $indicator->check();

        $details = $status->getDetails();

        $this->assertArrayHasKey('connection', $details);
        $this->assertEquals('connected', $details['connection']);
    }

    public function test_indicator_includes_driver_details(): void
    {
        $indicator = new DatabaseHealthIndicator();
        $status    = $indicator->check();

        $details = $status->getDetails();

        $this->assertArrayHasKey('driver', $details);
        $this->assertEquals('sqlite', $details['driver']);
    }

    public function test_indicator_to_array_is_serializable(): void
    {
        $indicator = new DatabaseHealthIndicator();
        $status    = $indicator->check();
        $array     = $status->toArray();

        $json = json_encode($array);
        $this->assertJson($json);
    }
}
