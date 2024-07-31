<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class CollectionController extends Controller
{
    public function index(): JsonResponse
    {
        $collections = Collection::all(); // You can change this to paginate in the future
        return response()->json($collections, 200);
    }
    // curl -i -X GET http://127.0.0.1:8080/api/collections

    public function store(CollectionRequest $request): JsonResponse
    {
        try {
            $collection = Collection::create($request->validated());
            return response()->json($collection, 201);
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
    // curl -i -X POST http://127.0.0.1:8080/api/collections -H "Content-Type: application/json" --json "{\"name\": \"Admin\", \"status\": \"active\"}"

    public function show($id): JsonResponse
    {
        try {
            $collection = Collection::findOrFail($id);
            return response()->json($collection, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Collection not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X GET http://127.0.0.1:8080/api/collections/{id}

    public function update(CollectionRequest $request, $id): JsonResponse
    {
        try {
            $collection = Collection::findOrFail($id);
            $collection->update($request->validated());
            return response()->json($collection, 200);
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
    // curl -i -X PUT http://127.0.0.1:8080/api/collections/{id} -H "Content-Type: application/json" --json "{\"name\": \"Admin\", \"status\": \"active\"}"

    public function destroy($id): JsonResponse
    {
        try {
            $collection = Collection::findOrFail($id);
            $collection->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Collection not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X DELETE http://127.0.0.1:8080/api/collections/{id}
}
