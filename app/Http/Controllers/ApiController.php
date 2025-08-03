<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    use ApiResponse;
     
    public function health(): JsonResponse
    {
        try {
            return $this->successResponse(['status' => 'ok', 'timestamp' => now()->toISOString()], 'Service is healthy');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Health check failed', config('app.debug') ? $e->getMessage() : 'An error occurred during health check');
        }
    }

 
    public function external(Request $request): JsonResponse
    {
        try { 
            $response = Http::timeout(10)->get('http://node-microservice:3000/external');
            
            if ($response->successful()) {
                return $this->successResponse($response->json(), 'Data retrieved from external microservice');
            } else {
                return $this->errorResponse('External service returned an error', 502, 'Status code: ' . $response->status());
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to connect to external microservice', 503, $e->getMessage());
        }
    }
} 