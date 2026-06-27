<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Tests\Unit;

use Sbasu\LaravelActuator\Indicators\DiskSpaceHealthIndicator;
use Sbasu\LaravelActuator\Tests\TestCase;

class DiskSpaceIndicatorTest extends TestCase
{
    public function test_indicator_name_is_disk_space(): void
    {
        $indicator = new DiskSpaceHealthIndicator();

        $this->assertEquals('disk_space', $indicator->name());
    }

    public function test_indicator_returns_health_status(): void
    {
        $indicator = new DiskSpaceHealthIndicator(sys_get_temp_dir());
        $status    = $indicator->check();

        $this->assertContains($status->getStatus(), ['UP', 'DOWN', 'DEGRADED']);
    }

    public function test_indicator_includes_disk_details(): void
    {
        $indicator = new DiskSpaceHealthIndicator(sys_get_temp_dir());
        $status    = $indicator->check();
        $details   = $status->getDetails();

        $this->assertArrayHasKey('free_bytes', $details);
        $this->assertArrayHasKey('total_bytes', $details);
        $this->assertArrayHasKey('percentage_used', $details);
    }

    public function test_indicator_returns_down_when_threshold_exceeded(): void
    {
        $indicator = new DiskSpaceHealthIndicator(sys_get_temp_dir(), 0.0);
        $status    = $indicator->check();

        $this->assertTrue($status->isDown());
    }

    public function test_indicator_returns_up_when_under_threshold(): void
    {
        $indicator = new DiskSpaceHealthIndicator(sys_get_temp_dir(), 100.0);
        $status    = $indicator->check();

        $this->assertTrue($status->isUp());
    }

    public function test_indicator_includes_human_readable_sizes(): void
    {
        $indicator = new DiskSpaceHealthIndicator(sys_get_temp_dir());
        $status    = $indicator->check();
        $details   = $status->getDetails();

        $this->assertArrayHasKey('free', $details);
        $this->assertArrayHasKey('total', $details);
        $this->assertStringContainsString(' ', $details['free']);
    }

    public function test_percentage_is_between_0_and_100(): void
    {
        $indicator = new DiskSpaceHealthIndicator(sys_get_temp_dir());
        $status    = $indicator->check();
        $details   = $status->getDetails();

        $percentage = $details['percentage_used'];
        $this->assertGreaterThanOrEqual(0, $percentage);
        $this->assertLessThanOrEqual(100, $percentage);
    }
}
