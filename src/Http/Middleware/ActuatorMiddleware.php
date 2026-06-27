<?php

declare(strict_types=1);

namespace Sbasu\LaravelActuator\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ActuatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('actuator.log_access', false)) {
            Log::info('Actuator endpoint accessed', [
                'path'   => $request->path(),
                'method' => $request->method(),
                'ip'     => $request->ip(),
                'agent'  => $request->userAgent(),
            ]);
        }

        return $next($request);
    }
}
