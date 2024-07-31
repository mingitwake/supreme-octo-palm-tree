<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class LogController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = Log::all(); // You can change this to paginate in the future
        return response()->json($logs, 200);
    }
    // curl -i -X GET http://127.0.0.1:8080/api/logs

    public function store(LogRequest $request): JsonResponse
    {
        try {
            $log = Log::create($request->validated());
            return response()->json($log, 201);
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
    // curl -i -X POST http://127.0.0.1:8080/api/logs -H "Content-Type: application/json" --json "{\"title\": \"\", \"status\": \"active\"}"

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
    // curl -X GET http://127.0.0.1:8080/api/logs/{id}

    public function update(LogRequest $request, $id): JsonResponse
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
    // curl -i -X PUT http://127.0.0.1:8080/api/logs/{id} -H "Content-Type: application/json" --json "{\"title\": \"\", \"status\": \"active\"}"

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
    // curl -X DELETE http://127.0.0.1:8080/api/logs/{id}
}
