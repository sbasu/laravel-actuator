<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Tests\Feature;

use Sbasu\LaravelActuator\Tests\TestCase;

class MetricsEndpointTest extends TestCase
{
    public function test_metrics_index_returns_available_metric_names(): void
    {
        $response = $this->getJson('/actuator/metrics');

        $response->assertStatus(200)
            ->assertJsonStructure(['names']);
    }

    public function test_metrics_index_includes_registered_metrics(): void
    {
        $response = $this->getJson('/actuator/metrics');

        $names = $response->json('names');
        $this->assertIsArray($names);
        $this->assertNotEmpty($names);
    }

    public function test_memory_metric_endpoint_returns_correct_structure(): void
    {
        $response = $this->getJson('/actuator/metrics/memory');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'measurements',
                'base_unit',
            ]);
    }

    public function test_request_metric_endpoint_returns_correct_structure(): void
    {
        $response = $this->getJson('/actuator/metrics/request');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'measurements',
                'base_unit',
            ]);
    }

    public function test_unknown_metric_returns_404(): void
    {
        $response = $this->getJson('/actuator/metrics/nonexistent-metric');

        $response->assertStatus(404)
            ->assertJsonStructure(['error', 'message']);
    }

    public function test_database_metric_returns_driver_info(): void
    {
        $response = $this->getJson('/actuator/metrics/database');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertArrayHasKey('measurements', $data);
    }
}
