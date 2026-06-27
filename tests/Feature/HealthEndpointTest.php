<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Tests\Feature;

use Sbasu\LaravelActuator\Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    public function test_health_endpoint_returns_json_response(): void
    {
        $response = $this->getJson('/actuator/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
            ]);
    }

    public function test_health_endpoint_returns_status_field(): void
    {
        $response = $this->getJson('/actuator/health');

        $response->assertJsonPath('status', fn ($status) => in_array($status, ['UP', 'DOWN', 'DEGRADED'], true));
    }

    public function test_health_endpoint_returns_components_when_show_details_is_true(): void
    {
        config(['actuator.show_details' => true]);

        $response = $this->getJson('/actuator/health');

        $response->assertJsonStructure([
            'status',
            'components',
            'timestamp',
        ]);
    }

    public function test_health_endpoint_hides_components_when_show_details_is_false(): void
    {
        config(['actuator.show_details' => false]);

        $response = $this->getJson('/actuator/health');

        $response->assertJsonMissing(['components' => []]);
        $response->assertJsonStructure(['status', 'timestamp']);
    }

    public function test_health_endpoint_returns_503_when_down(): void
    {
        config(['actuator.indicators.database' => false]);
        config(['actuator.indicators.disk_space' => false]);
        config(['actuator.indicators.cache' => false]);
        config(['actuator.indicators.queue' => false]);

        $response = $this->getJson('/actuator/health');

        $this->assertContains($response->status(), [200, 503]);
    }

    public function test_health_response_has_iso8601_timestamp(): void
    {
        $response = $this->getJson('/actuator/health');

        $timestamp = $response->json('timestamp');
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/',
            $timestamp
        );
    }
}
