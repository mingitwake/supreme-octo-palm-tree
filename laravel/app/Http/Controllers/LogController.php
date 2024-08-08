<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogRequest;
use App\Http\Requests\UpdateLogRequest;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

// curl -i -X GET http://127.0.0.1/api/logs
// curl -i -X POST http://127.0.0.1/api/logs -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X GET http://127.0.0.1/api/logs/{id}
// curl -i -X PUT http://127.0.0.1/api/logs/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X DELETE http://127.0.0.1/api/logs/{id}

class LogController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = Log::all(); // You can change this to paginate in the future
        return response()->json($logs, 200);
    }

    public function store(StoreLogRequest $request): JsonResponse
    {
        try {
            $log = Log::create($request->validated());
            return response()->json(["id" => $log->id, "created_at" => $log->created_at], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id): JsonResponse
    {
        try {
            $log = Log::findOrFail($id);
            return response()->json($log, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Log not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function update(UpdateLogRequest $request, $id): JsonResponse
    {
        try {
            $log = Log::findOrFail($id);
            $log->update($request->validated());
            return response()->json($log, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id): JsonResponse
    {
        try {
            $log = Log::findOrFail($id);
            $log->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Log not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}