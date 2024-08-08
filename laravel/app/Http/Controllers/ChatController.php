<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

// curl -i -X GET http://127.0.0.1/api/chats
// curl -i -X POST http://127.0.0.1/api/chats -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X GET http://127.0.0.1/api/chats/{id}
// curl -i -X PUT http://127.0.0.1/api/chats/{id} -H "Content-Type: application/json" --json "{\"log_id\": \"\", \"content\": \"\", \"role\": \"user\", \"status\": \"active\"}"
// curl -X DELETE http://127.0.0.1/api/chats/{id}

class ChatController extends Controller
{
    public function index(): JsonResponse
    {
        $chats = Chat::all(); // You can change this to paginate in the future
        return response()->json($chats, 200);
    }

    public function store(StoreChatRequest $request): JsonResponse
    {
        try {
            $chat = Chat::create($request->validated());
            return response()->json(["id" => $chat->id, "created_at" => $chat->created_at], 201);
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
            $chat = Chat::findOrFail($id);
            return response()->json($chat, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Chat not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function update(UpdateChatRequest $request, $id): JsonResponse
    {
        try {
            $chat = Chat::findOrFail($id);
            $chat->update($request->validated());
            return response()->json($chat, 200);
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
            $chat = Chat::findOrFail($id);
            $chat->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Chat not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

