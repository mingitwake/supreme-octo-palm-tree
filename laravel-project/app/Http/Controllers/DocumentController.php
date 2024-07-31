<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    public function index(): JsonResponse
    {
        $documents = Document::all(); // You can change this to paginate in the future
        return response()->json($documents, 200);
    }
    // curl -i -X GET http://127.0.0.1:8080/api/documents

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        try {
            $document = Document::create($request->validated());
            return response()->json($document, 201);
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
    // curl -i -X POST http://127.0.0.1:8080/api/documents -H "Content-Type: application/json" --json "{\"collection_id\": \"\", \"url\": \"\", \"status\": \"active\"}"

    public function show($id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            return response()->json($document, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Document not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X GET http://127.0.0.1:8080/api/documents/{id}

    public function update(UpdateDocumentRequest $request, $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            $document->update($request->validated());
            return response()->json($document, 200);
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
    // curl -i -X PUT http://127.0.0.1:8080/api/documents/{id} -H "Content-Type: application/json" --json "{\"collection_id\": \"\", \"url\": \"\", \"status\": \"active\"}"

    public function destroy($id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            $document->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Document not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    // curl -X DELETE http://127.0.0.1:8080/api/documents/{id}
}
