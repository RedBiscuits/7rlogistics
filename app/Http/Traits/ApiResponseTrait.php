<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a successful JSON response
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data = null, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error JSON response
     *
     * @param string $error
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function errorResponse(string $error, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'data' => null,
        ], $statusCode);
    }
}






