<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnswerRequest;
use App\Http\Requests\UpdateAnswerRequest;
use App\Models\Answer;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class AnswerController extends Controller
{
    public function index(): JsonResponse
    {
        $answers = Answer::all(); // You can change this to paginate in the future
        return response()->json($answers, 200);
    }
    // curl -i -X GET http://127.0.0.1:8080/api/answers

    public function store(StoreAnswerRequest $request): JsonResponse
    {
        try {
            $answer = Answer::create($request->validated());
            return response()->json($answer, 201);
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
    // curl -i -X POST http://127.0.0.1:8080/api/answers -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"

    public function show($id): JsonResponse
    {
        try {
            $answer = Answer::findOrFail($id);
            return response()->json($answer, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Answer not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X GET http://127.0.0.1:8080/api/answers/{id}

    public function update(UpdateAnswerRequest $request, $id): JsonResponse
    {
        try {
            $answer = Answer::findOrFail($id);
            $answer->update($request->validated());
            return response()->json($answer, 200);
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
    // curl -i -X PUT http://127.0.0.1:8080/api/answers/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"

    public function destroy($id): JsonResponse
    {
        try {
            $answer = Answer::findOrFail($id);
            $answer->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Answer not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X DELETE http://127.0.0.1:8080/api/answers/{id}
}
