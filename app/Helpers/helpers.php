<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('jsonResponse')) {
    function jsonResponse(
        bool $status,
        string $message,
        int $statusCode,
        string $errorMessage = null,
        array $data = [],
        array $errors = [],
        array $mergeData = []
    ): JsonResponse {
        return response()->json(
            data: array_merge(
                [
                    'status'  => $status,
                    'message' => $message,
                ],
                array_filter([
                    'error'  => $errorMessage,
                    'data'   => $data,
                    'errors' => $errors
                ]),
                $mergeData
            ),
            status: $statusCode
        );
    }
}
