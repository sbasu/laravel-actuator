<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Tests\Feature;

use Sbasu\LaravelActuator\Tests\TestCase;

class EnvEndpointTest extends TestCase
{
    public function test_env_endpoint_returns_403_when_disabled(): void
    {
        config(['actuator.show_env' => false]);

        $response = $this->getJson('/actuator/env');

        $response->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_env_endpoint_returns_data_when_enabled(): void
    {
        config(['actuator.show_env' => true]);

        $response = $this->getJson('/actuator/env');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'activeProfiles',
                'propertySources',
            ]);
    }

    public function test_env_endpoint_masks_sensitive_variables(): void
    {
        config(['actuator.show_env' => true]);

        $_ENV['APP_SECRET']   = 'super-secret-value';
        $_ENV['DB_PASSWORD']  = 'db-password-value';
        $_ENV['API_KEY']      = 'api-key-value';

        $response = $this->getJson('/actuator/env');

        $response->assertStatus(200);

        $sources     = $response->json('propertySources');
        $properties  = $sources[0]['properties'] ?? [];

        if (isset($properties['APP_SECRET'])) {
            $this->assertEquals('******', $properties['APP_SECRET']);
        }

        if (isset($properties['DB_PASSWORD'])) {
            $this->assertEquals('******', $properties['DB_PASSWORD']);
        }

        if (isset($properties['API_KEY'])) {
            $this->assertEquals('******', $properties['API_KEY']);
        }
    }

    public function test_env_endpoint_includes_active_profiles(): void
    {
        config(['actuator.show_env' => true]);
        config(['app.env' => 'testing']);

        $response = $this->getJson('/actuator/env');

        $response->assertStatus(200);
        $profiles = $response->json('activeProfiles');
        $this->assertContains('testing', $profiles);
    }
}
