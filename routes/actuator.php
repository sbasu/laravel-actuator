<?php

declare(strict_types=1);

use Sbasu\LaravelActuator\Http\Controllers\EnvController;
use Sbasu\LaravelActuator\Http\Controllers\HealthController;
use Sbasu\LaravelActuator\Http\Controllers\InfoController;
use Sbasu\LaravelActuator\Http\Controllers\MetricsController;
use Sbasu\LaravelActuator\Http\Middleware\ActuatorMiddleware;
use Illuminate\Support\Facades\Route;

$prefix     = config('actuator.path', 'actuator');
$middleware = array_merge(
    config('actuator.middleware', ['api']),
    [ActuatorMiddleware::class]
);

Route::prefix($prefix)
    ->middleware($middleware)
    ->group(function () {
        Route::get('health', [HealthController::class, 'index'])
            ->name('actuator.health');

        Route::get('metrics', [MetricsController::class, 'index'])
            ->name('actuator.metrics');

        Route::get('metrics/{metric}', [MetricsController::class, 'show'])
            ->name('actuator.metrics.show');

        Route::get('info', [InfoController::class, 'index'])
            ->name('actuator.info');

        Route::get('env', [EnvController::class, 'index'])
            ->name('actuator.env');
    });
