<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);

        if (!$response instanceof JsonResponse) {
            $response = response()->json($response->content(),
                $response->status(),
                $response->headers->all());
        }

        // Преобразуем 404 ошибки в JSON
        if ($response->isNotFound()) {
            return response()->json([
                'error'   => 'Not found',
                'message' => 'The requested resource was not found',
            ], 404);
        }

        return $response;
    }
}
