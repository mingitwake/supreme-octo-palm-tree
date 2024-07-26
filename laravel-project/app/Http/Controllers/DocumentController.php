<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    public function index(): JsonResponse
    {
        $documents = Document::all(); // Change to paginate in the future
        return response()->json($documents, 200);
    }

    public function store(DocumentRequest $request): JsonResponse
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

    public function update(DocumentRequest $request, $id): JsonResponse
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
}
