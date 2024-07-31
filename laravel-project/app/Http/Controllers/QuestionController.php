<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function index(): JsonResponse
    {
        $questions = Question::all(); // You can change this to paginate in the future
        return response()->json($questions, 200);
    }
    // curl -i -X GET http://127.0.0.1:8080/api/questions

    public function store(StoreQuestionRequest $request): JsonResponse
    {
        try {
            $question = Question::create($request->validated());
            return response()->json($question, 201);
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
    // curl -i -X POST http://127.0.0.1:8080/api/questions -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"

    public function show($id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
            return response()->json($question, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Question not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X GET http://127.0.0.1:8080/api/questions/{id}

    public function update(UpdateQuestionRequest $request, $id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
            $question->update($request->validated());
            return response()->json($question, 200);
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
    // curl -i -X PUT http://127.0.0.1:8080/api/questions/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"

    public function destroy($id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
            $question->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Question not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X DELETE http://127.0.0.1:8080/api/questions/{id}
}
