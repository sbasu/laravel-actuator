<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Tests\Feature;

use Sbasu\LaravelActuator\Tests\TestCase;

class InfoEndpointTest extends TestCase
{
    public function test_info_endpoint_returns_app_info(): void
    {
        $response = $this->getJson('/actuator/info');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'app' => ['name'],
                'actuator' => ['version', 'package'],
            ]);
    }

    public function test_info_endpoint_returns_correct_app_name(): void
    {
        config(['app.name' => 'My Test App']);

        $response = $this->getJson('/actuator/info');

        $response->assertJsonPath('app.name', 'My Test App');
    }

    public function test_info_endpoint_exposes_environment_in_non_production(): void
    {
        config(['app.env' => 'testing']);

        $response = $this->getJson('/actuator/info');

        $response->assertJsonPath('app.environment', 'testing');
    }

    public function test_info_endpoint_hides_environment_in_production(): void
    {
        config(['app.env' => 'production']);

        $response = $this->getJson('/actuator/info');

        $data = $response->json();
        $this->assertArrayNotHasKey('environment', $data['app']);
    }

    public function test_info_endpoint_includes_actuator_package_info(): void
    {
        $response = $this->getJson('/actuator/info');

        $response->assertJsonPath('actuator.package', 'sbasu/laravel-actuator');
    }
}
