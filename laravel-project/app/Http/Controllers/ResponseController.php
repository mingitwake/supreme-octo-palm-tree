<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResponseRequest;
use App\Http\Requests\UpdateResponseRequest;
use App\Models\Response;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ResponseController extends Controller
{
    public function index(): JsonResponse
    {
        $responses = Response::all(); // You can change this to paginate in the future
        return response()->json($responses, 200);
    }
    // curl -i -X GET http://127.0.0.1:8080/api/responses

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
    // curl -i -X POST http://127.0.0.1:8080/api/responses -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"

    public function show($id): JsonResponse
    {
        try {
            $response = Response::findOrFail($id);
            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Response not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X GET http://127.0.0.1:8080/api/responses/{id}

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
    // curl -i -X PUT http://127.0.0.1:8080/api/responses/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"

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
    // curl -X DELETE http://127.0.0.1:8080/api/responses/{id}
}
