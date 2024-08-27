<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogRequest;
use App\Http\Requests\UpdateLogRequest;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

// curl -i -X GET http://127.0.0.1/api/logs
// curl -i -X POST http://127.0.0.1/api/logs -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X GET http://127.0.0.1/api/logs/{id}
// curl -i -X PUT http://127.0.0.1/api/logs/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X DELETE http://127.0.0.1/api/logs/{id}

class LogController extends Controller
{
    public function index(Request $request)//: JsonResponse
    {
        $query = Log::query();

        $validated = $request->validate([
            'search_by' => 'nullable|string',
            'search_value' => 'nullable|string',
            'sort_by' => 'nullable|string',
            'sort_order' => 'nullable|string|in:asc,desc',
        ]);
    
        if ($request->search_by && $request->search_value) {
            $searchBy = $request->search_by;
            $searchValue = $request->search_value;
            $query->where($searchBy, 'LIKE', '%' . $searchValue . '%');
        }
        
        if ($request->sort_by && $request->sort_order) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order;
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    
        return $query->paginate(10);
    }

    public function store(StoreLogRequest $request): JsonResponse
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
    
    public function show($id): JsonResponse
    {
        try {
            $log = Log::with(['chats'])->withTrashed()->findOrFail($id);
            if ($log->trashed()) {
                return response()->json([
                    'message' => 'Log Deleted',
                ], 404);
            }
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