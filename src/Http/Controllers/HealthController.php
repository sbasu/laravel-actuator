<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Http\Controllers;

use Sbasu\LaravelActuator\Actuator;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class HealthController extends Controller
{
    public function __construct(private readonly Actuator $actuator) {}

    public function index(): JsonResponse
    {
        $health = $this->actuator->checkHealth();

        if (!config('actuator.show_details', true)) {
            $health = [
                'status'    => $health['status'],
                'timestamp' => $health['timestamp'],
            ];
        }

        $statusCode = $health['status'] === 'UP' ? 200 : 503;

        return response()->json($health, $statusCode);
    }
}
