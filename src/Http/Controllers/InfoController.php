<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Http\Controllers;

use Sbasu\LaravelActuator\Actuator;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class InfoController extends Controller
{
    public function __construct(private readonly Actuator $actuator) {}

    public function index(): JsonResponse
    {
        return response()->json($this->actuator->getInfo());
    }
}
