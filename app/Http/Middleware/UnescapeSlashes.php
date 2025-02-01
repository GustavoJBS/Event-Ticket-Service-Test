<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;

class UnescapeSlashes
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions(JSON_UNESCAPED_SLASHES);
        }

        return $response;
    }
}
