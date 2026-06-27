<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class EnvController extends Controller
{
    private const SENSITIVE_PATTERNS = [
        'PASSWORD',
        'KEY',
        'SECRET',
        'TOKEN',
        'PRIVATE',
        'CREDENTIAL',
        'AUTH',
        'PASS',
        'PWD',
    ];

    public function index(): JsonResponse
    {
        if (!config('actuator.show_env', false)) {
            return response()->json([
                'message' => 'Environment endpoint is disabled. Set actuator.show_env to true to enable.',
            ], 403);
        }

        $env     = $_ENV + $_SERVER;
        $filtered = [];

        foreach ($env as $key => $value) {
            if ($this->isSensitive($key)) {
                $filtered[$key] = '******';
            } else {
                $filtered[$key] = $value;
            }
        }

        ksort($filtered);

        return response()->json([
            'activeProfiles' => [config('app.env', 'local')],
            'propertySources' => [
                [
                    'name'       => 'systemEnvironment',
                    'properties' => $filtered,
                ],
            ],
        ]);
    }

    private function isSensitive(string $key): bool
    {
        $upperKey = strtoupper($key);

        foreach (self::SENSITIVE_PATTERNS as $pattern) {
            if (str_contains($upperKey, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
