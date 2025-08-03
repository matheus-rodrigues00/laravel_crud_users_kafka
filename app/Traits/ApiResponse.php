<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
 
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message
        ], $code);
    }

 
    protected function errorResponse(string $message = 'Error', int $code = 400, $error = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($error) {
            $response['error'] = $error;
        }

        return response()->json($response, $code);
    }

 
    protected function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], 422);
    }

 
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

 
    protected function serverErrorResponse(string $message = 'Internal server error', $error = null): JsonResponse
    {
        return $this->errorResponse($message, 500, $error);
    }
} 