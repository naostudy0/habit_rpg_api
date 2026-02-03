<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

/**
 * API共通レスポンスのResource
 */
class ApiResponseResource
{
    /**
     * @param mixed $data
     * @param array<string, mixed>|null $extra
     */
    public static function success(
        $data,
        ?string $message = null,
        int $status = 200,
        ?array $extra = null
    ): JsonResponse {
        $payload = [
            'result' => true,
            'data' => $data,
        ];

        if ($message !== null) {
            $payload['message'] = $message;
        }

        if ($extra !== null) {
            $payload = array_merge($payload, $extra);
        }

        return response()->json($payload, $status);
    }

    /**
     * @param array<string, mixed>|null $errors
     */
    public static function error(string $message, int $status = 400, ?array $errors = null): JsonResponse
    {
        $payload = [
            'result' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
