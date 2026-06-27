<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Http\Controllers;

use Sbasu\LaravelActuator\Actuator;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class MetricsController extends Controller
{
    public function __construct(private readonly Actuator $actuator) {}

    public function index(): JsonResponse
    {
        $names = $this->actuator->getAvailableMetricNames();

        return response()->json([
            'names' => array_map(
                fn (string $name) => 'actuator.' . $name,
                $names
            ),
        ]);
    }

    public function show(string $metric): JsonResponse
    {
        $data = $this->actuator->getMetric($metric);

        if ($data === null) {
            return response()->json([
                'error'   => 'Metric not found',
                'message' => "No metric with name '{$metric}' is registered.",
            ], 404);
        }

        return response()->json($data);
    }
}
