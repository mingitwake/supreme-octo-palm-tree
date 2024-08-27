<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreResponseRequest;
use App\Http\Requests\UpdateResponseRequest;
use App\Models\Response;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

// curl -i -X GET http://127.0.0.1/api/responses
// curl -i -X POST http://127.0.0.1/api/responses -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X GET http://127.0.0.1/api/responses/{id}
// curl -i -X PUT http://127.0.0.1/api/responses/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X DELETE http://127.0.0.1/api/responses/{id}

class ResponseController extends Controller
{
    public function index(Request $request)//: JsonResponse
    {
        $query = Response::query();

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

    public function store(StoreResponseRequest $request): JsonResponse
    {
        try {
            $response = Response::create($request->validated());
            return response()->json($response, 201);
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
            $response = Response::with(['answers.question.tableColumns', 'answers.selectedCheckboxes.checkboxOption', 'answers.tableRows'])
            ->withTrashed()->findOrFail($id);
            if ($response->trashed()) {
                return response()->json([
                    'message' => 'Response Deleted',
                ], 404);
            }
            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Response not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function update(UpdateResponseRequest $request, $id): JsonResponse
    {
        try {
            $response = Response::findOrFail($id);
            $response->update($request->validated());
            return response()->json($response, 200);
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
            $response = Response::findOrFail($id);
            $response->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Response not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

