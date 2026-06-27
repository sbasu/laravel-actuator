<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Tests;

use Sbasu\LaravelActuator\ActuatorServiceProvider;
use Sbasu\LaravelActuator\Facades\Actuator;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ActuatorServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Actuator' => Actuator::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('app.name', 'Test Application');
        $app['config']->set('app.env', 'testing');
        $app['config']->set('app.debug', true);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('cache.default', 'array');

        $app['config']->set('queue.default', 'sync');

        $app['config']->set('actuator.indicators.database', true);
        $app['config']->set('actuator.indicators.disk_space', true);
        $app['config']->set('actuator.indicators.cache', true);
        $app['config']->set('actuator.indicators.queue', true);
        $app['config']->set('actuator.metrics.enabled', true);
        $app['config']->set('actuator.show_details', true);
        $app['config']->set('actuator.show_env', false);
        $app['config']->set('actuator.log_access', false);
    }
}
